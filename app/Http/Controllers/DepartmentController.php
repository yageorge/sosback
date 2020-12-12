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
        //Re-enable current user + find a better approach to get all departments:
        // $currentUser = current_user();
        $userDepartment = User::findOrFail(1)->department()->first();
        $userCompany = $userDepartment->company()->first();

        return $userCompany->departments()->get();
    }

    public function store(Request $request)
    {
        try {
            // validate the input data
            $request->validate([
                'departmentName' => 'required|string|max:128',
            ]);

            // Fetching the current user's company id
            //Re-enable current user + find a better approach to get all departments:
            // $currentUser = current_user();
            $userDepartment = User::findOrFail(1)->department()->first();
            $userCompany = $userDepartment->company()->first();
            $companyId = $userCompany->id;

            // Creating department
            $department = new Department([
                'name' => $request->departmentName,
                'company_id' => $companyId
            ]);

            $department->save();

            // return a success response
            $data['departmentName'] = $request->departmentName;
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
