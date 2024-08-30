<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Helpers\APIHelper;
use App\Http\Resources\CourseResource;
use Illuminate\Http\Request;
use App\Models\Course;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class CourseController extends Controller
{
    /**
     * Summary of index
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException If the user is not authorized to view courses.
     * @throws \Exception If an error occurs while retrieving the courses.
     */
    public function index(): JsonResponse
    {
        try {
            Gate::authorize('viewAny', Course::class);

            $courses = Course::orderBy("id", "desc")->paginate(10); // latest courses first

            $coursesResource = CourseResource::collection($courses); // use CourseResource to format the response and return it as a JSON collection

            return APIHelper::makeApiResponse(true, 'Courses retrieved successfully', $coursesResource, APIHelper::HTTP_CODE_SUCCESS);
        } catch (AuthorizationException $e) {
            return APIHelper::makeApiResponse(false, 'Unauthorized', null, APIHelper::HTTP_CODE_FORBIDDEN);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, $e->getMessage(), null, APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }

    /**
     * Summary of show
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     * @throws ModelNotFoundException If the course is not found.
     * @throws AuthorizationException If the user is not authorized to view the course.
     * @throws \Exception If an error occurs while retrieving the course.
     */
    public function show($id): JsonResponse
    {
        try {
            $course = Course::findOrFail($id);

            Gate::authorize('view', $course);

            $coursesResource = new CourseResource($course); // use CourseResource to format the response and return it as a JSON object

            // return response()->json([
            //     'course' => $coursesResource
            // ], 200);

            return APIHelper::makeApiResponse(true, 'Course retrieved successfully', $coursesResource, APIHelper::HTTP_CODE_SUCCESS);
        } catch (ModelNotFoundException  $e) {
            return APIHelper::makeApiResponse(false, 'Course not found', null, APIHelper::HTTP_CODE_NOT_FOUND);
        } catch (AuthorizationException $e) {
            return APIHelper::makeApiResponse(false, 'Unauthorized', null, APIHelper::HTTP_CODE_FORBIDDEN);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, $e->getMessage(), null, APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }

    /**
     * Summary of store
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException If the user is not authorized to create a course.
     * @throws ValidationException If the request data is invalid.
     * @throws \Exception If an error occurs while creating the course.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::user();
            Gate::authorize('create', Course::class); // check if user has permission to create a course

            $validation_schema = [
                'course_code'   => 'required|unique:courses,course_code',
                'title'         => 'required|string',
                'description'   => 'required|string',
                'category'      => 'required|string',
            ];

            $validator = APIHelper::validateRequest($validation_schema, $request);
            if ($validator['errors']) {
                return APIHelper::makeApiResponse(false, $validator['error_messages'], null, 400);
            }

            // dd($validator);

            $course = Course::create([
                'course_code'   => $request->course_code,
                'title'         => $request->title,
                'description'   => $request->description,
                'category'      => $request->category,
                'instructor_id' => $user->id,
            ]);

            // return response()->json([
            //     'course' => $course
            // ], 201); // 201 for succesfully creating a resource

            $courseResource = new CourseResource($course);

            return APIHelper::makeApiResponse(true, 'Course created successfully', $courseResource, APIHelper::HTTP_CODE_SUCCESS);
        } catch (AuthorizationException $e) {
            return APIHelper::makeApiResponse(false, 'Unauthorized', null, APIHelper::HTTP_CODE_FORBIDDEN);
        } catch (ValidationException $e) {
            return APIHelper::makeApiResponse(false, 'Validation error', $e->errors(), APIHelper::HTTP_CODE_BAD_REQUEST);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, $e->getMessage(), null, APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }

    /**
     * Summary of update
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException If the user is not authorized to update the course.
     * @throws ModelNotFoundException If the course is not found.
     * @throws ValidationException If the request data is invalid.
     * @throws \Exception If an error occurs while updating the course.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Retrieve the course and authorize the action
            $course = Course::findOrFail($id);

            Gate::authorize('update', $course);

            $validation_schema = [
                'title'         => 'required|string',
                'description'   => 'required|string',
                'category'      => 'required|string',
            ];

            $validator = APIHelper::validateRequest($validation_schema, $request);
            if ($validator['errors']) {
                return APIHelper::makeApiResponse(false, $validator['error_messages'], null, 400);
            }

            // Update the course with validated data
            $course->update($validator['data']);

            // Return the updated course as a resource
            return APIHelper::makeApiResponse(true, 'Course updated successfully', new CourseResource($course), APIHelper::HTTP_CODE_SUCCESS);
        } catch (ModelNotFoundException $e) {
            return APIHelper::makeApiResponse(false, 'Course not found', null, APIHelper::HTTP_CODE_NOT_FOUND);
        } catch (AuthorizationException $e) {
            return APIHelper::makeApiResponse(false, 'Unauthorized', null, APIHelper::HTTP_CODE_FORBIDDEN);
        } catch (ValidationException $e) {
            return APIHelper::makeApiResponse(false, 'Validation error', $e->errors(), APIHelper::HTTP_CODE_BAD_REQUEST);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, $e->getMessage(), null, APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }


    /**
     * Summary of enroll
     * @param mixed $id
     * @var \App\Models\User $user
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException If the user is not authorized to enroll in the course.
     * @throws ModelNotFoundException If the course is not found.
     * @throws \Exception If an error occurs while enrolling the user in the course.
     */
    public function enroll($id): JsonResponse
    {
        try {
            $user = Auth::user();
            $course = Course::with('students')->findOrFail($id); // eager load students with the course for better performance

            Gate::authorize('enroll', $course);

            if ($course->students->contains($user)) {
                return APIHelper::makeApiResponse(false, 'You are already enrolled in this course', null, APIHelper::HTTP_CODE_CONFLICT);
            }

            $user->enrollCourse($course);

            return APIHelper::makeApiResponse(true, 'You have enrolled in the course', null, APIHelper::HTTP_CODE_SUCCESS);
        } catch (AuthorizationException $e) {
            return APIHelper::makeApiResponse(false, 'Unauthorized', null, APIHelper::HTTP_CODE_FORBIDDEN);
        } catch (ModelNotFoundException $e) {
            return APIHelper::makeApiResponse(false, 'Course not found', null, APIHelper::HTTP_CODE_NOT_FOUND);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, $e->getMessage(), null, APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }

    /**
     * Summary of unenroll
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException If the user is not authorized to unenroll from the course.
     * @throws ModelNotFoundException If the course is not found.
     * @throws \Exception If an error occurs while unenrolling the user from the course.
     */
    public function unenroll($id): JsonResponse
    {
        try {
            $user = Auth::user();

            $course = Course::with('students')->findOrFail($id); // eager load students with the course for better performance

            Gate::authorize('unenroll', $course);

            if (!$course->students->contains($user->id)) {
                return APIHelper::makeApiResponse(false, 'You are not enrolled in this course', null, APIHelper::HTTP_CODE_CONFLICT);
            }

            $user->unenrollCourse($course);

            return APIHelper::makeApiResponse(true, 'You have unenrolled from the course', null, APIHelper::HTTP_CODE_SUCCESS);
        } catch (AuthorizationException $e) {
            return APIHelper::makeApiResponse(false, 'Unauthorized', null, APIHelper::HTTP_CODE_FORBIDDEN);
        } catch (ModelNotFoundException  $e) {
            return APIHelper::makeApiResponse(false, 'Course not found', null, APIHelper::HTTP_CODE_NOT_FOUND);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, $e->getMessage(), null, APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }

    /**
     * Summary of isEnrolled
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     * @throws ModelNotFoundException If the course is not found.
     * @throws \Exception If an error occurs while checking if the user is enrolled in the course.
     */
    public function isEnrolled($id): JsonResponse
    {
        try {
            $user = Auth::user();
            $course = Course::with('students')->findOrFail($id); // eager load students with the course for better performance

            $isEnrolled = $course->students->contains($user->id);
            $message = $isEnrolled ? "You are enrolled in this course." : "You are not enrolled in this course.";

            return APIHelper::makeApiResponse(true, $message, ['is_enrolled' => $isEnrolled], APIHelper::HTTP_CODE_SUCCESS);
        } catch (ModelNotFoundException  $e) {
            return APIHelper::makeApiResponse(false, 'Course not found', null, APIHelper::HTTP_CODE_NOT_FOUND);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, $e->getMessage(), null, APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     * @throws AuthorizationException If the user is not authorized to delete the course.
     * @throws ModelNotFoundException If the course is not found.
     * @throws \Exception If an error occurs while deleting the course.
     */
    public function destroy($id): JsonResponse
    {
        try {
            $course = Course::findOrFail($id);

            Gate::authorize('delete', $course);

            // detach all students before deleting the course(cannot delete because of pivot table relationship)
            $course->students()->detach();

            $course->delete();

            return APIHelper::makeApiResponse(true, 'Course Deleted', null, APIHelper::HTTP_NO_DATA_FOUND);
        } catch (AuthorizationException $e) {
            return APIHelper::makeApiResponse(false, 'Unauthorized', null, APIHelper::HTTP_CODE_FORBIDDEN);
        } catch (ModelNotFoundException  $e) {
            return APIHelper::makeApiResponse(false, 'Course not found', null, APIHelper::HTTP_CODE_NOT_FOUND);
        } catch (\Exception $e) {
            return APIHelper::makeApiResponse(false, $e->getMessage(), null, APIHelper::HTTP_CODE_SERVER_ERROR);
        }
    }
}
