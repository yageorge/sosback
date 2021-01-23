<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;

use App\Models\Department;

class CloudMessagingController extends Controller
{
    // Save user FCM token to user data
    public function saveToken(Request $request)
    {
        try {
            current_user()->update(['messagingToken' => $request->messagingToken]);

            return response()->json(['success' => true], 200);
        } catch (Exception $e) {
            return response_success(['error: ' => $e->getMessage()]);
        }
    }


    public function sendMessage($course, $departmentId)
    {
        // Firebase Cloud Messaging url
        $url = 'https://fcm.googleapis.com/fcm/send';

        // Project server api key - Sensitive Data
        $SERVER_API_KEY = 'AAAAT0nKLAg:APA91bHCVzLbRbFMORO3XsR8668ZlMOJAXyNrHFtFyw_S_xY7wrycOsSnCJXrmoAXqqKlwNly7nrMXiaA0q_hc5kLEzfbq1sOJ6KF_4E39IH1uGfstB8JfoKeeePZke8tBGTOmslTkoU';

        // Get all users in department
        $department = Department::find($departmentId);
        $usersList = $department->users()->get();

        // get all messagingTokens for all users
        $usersMessagingToken = [];
        foreach ($usersList as $user) {
            array_push($usersMessagingToken, $user->messagingToken);
        }

        // Preparing message data
        $data = [
            "registration_ids" => $usersMessagingToken,
            'notification' =>
            [
                'title' => 'A new course is added!',
                'body' => $course->title,
                // 'icon' => url('/logo.png')
            ],
            'data' =>
            [
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'courseId' => $course->id,
                'status' => 'done',
            ],
        ];

        $dataFields = json_encode($data);

        $headers = [
            'Authorization: key=' . $SERVER_API_KEY,
            'Content-Type: application/json'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $dataFields);

        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }
}
