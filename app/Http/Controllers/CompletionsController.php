<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Lecture;

// This will handle User to Lecture completions
class CompletionsController extends Controller
{

    // Check all 1 course's lectures, mark as completed or incomplete
    public function checkIfCourseCompleted($lecture_id)
    {
        // Getting all lectures for 1 course
        $lecture = Lecture::findOrFail($lecture_id);
        $course = $lecture->course()->first();
        $lectures = $course->lectures()->get();

        $isCourseCompleted = 1;
        foreach ($lectures as $lecture) {
            $isLectureDone = 0;
            $isLectureDone =
                current_user()->lectures()->where('lecture_id', $lecture->id)->exists();
            if ($isLectureDone !== true) {
                // If one lecture remains 0 / UnDone => not found in pivot table => isCourseCompleted to be false
                $isCourseCompleted = 0;
            }
        }

        // Course is completed by current user
        if ($isCourseCompleted === 1) {
            // Add completedDate in enrollments / course_user pivot table
            current_user()->courses()->updateExistingPivot($course->id, [
                'completedDate' =>  Carbon::now()
            ]);
        } else {
            // Empty completedDate in enrollments / course_user pivot table
            // Always Empty, maybe course was done, then undone by changing 1 Lecture's isDone
            current_user()->courses()->updateExistingPivot($course->id, [
                'completedDate' => null,
            ]);
        }
    }

    // Mark a lecture as completed by a user
    public function store(Request $request)
    {
        try {
            $request->validate([
                'user_id' => 'required|int',
                'lecture_id' => 'required|int'
            ]);

            // Get User by id:
            $user = User::findOrFail($request->user_id);

            // Adding a Lecture to a User in Pivot table completions lecture_user
            $user->lectures()->attach($request->lecture_id);

            // Check if all course's lectures are done, set course as completed:
            $this->checkIfCourseCompleted($request->lecture_id);

            return response_success(['success' => true]);
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Delete user to lecture completion
    public function destroy($id)
    {
        try {
            // Removing a Lecture / User relation
            current_user()->lectures()->detach($id);

            // Check if all course's lectures are done, set course as completed:
            $this->checkIfCourseCompleted($id);

            return response_success(['success' => true]);
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }
}
