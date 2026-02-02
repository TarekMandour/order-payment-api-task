<?php
namespace App\Services\Payments;

use App\Models\PaymentMethod;
use App\Services\Payments\Gateways\ApplePayGateway;
use App\Services\Payments\Gateways\CreditCardGateway;
use Exception;

class PaymentManager
{
    public function resolve(PaymentMethod $method): PaymentGatewayInterface
    {
        return match ($method->code) {
            'apple_pay'   => new ApplePayGateway(),
            'credit_card' => new CreditCardGateway(),
            default       => throw new Exception('Unsupported method'),
        };
    }
}

