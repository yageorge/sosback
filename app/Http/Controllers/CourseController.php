<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Validator;

use App\Models\Course;

class CourseController extends Controller
{
    // Return all courses of the current user's company
    public function index()
    {
        try {
            //To be improved
            $userDepartment = current_user()->department()->first();
            $userCompany = $userDepartment->company()->first();
            $companyCourses = $userCompany
                ->courses()
                ->select('courses.*', 'categories.name as categoryName')
                ->orderBy('name', 'asc')
                ->get();

            return $companyCourses;
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
}
