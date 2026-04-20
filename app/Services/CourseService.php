<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\CourseRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CourseService
{
    public function __construct(private readonly CourseRepository $courseRepository) {}

    public function listCourses(int $perPage = 10): LengthAwarePaginator
    {
        return $this->courseRepository->paginateWithFilters($perPage);
    }

    public function createCourse(array $data): Course
    {
        return $this->courseRepository->create($data);
    }
}
