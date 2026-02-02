<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Traits\JwtTestHelper;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\PaymentMethod;

class CheckoutTest extends TestCase
{
    use RefreshDatabase, JwtTestHelper;

    /** @test */
    public function user_can_checkout_using_apple_pay()
    {
        $auth = $this->authenticateWithJwt();

        $paymentMethod = PaymentMethod::factory()->create([
            'code' => 'apple_pay',
            'is_active' => true,
        ]);

        $response = $this->postJson(
            '/api/order/checkout',
            [
                'items' => [
                    [
                        'product_name' => 'gold',
                        'quantity'   => 2,
                        'price'      => 100,
                    ],
                ],
                'payment_method_id' => $paymentMethod->id,
            ],
            $auth['headers']
        );

        $response
            ->assertStatus(200)
            ->assertJsonPath('status', null)
            ->assertJsonPath('data.order_id', fn ($id) => !empty($id));
    }

    /** @test */
    public function credit_card_checkout_returns_payment_url()
    {
        $auth = $this->authenticateWithJwt();


        $paymentMethod = PaymentMethod::factory()->create([
            'code' => 'credit_card',
            'is_active' => true,
        ]);

        $response = $this->postJson(
            '/api/order/checkout',
            [
                'items' => [
                    [
                        'product_name' => 'silver',
                        'quantity'   => 1,
                        'price'      => 50,
                    ],
                ],
                'payment_method_id' => $paymentMethod->id,
            ],
            $auth['headers']
        );

        $response
            ->assertStatus(200)
            ->assertJsonPath('status', null)
            ->assertJsonPath(
                'data.payment.payment_url',
                fn ($url) => is_string($url)
            );
    }
}
