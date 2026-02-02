<?php

namespace Tests\Traits;

use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

trait JwtTestHelper
{
    protected function authenticateWithJwt(?User $user = null): array
    {
        $user = $user ?: User::factory()->create();

        $token = JWTAuth::fromUser($user);

        return [
            'user' => $user,
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ],
        ];
    }
}
