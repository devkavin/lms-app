<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function viewAllInstructors()
    {
        return response()->json([
            'instructors' => User::role('instructor')->get()
        ], 200);
    }
}
