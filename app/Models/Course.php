<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = ['course_code', 'title', 'description', 'category', 'instructor_id', 'created_at', 'updated_at'];

    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'course_user', 'course_id', 'student_id');
    }

    public function isEnrolled($studentId)
    {
        return $this->students()->where('student_id', $studentId)->exists();
    }
}
