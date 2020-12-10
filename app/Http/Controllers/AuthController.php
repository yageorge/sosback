<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Validator;

class AuthController extends Controller
{

    public function login()
    {
        try {
            if (Auth::attempt([
                'email' => request('email'),
                'password' => request('password')
            ])) {
                //Authentication / On Success
                //Create User + Token
                $user = Auth::user();
                $data['token'] = $user->createToken('MyApp')->accessToken;
                //todo apply data logic array like signup
                return response()->json(['success' => true, 'data' => $data], 200);
            }
            return response()->json(['error' => 'loginFailed'], 401);

            //apply this catch error logic in signup as well
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function signup(Request $request)
    {
        try {
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
                return response()->json(['validatorFailError' => $validator->errors()], 400);
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

            //Create + Return Token + User info
            $data['token'] = $user->createToken('MyApp')->accessToken;
            $data['firstName'] = $user->firstName;
            $data['lastName'] = $user->lastName;
            return response()->json(['success' => true, 'data' => $data], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
