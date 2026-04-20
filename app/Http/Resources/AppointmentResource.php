<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'practitioner_name' => $this->practitioner_name,
            'appointment_at' => $this->appointment_at,
            'time_slot' => $this->appointment_at?->format('H:i').' - '.$this->appointment_at?->copy()->addMinutes(30)->format('H:i'),
            'status' => $this->status,
            'notes' => $this->notes,
            'user_name' => $this->user?->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
