<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\APIHelper;
use App\Http\Resources\StudentResource;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Registers a new user in the system
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException if the request data is invalid
     * @throws \Exception if an error occurred while registering user
     */
    public function register(Request $request): JsonResponse
    {
        try {
            $validation_schema = [
                'name'                  => 'required|string',
                'email'                 => 'required|email|unique:users,email',
                'password'              => 'required|string|min:6|confirmed',
                'password_confirmation' => 'required|same:password',
                'role'                  => 'required|string|in:instructor,student',
            ];

            $validator = APIHelper::validateRequest($validation_schema, $request);
            if ($validator['errors']) {
                return APIHelper::makeApiResponse(false, $validator['error_messages'], null, 400);
            }

            $request->password = bcrypt($request->password);
            $user = User::create($request->all())->assignRole($request['role']); // with one db query, create and assign role to user

            $token = $user->createToken('auth_token')->plainTextToken;

            // return response()->json([
            //     'user' => $user,
            //     'token' => $token,
            // ], 201);

            return APIHelper::makeApiResponse(true, 'User registered successfully', ['token' => $token], APIHelper::HTTP_CODE_CREATED);
        } catch (ValidationException $e) {
            return APIHelper::makeApiResponse(false, 'Validation error', $e->errors(), APIHelper::HTTP_CODE_BAD_REQUEST);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, 'Error occurred while registering user', $e->getMessage(), APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }

    /**
     * Logs in a user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws ValidationException if the request data is invalid
     * @throws \Exception if an error occurred while logging in user
     */
    public function login(Request $request): JsonResponse
    {

        try {
            $validation_schema = [
                'email'     => 'required|email',
                'password'  => 'required|string|min:6',
            ];

            $validator = APIHelper::validateRequest($validation_schema, $request);
            if ($validator['errors']) {
                return APIHelper::makeApiResponse(false, $validator['error_messages'], null, 400);
            }

            if (!Auth::attempt($request->only('email', 'password'))) {
                return APIHelper::makeApiResponse(false, 'Invalid credentials', null, 401);
            }

            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return APIHelper::makeApiResponse(true, 'User logged in successfully', ['token' => $token], 200);
        } catch (ValidationException $e) {
            return APIHelper::makeApiResponse(false, 'Validation error', $e->errors(), APIHelper::HTTP_CODE_BAD_REQUEST);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, 'Error occurred while logging in user', $e->getMessage(), APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }

    /**
     * Logs out a user
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception if an error occurred while logging out user
     */
    public function logout(): JsonResponse
    {
        try {
            $user = Auth::user();

            if ($user) {
                $user->tokens()->delete();

                return APIHelper::makeApiResponse(true, 'Logged out successfully', null, 204); // 204 No Content
            }

            return APIHelper::makeApiResponse(false, 'No authenticated user found', null, 401); // Unauthorized
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, 'Error occurred while logging out user', $e->getMessage(), APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }

    /**
     * Retrieves the authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception if an error occurred while retrieving user
     */
    public function user(): JsonResponse
    {
        try {
            $user = Auth::user();
            $userResource = new UserResource($user);

            return APIHelper::makeApiResponse(true, 'User retrieved successfully', $userResource, 200);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, 'Error occurred while retrieving user', $e->getMessage(), APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }
}
