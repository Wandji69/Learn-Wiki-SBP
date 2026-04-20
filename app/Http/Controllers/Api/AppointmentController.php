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
        $appointments = $this->appointmentService->listForUser(
            $request->user('api')->id,
            (int) $request->integer('per_page', 10)
        );

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
