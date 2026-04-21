<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function register(array $data): array
    {
        $user = $this->userRepository->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        Role::findOrCreate('user', 'api');
        $user->assignRole('user');

        $token = JWTAuth::fromUser($user);

        return [$token, $this->userRepository->withRoles($user)];
    }

    public function login(array $credentials): ?array
    {
        if (! $token = auth('api')->attempt($credentials)) {
            return null;
        }

        /** @var User $user */
        $user = auth('api')->user();

        return [$token, $this->userRepository->withRoles($user)];
    }
}
