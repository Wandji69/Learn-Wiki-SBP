<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CourseRepository
{
    public function paginateWithFilters(int $perPage = 10): LengthAwarePaginator
    {
        return QueryBuilder::for(Course::class)
            ->with(['modules.lessons.topics.contents'])
            ->withCount('users')
            ->allowedFilters(
                AllowedFilter::partial('title'),
            )
            ->allowedSorts('id', 'title', 'created_at')
            ->defaultSort('-created_at')
            ->paginate($perPage)
            ->appends(request()->query());
    }

    public function create(array $data): Course
    {
        return DB::transaction(function () use ($data) {
            $courseData = collect($data)->except('modules')->toArray();
            $course = Course::create($courseData);

            if (isset($data['modules'])) {
                foreach ($data['modules'] as $moduleData) {
                    $module = $course->modules()->create(collect($moduleData)->except('lessons')->toArray());

                    if (isset($moduleData['lessons'])) {
                        foreach ($moduleData['lessons'] as $lessonData) {
                            $lesson = $module->lessons()->create(collect($lessonData)->except('topics')->toArray());

                            if (isset($lessonData['topics'])) {
                                foreach ($lessonData['topics'] as $topicData) {
                                    $topic = $lesson->topics()->create(collect($topicData)->except('contents')->toArray());

                                    if (isset($topicData['contents'])) {
                                        foreach ($topicData['contents'] as $contentData) {
                                            $topic->contents()->create($contentData);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            return $course->load('modules.lessons.topics.contents');
        });
    }
}
