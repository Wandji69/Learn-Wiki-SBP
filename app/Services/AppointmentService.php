<?php

namespace App\Services;

use App\Models\Appointment;
use App\Repositories\AppointmentRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AppointmentService
{
    public function __construct(private readonly AppointmentRepository $appointmentRepository) {}

    public function listForUser(int $userId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->appointmentRepository->paginateForUser($userId, $perPage);
    }

    public function createForUser(int $userId, array $data): Appointment
    {
        return $this->appointmentRepository->create([
            ...$data,
            'user_id' => $userId,
        ]);
    }
}
