<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;

class DepartmentController extends Controller
{
    public function index()
    {
        //Re-enable current user + find a better approach to get all departments:
        // $currentUser = current_user();
        $userDepartment = User::findOrFail(10)->department()->first();
        $userCompany = $userDepartment->company()->first();

        return $userCompany->departments()->get();
    }
}
