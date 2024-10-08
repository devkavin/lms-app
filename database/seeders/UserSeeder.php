<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('password'), // password is 'password'
            ],
            [
                'name' => 'Instructor James',
                'email' => 'instructorjames@gmail.com',
                'password' => bcrypt('password'), // password is 'password'
            ],
            [
                'name' => 'Instructor Mary',
                'email' => 'instructormary@gmail.com',
                'password' => bcrypt('password'), // password is 'password'
            ],
            [
                'name' => 'Student John',
                'email' => 'studentjohn@gmail.com',
                'password' => bcrypt('password'), // password is 'password'
            ],
            [
                'name' => 'Student Jane',
                'email' => 'studentjane@gmail.com',
                'password' => bcrypt('password'),
            ],
            [
                'name' => 'No Role User',
                'email' => 'noroleuser@gmail.com',
                'password' => bcrypt('password'),
            ],
        ];

        foreach ($users as $user) {
            \App\Models\User::create($user);
        }

        $admin = \App\Models\User::where('email', 'admin@gmail.com')->first();
        $admin->assignRole('admin');

        $instructor = \App\Models\User::where('email', 'instructorjames@gmail.com')->first();
        $instructor->assignRole('instructor');

        $instructor = \App\Models\User::where('email', 'instructormary@gmail.com')->first();
        $instructor->assignRole('instructor');

        $student = \App\Models\User::where('email', 'studentjohn@gmail.com')->first();
        $student->assignRole('student');

        $student = \App\Models\User::where('email', 'studentjane@gmail.com')->first();
        $student->assignRole('student');
    }
}
