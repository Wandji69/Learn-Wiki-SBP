<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollRequest;
use App\Http\Resources\CourseResource;
use App\Services\EnrollmentService;
use Illuminate\Http\Request;

class EnrollmentController extends Controller
{
    private EnrollmentService $enrollmentService;

    public function __construct(EnrollmentService $enrollmentService)
    {
        $this->enrollmentService = $enrollmentService;
    }

    public function index(Request $request)
    {
        $enrollments = $this->enrollmentService->listUserEnrollments($request->user()->id);

        return CourseResource::collection($enrollments->pluck('course'));
    }

    public function store(EnrollRequest $request)
    {
        $user = $request->user();
        $courseId = (int) $request->validated('course_id');

        $enrollment = $this->enrollmentService->enroll($user->id, $courseId);

        return response()->json([
            'message' => 'Enrollment created successfully.',
            'enrollment' => $enrollment,
        ], 201);
    }
}
