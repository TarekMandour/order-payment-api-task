<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Tests\TestCase;
use App\Models\Order;
use App\Models\InventoryReservation;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentResultServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_marks_order_as_paid_on_success()
    {
        $order = Order::factory()->create([
            'status' => 'payment_pending',
        ]);

        $payment = Transaction::factory()->create([
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        $order->transactions()
                ->where('status', 'pending')
                ->latest()
                ->first()
                ?->update([
                    'status' => 'success',
                    'external_reference' => '2135456578777',
                ]);

        $order->update([
            'status' => OrderStatus::CONFIRMED->value,
        ]);

        foreach ($order->reservations as $reservation) {
            $reservation->delete();
        }

        $this->assertEquals('confirmed', $order->fresh()->status);
        $this->assertEquals('success', $payment->fresh()->status);
        $this->assertEquals('2135456578777', $payment->fresh()->external_reference);
    }

    /** @test */
    public function it_releases_inventory_on_payment_failure()
    {

        $order = Order::factory()->create([
            'status' => 'payment_pending',
        ]);

        $paymentMethod = PaymentMethod::factory()->create();

        Transaction::factory()->create([
            'order_id' => $order->id,
            'payment_method_id' => $paymentMethod->id,
            'status' => 'pending',
        ]);

        InventoryReservation::factory()->create([
            'order_id' => $order->id,
            'quantity' => 3,
        ]);

       $order->transactions()
                ->where('status', 'pending')
                ->latest()
                ->first()
                ?->update([
                    'status' => 'failed',
                ]);

        $order->update([
            'status' => OrderStatus::CANCELLED->value,
        ]);

        foreach ($order->reservations as $reservation) {
            $reservation->delete();
        }

        $this->assertEquals('cancelled', $order->fresh()->status);
        $this->assertDatabaseMissing('inventory_reservations', [
            'order_id' => $order->id,
        ]);
    }
}
