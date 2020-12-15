<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Validator;

class UserController extends Controller
{
    // Return all emlpoyees of the current user's company
    public function index()
    {
        try {
            //To be improved
            $userDepartment = current_user()->department()->first();
            $userCompany = $userDepartment->company()->first();
            $companyUsers = $userCompany
                ->users()
                ->select('users.*', 'departments.name as departmentName')
                ->orderBy('firstName', 'asc')
                ->get();

            return $companyUsers;
        } catch (Exception $e) {
            return response_success(['error' => $e->getMessage()]);
        }
    }

    // Creating new User
    public function store(Request $request)
    {
        try {
            //Validate register request params
            $validator = Validator::make($request->all(), [
                'firstName' => 'required|string|max:64',
                'lastName' => 'required|string|max:64',
                'email' => 'required|email|string|unique:users',
                'password' => 'required|min:8',
                'passwordConfirmation' => 'required||min:8|same:password',
                'departmentId' => 'required|int',
                'isAdmin' => 'required|int',
            ]);

            //Validation / On fail - Return error
            if ($validator->fails()) {
                return response()->json(['validatorFailError' => $validator->errors()], 400);
            }

            //Validation / On Success
            $input = $request->all();
            $input['department_id'] = $input['departmentId'];
            $input['password'] = bcrypt($input['password']);

            //Create User
            $user = User::create($input);

            //Return User info
            $data['firstName'] = $user->firstName;
            $data['lastName'] = $user->lastName;
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // Return user to be updated, by id
    public function edit($id)
    {
        try {
            return User::findOrFail($id);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    // Deleting a User
    public function destroy($id)
    {

        try {
            $user = User::findOrFail($id);
            $data['firstName'] = $user->firstName;

            $user->delete();

            // return a success response
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
