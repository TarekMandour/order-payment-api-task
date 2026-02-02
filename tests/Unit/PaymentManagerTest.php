<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\Payments\PaymentManager;
use App\Services\Payments\Gateways\ApplePayGateway;
use App\Services\Payments\Gateways\CreditCardGateway;
use App\Models\PaymentMethod;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentManagerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_resolves_apple_pay_gateway()
    {
        $method = PaymentMethod::factory()->create([
            'code' => 'apple_pay',
        ]);

        $manager = new PaymentManager();

        $gateway = $manager->resolve($method);

        $this->assertInstanceOf(ApplePayGateway::class, $gateway);
    }

    /** @test */
    public function it_resolves_credit_card_gateway()
    {
        $method = PaymentMethod::factory()->create([
            'code' => 'credit_card',
        ]);

        $manager = new PaymentManager();

        $gateway = $manager->resolve($method);

        $this->assertInstanceOf(CreditCardGateway::class, $gateway);
    }
}
