<?php

namespace Database\Seeders;

use App\Models\Appointment;
use App\Models\User;
use Illuminate\Database\Seeder;

class AppointmentSeeder extends Seeder
{
    /**
     * Seed the application's database with sample appointments.
     */
    public function run(): void
    {
        $practitioners = [
            'Dr. Smith',
            'Dr. Johnson',
            'Dr. Patel',
            'Dr. Williams',
            'Dr. Chen',
        ];

        $statuses = ['booked', 'completed', 'cancelled'];

        $notesPool = [
            'Routine check-up and blood pressure monitoring.',
            'Follow-up on previous lab results.',
            'Annual physical examination.',
            'Discuss new medication options.',
            'Post-surgery recovery assessment.',
            'Mental health counselling session.',
            'Dietary consultation and meal planning review.',
            'Chronic pain management follow-up.',
            'Vaccination appointment.',
            'Dermatology referral consultation.',
            null,
            null,
        ];

        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        if ($users->isEmpty()) {
            $users = User::all();
        }

        foreach ($users as $user) {
            $count = rand(3, 5);

            for ($i = 0; $i < $count; $i++) {
                $daysOffset = rand(-30, 30);
                $hour = rand(8, 17);
                $minute = [0, 15, 30, 45][rand(0, 3)];

                $appointmentAt = now()
                    ->addDays($daysOffset)
                    ->setTime($hour, $minute);

                if ($daysOffset < 0) {
                    $status = $statuses[array_rand(['completed', 'completed', 'cancelled'])];
                    $status = rand(1, 10) <= 7 ? 'completed' : 'cancelled';
                } else {
                    $status = rand(1, 10) <= 8 ? 'booked' : 'cancelled';
                }

                Appointment::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'appointment_at' => $appointmentAt,
                    ],
                    [
                        'practitioner_name' => $practitioners[array_rand($practitioners)],
                        'status' => $status,
                        'notes' => $notesPool[array_rand($notesPool)],
                    ],
                );
            }
        }
    }
}
