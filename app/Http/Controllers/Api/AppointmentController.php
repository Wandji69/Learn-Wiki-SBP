<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Resources\AppointmentResource;
use App\Services\AppointmentService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function __construct(private readonly AppointmentService $appointmentService) {}

    public function index(Request $request)
    {
        $user = $request->user('api');
        $perPage = (int) $request->integer('per_page', 10);

        $appointments = $user->hasRole('admin')
            ? $this->appointmentService->listAll($perPage)
            : $this->appointmentService->listForUser($user->id, $perPage);

        return AppointmentResource::collection($appointments);
    }

    public function store(StoreAppointmentRequest $request)
    {
        $appointment = $this->appointmentService->createForUser(
            $request->user('api')->id,
            $request->validated()
        );

        return AppointmentResource::make($appointment)
            ->response()
            ->setStatusCode(201);
    }
}
