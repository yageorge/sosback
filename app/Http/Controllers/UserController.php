<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Validator;

class UserController extends Controller
{

    public function login()
    {
        if (Auth::attempt([
            'email' => request('email'),
            'password' => request('password')
        ])) {
            //Authentication / On Success
            //Create User + Token
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->accessToken;
            return response()->json(['sucess' => $success], 200);
        } else {
            return response()->json(['error' => 'Unauthorised'], 401);
        }
    }

    public function signup(Request $request)
    {

        //Validate register request params
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string',
            'lastName' => 'required|string',
            'email' => 'required|email|string|unique:users',
            'departmentName' => 'required',
            'password' => 'required',
            'passwordConfirmation' => 'required|same:password',
        ]);

        //Validation / On fail - Return error
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 401);
        }

        //Validation / On Success
        $input = $request->all();

        //TODO Create Department OR if existing fetch ID only:
        $departmentId = $input['departmentName']; //this is temp

        $input['password'] = bcrypt($input['password']);
        $input['isAdmin'] = 1;
        $input['department_id'] = $departmentId;

        //Create User
        $user = User::create($input);

        //Create + Return Token
        $success['token'] = $user->createToken('MyApp')->accessToken;
        $success['firstName'] = $user->firstName;
        $success['lastName'] = $user->lastName;
        return response()->json(['success' => $success], 200);
    }
}
