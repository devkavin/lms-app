<?php

namespace App\Http\Controllers;

use App\Http\Requests\ViewInstructorsRequest;
use App\Http\Resources\InstructorResource;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function viewAllUsers(Request $request)
    {
        return response()->json([
            'users' => User::all()
        ], 200);
    }



    public function viewAllInstructors()
    {
        $instructors = User::role('instructor')->get();
        $instructorsResource = InstructorResource::collection($instructors); // use InstructorResource to format the response and return it as a JSON collection
        return response()->json([
            'instructors' => $instructorsResource
        ], 200);
    }

    public function viewAllStudents(Request $request)
    {
        $students = User::role('student')->get();
        $instructorsResource = InstructorResource::collection($students);
        return response()->json([
            'students' => $students
        ], 200);
    }
}
