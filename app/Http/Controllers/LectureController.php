<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Validator;

use App\Models\Lecture;

class LectureController extends Controller
{
    // Return all lectures of one course
    public function index(Request $request)
    {
        try {

            //Validate course_id
            $validator = Validator::make($request->all(), [
                'course_id' => 'required|int'
            ]);

            //Validation / On fail - Return error
            if ($validator->fails()) {
                return response()->json(['validatorFailError' => $validator->errors()], 400);
            }

            $course_id = $request->course_id;
            return Lecture::where('course_id', $course_id)->get();
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }
}
