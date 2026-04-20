<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Resources\CourseResource;
use App\Services\CourseService;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private readonly CourseService $courseService) {}

    public function index(Request $request)
    {
        $courses = $this->courseService->listCourses((int) $request->integer('per_page', 10));

        return CourseResource::collection($courses);
    }

    public function store(StoreCourseRequest $request)
    {
        $course = $this->courseService->createCourse($request->validated());

        return CourseResource::make($course)
            ->response()
            ->setStatusCode(201);
    }
}
