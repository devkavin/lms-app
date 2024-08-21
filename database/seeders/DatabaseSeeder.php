<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call(PermissionSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(CourseSeeder::class);

        // use CourseFactory
        Course::factory(5)->create();
        User::factory(50)->create()->each(function ($user) {
            // assign student role
            $user->assignRole('student');
        });

        // enroll students to courses
        $course = Course::where('id', 1)->first();
        $students = User::role('student')->get();
        $course->students()->attach($students);
    }
}
