<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class CourseRepository
{
    public function paginateWithFilters(int $perPage = 10): LengthAwarePaginator
    {
        return QueryBuilder::for(Course::class)
            ->allowedFilters([
                AllowedFilter::partial('title'),
            ])
            ->allowedSorts(['id', 'title', 'created_at'])
            ->defaultSort('-created_at')
            ->paginate($perPage)
            ->appends(request()->query());
    }

    public function create(array $data): Course
    {
        return Course::create($data);
    }
}
