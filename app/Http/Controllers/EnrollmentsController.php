<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\User;

// This will handle User to Course enrollments
class EnrollmentsController extends Controller
{
    // Check if user is enrolled to a course
    public function isEnrolled($courseId)
    {
        try {
            $isEnrolled = current_user()->courses()->where('course_id', $courseId)->exists();

            return $isEnrolled;
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Create enrollment, a user to a course
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|int',
                'course_id' => 'required|int'
            ]);

            // Get User by id:
            $user = User::findOrFail($request->user_id);

            // Adding a Course to a User in Pivot table enrollments course_user
            $user->courses()->attach($request->course_id);

            return response_success(['success' => true]);
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Delete user to course Enrollment
    public function destroy(Request $request)
    {
        try {
            // Get User by id:
            $user = User::findOrFail($request->user_id);

            // Removing a Course / User relation
            $user->courses()->detach($request->course_id);

            return response_success(['success' => true]);
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }
}
