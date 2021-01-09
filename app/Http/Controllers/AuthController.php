<?php

namespace App\Http\Controllers;

use Firebase\Auth\Token\Exception\InvalidToken;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Company;
use App\Models\Department;

use Exception;
use Validator;

class AuthController extends Controller
{

    // Login will verify User Request firebaseToken with Firebase
    // => get Laravel user => Return Json with Passport Token
    public function login(Request $request)
    {

        // Launch Firebase Auth
        $auth = app('firebase.auth');
        // Get Firebasetoken from user Login Request
        $firebaseToken = $request->firebaseToken;


        try {
            // Try to verify the Firebase credential token with Google
            $verifiedIdToken = $auth->verifyIdToken($firebaseToken);
        } catch (\InvalidArgumentException $e) { // If the token has the wrong format

            return response()->json([
                'error' => 'Unauthorized - Can\'t parse the token: ' . $e->getMessage()
            ], 401);
        } catch (InvalidToken $e) { // If the token is invalid (expired ...)

            return response()->json([
                'error' => 'Unauthorized - Token is invalid: ' . $e->getMessage()
            ], 401);
        }

        // Retrieve the UID (User ID) from the verified Firebase credential's token
        $uid = $verifiedIdToken->getClaim('sub');

        // Retrieve the user model linked with the Firebase UID
        $user = User::where('uid', $uid)->first();

        // Accept only Admin users from WEB Platform:
        if ($request->platform === 'web' && $user->isAdmin === 1) {
            // Create a Personal Access Token
            $passportToken = $user->createToken('Personal Access Token');

            // Store the created token
            $token = $passportToken->token;

            // Add a expiration date to the token
            if ($request->rememberMe === 1) {
                $token->expires_at = Carbon::now()->addDays(30);
            } else {
                $token->expires_at = Carbon::now()->addDays(1);
            }

            // Save the token to the user
            $token->save();

            $data = [
                'id' => $user->id,
                'userName' => $user->firstName . " " .  $user->lastName,
                'token' => $passportToken->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $passportToken->token->expires_at
                )->toDateTimeString()
            ];

            // Return a JSON object containing the token data
            return response()->json(
                [
                    'success' => true,
                    'data' => $data
                ],
                200
            );
        } else if ($request->platform === 'mobile') {
            // Mobile users login: Allow any user to login if platform = mobile

            // Create a Personal Access Token
            $passportToken = $user->createToken('Personal Access Token');

            $token = $passportToken->token;
            // Add a expiration date to the token
            $token->expires_at = Carbon::now()->addDays(30);
            $token->save();

            $department = $user->department()->first();

            $data = [
                'id' => $user->id,
                'user' =>  $user, // full user model
                'department' =>  $department, // full department model
                'token' => $passportToken->accessToken,
                'token_type' => 'Bearer',
                'expires_at' => Carbon::parse(
                    $passportToken->token->expires_at
                )->toDateTimeString()
            ];

            // Return a JSON object containing the token data
            return response()->json(
                [
                    'success' => true,
                    'data' => $data
                ],
                200
            );
        } else {
            return response()->json(['error' => 'loginFailed'], 401);
        }
    }

    public function signup(Request $request)
    {
        // Launch Firebase Auth
        $auth = app('firebase.auth');
        // Get Firebasetoken from user Login Request
        $firebaseToken = $request->firebaseToken;

        try {
            // Try to verify the Firebase credential token with Google
            $verifiedIdToken = $auth->verifyIdToken($firebaseToken);
        } catch (\InvalidArgumentException $e) { // If the token has the wrong format

            return response()->json([
                'error' => 'Unauthorized - Can\'t parse the token: ' . $e->getMessage()
            ], 401);
        } catch (InvalidToken $e) { // If the token is invalid (expired ...)

            return response()->json([
                'error' => 'Unauthorized - Token is invalid: ' . $e->getMessage()
            ], 401);
        }

        // Retrieve the UID (User ID) from the verified Firebase credential's token
        $uid = $verifiedIdToken->getClaim('sub');

        // Confirm if user model linked with the Firebase UID is unique
        $user = User::where('uid', $uid)->first();

        if ($user === null) {
            // Creating new User
            try {
                //Validate register request params
                $validator = Validator::make($request->all(), [
                    'firstName' => 'required|string|max:64',
                    'lastName' => 'required|string|max:64',
                    'email' => 'required|email|string|unique:users',
                    'companyName' => 'required|string|max:64|unique:companies,name', // Validating unique company name
                    'departmentName' => 'required|string|max:64|unique:departments,name', // Validating unique department name
                ]);

                //Validation / On fail - Return error
                if ($validator->fails()) {
                    return response()->json(['validatorFailError' => $validator->errors()], 400);
                }

                //Validation / On Success
                $input = $request->all();

                $companyName = $input['companyName'];
                $departmentName = $input['departmentName'];

                // Checking if Company Name alread Exists in Database
                $company = Company::where('name', '=', $companyName)->first();
                if ($company === null) {
                    //Company name is Unique. Create new company:
                    $company = new Company([
                        'name' => $companyName
                    ]);

                    $company->save();
                    $newCompanyId = $company->id;

                    // Creating new Department using new Company ID:
                    $department = new Department([
                        'name' => $departmentName,
                        'company_id' => $newCompanyId
                    ]);
                    $department->save();
                    $newDepartmentId = $department->id;

                    $input['uid'] = $uid;
                    $input['department_id'] = $newDepartmentId;
                    $input['isAdmin'] = 1;

                    //Create User
                    $user = User::create($input);

                    // Create a Personal Access Token
                    $passportToken = $user->createToken('Personal Access Token');

                    // Store the created token
                    $token = $passportToken->token;

                    // Add a expiration date to the token
                    $token->expires_at = Carbon::now()->addDays(30);

                    // Save the token to the user
                    $token->save();

                    //Create + Return Token + User info
                    $data['token'] = $passportToken->accessToken;
                    $data['firstName'] = $user->firstName;
                    $data['lastName'] = $user->lastName;
                    return response()->json(['success' => true, 'data' => $data], 200);
                }

                // Return error response: Company name already exist
                return response()->json(['error' => 'Company Name Already Exist'], 409);
            } catch (Exception $e) {
                return response()->json(['error' => $e->getMessage()], 400);
            }
        } else {
            // User already exist in Database
            return response()->json(['error' => 'userAlreadyExist'], 409);
        }
    }

    public function logout(Request $request)
    {
        current_user()->token()->revoke();

        return response()->json(['success' => true], 200);
    }
}
