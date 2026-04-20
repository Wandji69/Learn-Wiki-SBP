<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EnrollRequest;
use App\Services\EnrollmentService;

class EnrollmentController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollmentService) {}

    public function store(EnrollRequest $request)
    {
        $user = $request->user('api');
        $courseId = (int) $request->validated('course_id');

        $enrollment = $this->enrollmentService->enroll($user->id, $courseId);

        return response()->json([
            'message' => 'Enrollment created successfully.',
            'enrollment' => $enrollment,
        ], 201);
    }
}
