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
                'course_code' => 'AA00',
                'instructor_id' => 2,
                'title' => 'Learn Laravel',
                'description' => 'Learn Laravel from scratch',
                'category' => 'PHP',
            ],
            [
                'course_code' => 'AA01',
                'instructor_id' => 2,
                'title' => 'Learn Vue.js',
                'description' => 'Learn Vuejs from scratch',
                'category' => 'JavaScript',
            ],
            [
                'course_code' => 'AA02',
                'instructor_id' => 2,
                'title' => 'Learn React',
                'description' => 'Learn React from scratch',
                'category' => 'JavaScript',
            ],
            [
                'course_code' => 'AA03',
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
