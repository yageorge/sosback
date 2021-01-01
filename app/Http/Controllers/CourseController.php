<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Validator;

use App\Models\Course;
use App\Http\Controllers\EnrollmentsController;

class CourseController extends Controller
{
    // Return all courses of the current user's company (Admin Panel Request)
    public function index()
    {
        try {
            //To be improved
            $userDepartment = current_user()->department()->first();
            $userCompany = $userDepartment->company()->first();
            $companyCourses = $userCompany
                ->courses()
                ->select('courses.*', 'categories.name as categoryName')
                ->with('lectures') // Including all lectures
                ->with('category') // Including category
                ->orderBy('name', 'asc')
                ->get();

            return $companyCourses;
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Return all courses of the current user's department (Mobile user Request)
    public function userCourses()
    {
        try {
            // To be improved
            $userDepartment = current_user()->department()->first();
            $userCourses = $userDepartment
                ->courses()
                ->with('lectures') // Including all lectures
                ->with('category') // Including category
                ->orderBy('created_at', 'desc') // to check
                ->get();

            // Adding isUserEnrolled extra bool field, is user is enrolled to course (if i need this enrolled check in many places i can use https://www.youtube.com/watch?v=FNU3gYgiEgQ&list=UUTuplgOBi6tJIlesIboymGA&ab_channel=LaravelBusiness)
            $userCourses = $userCourses->map(function ($course) {
                $course->isUserEnrolled = (new EnrollmentsController)->isEnrolled($course->id);
                return $course;
            });

            return $userCourses;
            // return (new EnrollmentsController)->isEnrolled(2);
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Creating new Course
    public function store(Request $request)
    {
        try {
            //Validate create Course request params
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:64',
                'description' => 'required|string|max:128',
                'points' => 'int|max:999'
            ]);

            //Validation / On fail - Return error
            if ($validator->fails()) {
                return response()->json(['validatorFailError' => $validator->errors()], 400);
            }

            //Validation / On Success
            $input = $request->all();

            //Create User
            $course = Course::create($input);

            //Return Course info
            $data['title'] = $course->title;
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // Return course by id
    public function edit($id)
    {
        try {
            return Course::findOrFail($id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Updating course
    public function update(Request $request, Course $course)
    {

        try {
            //Validate update Course request params
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:64',
                'description' => 'required|string|max:128',
                'points' => 'int|max:999',
                'category_id' => 'required|int'
            ]);

            //Validation / On fail - Return error
            if ($validator->fails()) {
                return response()->json(['validatorFailError' => $validator->errors()], 400);
            }

            //Validation / On Success
            $input = $request->all();

            //Updating user
            $course->update($input);

            // return a success response
            $data['title'] = $request->title;
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Deleting a Course
    public function destroy($id)
    {

        try {
            $course = Course::findOrFail($id);
            $data['title'] = $course->title;

            $course->delete();

            // return a success response
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Getting only the count of Courses in current Company
    public function count()
    {

        try {

            //To be improved
            $userDepartment = current_user()->department()->first();
            $userCompany = $userDepartment->company()->first();
            $companyCourses = $userCompany
                ->courses()
                ->get();

            return $companyCourses->count();
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }
}
