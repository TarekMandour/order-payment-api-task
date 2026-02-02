<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'phone' => '201008233821',
        ]);

        PaymentMethod::insert([
            [
                'code' => 'apple_pay',
                'name' => 'Apple Pay',
                'flow' => 'native',
            ],
            [
                'code' => 'credit_card',
                'name' => 'Credit Card',
                'flow' => 'redirect',
            ],
        ]);
    }
}
