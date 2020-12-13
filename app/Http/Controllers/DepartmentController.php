<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Exception;
use Validator;

use App\Models\User;
use App\Models\Company;
use App\Models\Department;


class DepartmentController extends Controller
{
    public function index()
    {
        try {
            //Re-enable current user + find a better approach to get all departments:
            // $currentUser = current_user();
            $userDepartment = User::findOrFail(2)->department()->first();
            $userCompany = $userDepartment->company()->first();

            return $userCompany
                ->departments()
                ->orderBy('name', 'asc')
                ->get();
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        try {
            // validate the input data
            $request->validate([
                'name' => 'required|string|max:64',
            ]);

            // Fetching the current user's company id
            //Re-enable current user + find a better approach to get all departments:
            // $currentUser = current_user();
            $userDepartment = User::findOrFail(2)->department()->first();
            $userCompany = $userDepartment->company()->first();
            $companyId = $userCompany->id;

            // Creating department
            $department = new Department([
                'name' => $request->name,
                'company_id' => $companyId
            ]);

            $department->save();

            // return a success response
            $data['name'] = $request->name;
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            return Department::findOrFail($id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request, Department $department)
    {

        try {
            // validate the input data
            $attributes = $request->validate([
                'name' => 'required|string|max:128',
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
