<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\APIHelper;
use App\Http\Requests\ViewInstructorsRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\StudentResource;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * View all users
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception if an error occurred while fetching users
     */
    public function viewAllUsers(Request $request): JsonResponse
    {
        try {
            $filterByRole = $request->query('role');
            // Filter query
            $filterByRole ? $users = User::role($filterByRole)->paginate(10) : $users = User::paginate(10);
            return APIHelper::makeApiResponse(true, 'Users fetched successfully', $users, APIHelper::HTTP_CODE_SUCCESS);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, 'Error occurred while fetching users', $e->getMessage(), APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }

    /**
     * View all admins
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewAllAdmins()
    {
        $admins = User::role('admin')->get();
        $adminResource = AdminResource::collection($admins); // use AdminResource to format the response and return it as a JSON collection
        return response()->json([
            'admins' => $adminResource
        ], 200);
    }

    /**
     * Delete an admin
     *
     * @param ViewInstructorsRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser(Request $request)
    {

        $validated = $request->validate([
            'id' => 'required|exists:users,id',
        ]);


        if ($validated['id'] === Auth::id()) {
            return response()->json([
                'message' => 'You cannot delete yourself'
            ], 400);
        }

        $user = User::find($validated['id']);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        if ($user->hasRole('admin')) {
            return response()->json([
                'message' => 'You cannot delete an admin'
            ], 400);
        }
        if ($user->id === Auth::id()) {
            return response()->json([
                'message' => 'You cannot delete yourself'
            ], 400);
        }

        $user->enrolledCourses()->detach(); // Unenroll the user from all courses
        $user->createdCourses()->delete(); // Delete the courses created by the user
        $user->delete(); // Delete the user
        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }

    /**
     * Bulk delete users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function bulkDeleteUsers(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1|exists:users,id',
        ]);

        $ids = $validated['ids'];
        if (!is_array($ids)) {
            return response()->json([
                'message' => 'Invalid input'
            ], 400);
        }

        if (in_array(Auth::id(), $ids)) {
            return response()->json([
                'message' => 'You cannot delete yourself'
            ], 400);
        }

        User::whereIn('id', $ids)->each(function ($user) {
            $user->enrolledCourses()->detach(); // Unenroll the user from all courses
            $user->createdCourses()->delete(); // Delete the courses created by the user
            $user->delete(); // Delete the user
        });
        return response()->json([
            'message' => 'Users deleted successfully'
        ], 200);
    }
}
