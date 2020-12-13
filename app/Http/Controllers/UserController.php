<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    // Return all emlpoyees of the current user's company
    public function index()
    {
        try {
            //To be improved
            $userDepartment = current_user()->department()->first();
            $userCompany = $userDepartment->company()->first();
            $companyUsers = $userCompany->users()->get();

            return $companyUsers;
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }
}
