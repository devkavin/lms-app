<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $user->hasPermissionTo('courses_view')
            ? Response::allow()
            : Response::deny('You do not have permission to view courses');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): Response
    {
        return $user->hasPermissionTo('courses_view')
            ? Response::allow()
            : Response::deny('You do not have permission to view this course');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $user->hasPermissionTo('courses_create')
            ? Response::allow()
            : Response::deny('You do not have permission to create courses');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): Response
    {
        return $user->hasPermissionTo('courses_edit')
            ? Response::allow()
            : Response::deny('You do not have permission to edit this course');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): Response
    {
        return $user->hasPermissionTo('courses_delete')
            ? Response::allow()
            : Response::deny('You do not have permission to delete this course');
    }

    /**
     * Determine whether the user can enroll in the model.
     */
    public function enroll(User $user, Course $course): Response
    {
        return $user->hasPermissionTo('enroll_course')
            ? Response::allow()
            : Response::deny('You do not have permission to enroll in this course');
    }

    /**
     * Determine whether the user can unenroll in the model.
     */
    public function unenroll(User $user, Course $course): Response
    {
        if ($course->instructor_id === $user->id) {
            return Response::deny('Instructors cannot unenroll from their own courses');
        }

        return $user->hasPermissionTo('unenroll_course')
            ? Response::allow()
            : Response::deny('You do not have permission to unenroll from this course');
    }
}
