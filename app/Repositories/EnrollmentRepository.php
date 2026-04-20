<?php

namespace App\Repositories;

use App\Models\Enrollment;
use Illuminate\Database\Eloquent\Collection;

class EnrollmentRepository
{
    public function existsForUserAndCourse(int $userId, int $courseId): bool
    {
        return Enrollment::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();
    }

    public function create(array $data): Enrollment
    {
        return Enrollment::create($data);
    }

    public function listForUser(int $userId): Collection
    {
        return Enrollment::query()
            ->where('user_id', $userId)
            ->with(['course.modules.lessons.topics.contents'])
            ->get();
    }
}
