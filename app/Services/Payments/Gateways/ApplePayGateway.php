<?php
namespace App\Services\Payments\Gateways;

use App\Enums\PaymentStatus;
use App\Services\Payments\PaymentGatewayInterface;
use App\Models\Order;

class ApplePayGateway implements PaymentGatewayInterface
{
    public function initiate(Order $order): array
    {
        return [
            'status' => PaymentStatus::SUCCESSFUL,
            'reference' => 'APPLE-' . uniqid(),
        ];
    }
}
