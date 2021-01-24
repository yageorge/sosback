<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\User;
use App\Models\Course;

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
    public function destroy($id)
    {
        try {
            // Removing a Course / User relation
            current_user()->courses()->detach($id);

            return response_success(['success' => true]);
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Get count of Enrollments + Count of completed Couses + CompletionsPoints in current Company
    public function count()
    {

        try {
            // Get company courses
            $userDepartment = current_user()->department()->first();
            $userCompany = $userDepartment->company()->first();
            $companyCourses = $userCompany
                ->courses()
                ->get();

            $totalModulesMinutes = 0;
            $totalEnrollments = 0;
            $totalCompletions = 0;
            $totalCompletionsPoints = 0;

            // Count total occurences in enrollment (course_user) pivot table for every company course
            foreach ($companyCourses as $course) {
                // get lectures' course:
                $courseLectures = $course->lectures()->get();
                // count total of minutes for this courses' lectures - if course contains lectures:
                if ($courseLectures !== null) {
                    foreach ($courseLectures as $lecture) {
                        $totalModulesMinutes += $lecture->duration;
                    }
                }

                // get total enrollment for this course for different users
                $courseEnrollments = $course->users()->get();
                $totalEnrollments += $courseEnrollments->count();

                // check if every course enrollment is completed (this is not n-1 => no repetition, this second loop will iterates only unique 1 courses enrollments)
                foreach ($courseEnrollments as $enrollment) {
                    // If completedDate not null => Course entrollment is completed
                    if ($enrollment->pivot->completedDate !== null) {
                        // Add +1 to completed courses
                        $totalCompletions += 1;

                        // Get course's points and add sum
                        $courseId = $enrollment->pivot->course_id;
                        $course = Course::find($courseId);
                        $totalCompletionsPoints += $course->points;
                    }
                }
            }

            // preparing data figures
            $data['totalModulesMinutes'] = $totalModulesMinutes;
            $data['totalEnrollments'] = $totalEnrollments;
            $data['totalCompletions'] = $totalCompletions;
            $data['totalCompletionsPoints'] = $totalCompletionsPoints;

            return $data;
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Returning count of Courses Completions by month (Chart Data)
    public function completionHistory()
    {
        try {
            // Get company courses
            $userDepartment = current_user()->department()->first();
            $userCompany = $userDepartment->company()->first();
            $companyCourses = $userCompany
                ->courses()
                ->get();

            $currentYearCompletions = ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0];
            $previousYearCompletions =
                ['1' => 0, '2' => 0, '3' => 0, '4' => 0, '5' => 0, '6' => 0, '7' => 0, '8' => 0, '9' => 0, '10' => 0, '11' => 0, '12' => 0];

            foreach ($companyCourses as $course) {

                // get 1 course enrollments
                $courseEnrollments = $course->users()->get();

                // check if every course enrollment is completed (this is not n-1 => no repetition, this second loop will iterates only unique 1 courses enrollments)
                foreach ($courseEnrollments as $enrollment) {
                    // If completedDate not null => Course entrollment is completed
                    if ($enrollment->pivot->completedDate !== null) {
                        // TODO work with completedDate to count+ group by month
                        $completedDate = Carbon::parse($enrollment->pivot->completedDate);
                        $monthNumber = $completedDate->isoFormat('OM');

                        // Splitting completion dates to current or previous year array of dates
                        if ($completedDate->isCurrentYear()) {
                            // array_push($currentYearDates, $completedDate->isoFormat('OM'));
                            $currentYearCompletions[$monthNumber] += 1;
                        } else if ($completedDate->isLastYear()) {
                            // array_push($previousYearDates, $completedDate->isoFormat('OM'));
                            $previousYearCompletions[$monthNumber] += 1;
                        }
                    }
                }
            }

            // preparing data figures
            $data['currentYearCompletions'] = $currentYearCompletions;
            $data['previousYearCompletions'] = $previousYearCompletions;

            return $data;
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }
}
