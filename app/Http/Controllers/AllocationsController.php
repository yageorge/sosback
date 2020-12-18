<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Course;

// Handling Courses to Departments Allocations
class AllocationsController extends Controller
{

    // Returning all courses for one Department id
    public function index($departmentId)
    {
        $department = Department::findOrFail($departmentId);
        return $department->courses()->get();
    }

    // Returning all Courses Un-Allocated to 1 department ID
    public function testIndex($departmentId)
    {
        $courses = Course::whereDoesntHave('departments', function ($query) use ($departmentId) {
            $query->where('department_id', $departmentId);
        })
            ->get();

        return $courses;
    }

    // Create allocation, a course to a department
    public function store(Request $request)
    {
        $request->validate([
            'department_id' => 'required|int',
            'course_id' => 'required|int'
        ]);

        // Get Course by id:
        $course = Course::findOrFail($request->course_id);

        // Adding a Course to a Department in Pivot table course_department
        $course->departments()->attach($request->department_id);

        return response_success(['success' => true]);
    }

    // Delete course to department Allocation
    public function destroy(Request $request)
    {
        // Get Course by id:
        $course = Course::findOrFail($request->course_id);

        // Adding a Course to a Department in Pivot table course_department
        $course->departments()->detach($request->department_id);

        return response_success(['success' => true]);
    }
}
