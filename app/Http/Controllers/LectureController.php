<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Validator;
use DB;

use App\Models\Course;
use App\Models\Lecture;

class LectureController extends Controller
{
    // Return all lectures of one course
    public function index(Request $request)
    {
        try {

            // Validate course_id
            $validator = Validator::make($request->all(), [
                'course_id' => 'required|int'
            ]);

            // Validation / On fail - Return error
            if ($validator->fails()) {
                return response()->json(['validatorFailError' => $validator->errors()], 400);
            }

            $course_id = $request->course_id;
            return Lecture::where('course_id', $course_id)->get();
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }


    // Creating new Lecture
    public function store(Request $request)
    {
        try {
            // Validate create Course request params
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:64',
                'content' => 'string',
                'urlVideo' => 'url',
                'duration' => 'int|max:9999',
                'course_id' => 'required|int'
            ]);

            // Validation / On fail - Return error
            if ($validator->fails()) {
                return response()->json(['validatorFailError' => $validator->errors()], 400);
            }

            // Validation / On Success
            $input = $request->all();

            // Create Lecture
            $lecture = Lecture::create($input);

            // Increasing the related course minutes + lectures
            $course = Course::findOrFail($lecture->course_id);
            $course->increment('totalMinutes', $lecture->duration);
            $course->increment('totalLectures');

            // Return Course info
            $data['title'] = $lecture->title;

            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // Return lecture by id
    public function edit($id)
    {
        try {
            return Lecture::findOrFail($id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Updating lecture
    public function update(Request $request, Lecture $lecture)
    {

        try {
            // Validate update Course request params
            $validator = Validator::make($request->all(), [
                'title' => 'required|string|max:64',
                'content' => 'string',
                'duration' => 'int|max:9999',
                'course_id' => 'required|int'
            ]);

            // Validation / On fail - Return error
            if ($validator->fails()) {
                return response()->json(['validatorFailError' => $validator->errors()], 400);
            }

            // Saving lecture Duration before Edit
            $oldLectureDuration = Lecture::findOrFail($lecture->id)->duration;

            // Validation / On Success
            $input = $request->all();

            // Updating user
            $lecture->update($input);

            // Updating the related course minutes
            $course = Course::findOrFail($lecture->course_id);
            // Deduction old duration + Incrementing new duration
            $course->decrement('totalMinutes', $oldLectureDuration);
            $course->increment('totalMinutes', $lecture->duration);

            // return a success response
            $data['title'] = $request->title;
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }


    // Deleting a Lecture
    public function destroy($id)
    {

        try {
            $lecture = Lecture::findOrFail($id);
            $data['title'] = $lecture->title;

            // Decrement the related course minutes + lectures
            $course = Course::findOrFail($lecture->course_id);
            $course->decrement('totalMinutes', $lecture->duration);
            $course->decrement('totalLectures');


            $lecture->delete();

            // return a success response
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
