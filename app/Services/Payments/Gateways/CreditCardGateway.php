<?php
namespace App\Services\Payments\Gateways;

use App\Enums\PaymentStatus;
use App\Services\Payments\PaymentGatewayInterface;
use App\Models\Order;

class CreditCardGateway implements PaymentGatewayInterface
{
    public function initiate(Order $order): array
    {
        return [
            'payment_url' => 'https://myfatoorah-gateway.test/pay/' . uniqid(),
        ];
    }

    public function handleCallback(array $payload): void {}

}
