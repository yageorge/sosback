<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Validator;

use App\Models\Department;


class DepartmentController extends Controller
{

    // Return all departments of the current user's company
    public function index()
    {
        try {
            //To be improved
            $userDepartment = current_user()->department()->first();
            $userCompany = $userDepartment->company()->first();

            return $userCompany
                ->departments()
                ->orderBy('name', 'asc')
                ->get();
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Creating new Department
    public function store(Request $request)
    {
        try {
            // validate the input data
            $request->validate([
                'name' => 'required|string|max:64',
            ]);

            // Get current user's company id
            $userDepartment = current_user()->department()->first();
            $companyId = $userDepartment->company_id;

            // Creating department
            $department = new Department([
                'name' => $request->name,
                'company_id' => $companyId
            ]);

            $department->save();

            // return success response + name of new department
            $data['name'] = $request->name;
            return response_success(['success' => true, 'data' => $data]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Return department to be updated, by id
    public function edit($id)
    {
        try {
            return Department::findOrFail($id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Updating department
    public function update(Request $request, Department $department)
    {

        try {
            // validate the input data
            $attributes = $request->validate([
                'name' => 'required|string|max:64',
            ]);

            //Updating department
            $department->update($attributes);

            // return a success response
            $data['name'] = $request->name;
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Deleting a department
    public function destroy($id)
    {

        try {
            $department = Department::findOrFail($id);
            $data['name'] = $department->name;

            $department->delete();

            // return a success response
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
