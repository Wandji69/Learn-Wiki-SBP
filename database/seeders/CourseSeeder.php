<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    /**
     * Seed the application's database with sample courses and enroll existing users.
     */
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Introduction to Mental Health',
                'description' => 'A foundational course covering the basics of mental health awareness, common conditions, and self-care strategies for everyday well-being.',
                'duration_minutes' => 120,
            ],
            [
                'title' => 'Nutrition & Balanced Living',
                'description' => 'Learn how to build a balanced diet, understand macronutrients and micronutrients, and develop healthy eating habits that support long-term wellness.',
                'duration_minutes' => 90,
            ],
            [
                'title' => 'Stress Management Techniques',
                'description' => 'Explore evidence-based techniques for managing stress, including mindfulness, breathing exercises, progressive muscle relaxation, and time management.',
                'duration_minutes' => 75,
            ],
            [
                'title' => 'First Aid Fundamentals',
                'description' => 'Gain essential first aid skills covering wound care, CPR basics, choking response, and how to handle common medical emergencies before professional help arrives.',
                'duration_minutes' => 150,
            ],
            [
                'title' => 'Understanding Chronic Illness',
                'description' => 'An in-depth look at managing chronic conditions such as diabetes, hypertension, and asthma, including medication adherence, lifestyle modifications, and support resources.',
                'duration_minutes' => 180,
            ],
            [
                'title' => 'Sleep Science & Hygiene',
                'description' => 'Discover the science behind sleep cycles, the impact of sleep deprivation, and actionable tips for improving sleep quality and establishing a healthy bedtime routine.',
                'duration_minutes' => 60,
            ],
            [
                'title' => 'Physical Fitness for Beginners',
                'description' => 'A beginner-friendly guide to starting a fitness journey with safe warm-up routines, strength training basics, cardiovascular exercises, and recovery practices.',
                'duration_minutes' => 100,
            ],
            [
                'title' => 'Preventive Health Screenings',
                'description' => 'Understand the importance of regular health screenings, what tests to expect at different life stages, and how early detection can save lives.',
                'duration_minutes' => 45,
            ],
        ];

        $createdCourses = [];

        foreach ($courses as $courseData) {
            $createdCourses[] = Course::updateOrCreate(
                ['title' => $courseData['title']],
                $courseData,
            );
        }

        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->get();

        foreach ($users as $user) {
            $randomCourses = collect($createdCourses)->random(min(3, count($createdCourses)));

            foreach ($randomCourses as $course) {
                $user->courses()->syncWithoutDetaching($course->id);
            }
        }
    }
}
