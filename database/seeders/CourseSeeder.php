<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $courses = [
            [
                'instructor_id' => 2,
                'title' => 'Learn Laravel',
                'description' => 'Learn Laravel from scratch',
                'category' => 'PHP',
            ],
            [
                'instructor_id' => 2,
                'title' => 'Learn Vue.js',
                'description' => 'Learn Vuejs from scratch',
                'category' => 'JavaScript',
            ],
            [
                'instructor_id' => 2,
                'title' => 'Learn React',
                'description' => 'Learn React from scratch',
                'category' => 'JavaScript',
            ],
            [
                'instructor_id' => 2,
                'title' => 'Learn Python',
                'description' => 'Learn Python from scratch',
                'category' => 'Python',
            ],
        ];

        foreach ($courses as $course) {
            \App\Models\Course::create($course);
        }
    }
}
