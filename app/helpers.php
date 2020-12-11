<?php
/*
|--------------------------------------------------------------------------
| Helper methods that makes the syntax more readable
|--------------------------------------------------------------------------
| Add methods that has common usage here
|
*/


use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;

/**
 * The method that returns the current user from the current session
 *
 * @return User|Authenticatable|null
 */
function current_user()
{
    // return the current user
    return auth()->user();
}

/**
 * the method that returns a json response object with status code 200
 *
 * @param array $data array of data to send it with the response
 * @return JsonResponse
 */
function response_success($data = [])
{
    return response()->json($data);
}

/**
 * the method that returns a json response object with status code 401
 *
 * @param array $data array of data to send it with the response
 * @return JsonResponse
 */
function response_failure_401($data = [])
{
    return response()->json($data, 401);
}

/**
 * the method that returns a json response object with status code 201
 *
 * @param array $data array of data to send it with the response
 * @return JsonResponse
 */
function response_data_created($data = [])
{
    return response()->json($data, 201);
}

/**
 * the method that returns an array with the provided msg (key->value)
 *
 * @param $message
 * @return array
 */
function response_message($message)
{
    return ['message' => $message];
}
