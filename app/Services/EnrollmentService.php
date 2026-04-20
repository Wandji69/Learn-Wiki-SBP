<?php

namespace App\Services;

use App\Models\Enrollment;
use App\Repositories\EnrollmentRepository;
use Illuminate\Validation\ValidationException;

class EnrollmentService
{
    public function __construct(private readonly EnrollmentRepository $enrollmentRepository) {}

    public function enroll(int $userId, int $courseId): Enrollment
    {
        if ($this->enrollmentRepository->existsForUserAndCourse($userId, $courseId)) {
            throw ValidationException::withMessages([
                'course_id' => ['You are already enrolled in this course.'],
            ]);
        }

        return $this->enrollmentRepository->create([
            'user_id' => $userId,
            'course_id' => $courseId,
        ]);
    }
}
