<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::findOrCreate('admin', 'api');
        Role::findOrCreate('user', 'api');

        $admin = User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin User',
            'password' => Hash::make('password'),
        ]);

        $admin->assignRole('admin');

        $user = User::updateOrCreate([
            'email' => 'test@example.com',
        ], [
            'name' => 'Test User',
            'password' => Hash::make('password'),
        ]);

        $user->assignRole('user');

        $this->call([
            CourseSeeder::class,
            AppointmentSeeder::class,
        ]);
    }
}
