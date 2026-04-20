<?php

namespace App\Repositories;

use App\Models\Appointment;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AppointmentRepository
{
    public function paginateAll(int $perPage = 10): LengthAwarePaginator
    {
        return QueryBuilder::for(Appointment::query()->with('user:id,name'))
            ->allowedFilters(
                AllowedFilter::partial('practitioner_name'),
                AllowedFilter::exact('status'),
            )
            ->allowedSorts('appointment_at', 'created_at')
            ->defaultSort('appointment_at')
            ->paginate($perPage)
            ->appends(request()->query());
    }

    public function paginateForUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return QueryBuilder::for(
            Appointment::query()->where('user_id', $userId)->with('user:id,name')
        )
            ->allowedFilters(
                AllowedFilter::partial('practitioner_name'),
                AllowedFilter::exact('status'),
            )
            ->allowedSorts('appointment_at', 'created_at')
            ->defaultSort('appointment_at')
            ->paginate($perPage)
            ->appends(request()->query());
    }

    public function create(array $data): Appointment
    {
        return Appointment::create($data);
    }
}
