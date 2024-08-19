<?php

namespace App\Http\Controllers;

use App\Http\Requests\ViewInstructorsRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\InstructorResource;
use App\Http\Resources\StudentResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * View all users
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function viewAllUsers()
    {
        return response()->json([
            'users' => User::all()
        ], 200);
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
}
