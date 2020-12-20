<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Course;
use App\Models\Category;
use App\Models\Company;

// Handling Courses to Departments Allocations
class AllocationsController extends Controller
{

    // Returning all courses for one Department id
    public function allocated($departmentId)
    {
        try {
            $department = Department::findOrFail($departmentId);
            return $department->courses()->get();
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Returning all Courses Un-Allocated to 1 department ID + belonging to one company
    public function unallocated($departmentId)
    {
        try {

            // Getting all unAllocated courses to departmentId
            $courses = Course::whereDoesntHave('departments', function ($query) use ($departmentId) {
                $query->where('department_id', $departmentId);
            })->get();

            // Getting currentUser companyId
            $userDepartment = current_user()->department()->first();
            $userCompany = $userDepartment->company()->first();

            // Filter courses with current user's company id
            $filtered = $courses->filter(function ($course) use ($userCompany) {

                $category = Category::findOrFail($course->category_id);
                return $category->company_id === $userCompany->id;
            });

            // Returning unAllocated Courses + flatten() to clean result sending only an array not an object
            return $filtered->flatten();
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Create allocation, a course to a department
    public function store(Request $request)
    {
        try {
            $request->validate([
                'department_id' => 'required|int',
                'course_id' => 'required|int'
            ]);

            // Get Course by id:
            $course = Course::findOrFail($request->course_id);

            // Adding a Course to a Department in Pivot table course_department
            $course->departments()->attach($request->department_id);

            return response_success(['success' => true]);
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Delete course to department Allocation
    public function destroy(Request $request)
    {
        try {
            // Get Course by id:
            $course = Course::findOrFail($request->course_id);

            // Adding a Course to a Department in Pivot table course_department
            $course->departments()->detach($request->department_id);

            return response_success(['success' => true]);
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }
}
