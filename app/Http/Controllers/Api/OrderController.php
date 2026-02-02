<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Order\OrderRequest;
use App\Http\Requests\Api\Order\StatusRequest;
use App\Http\Resources\OrderResource;
use App\Models\InventoryReservation;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Services\Payments\PaymentManager;
use App\Traits\ApiResponse;
use Illuminate\Http\Request;
use DB;

class OrderController extends Controller
{
    use ApiResponse;

    public function list(Request $request)
    {
        $orders = Order::query()->where('user_id', auth('api')->id())
        ->with(['items', 'latestTransaction'])
        ->when($request->filled('order_status'), function ($q) use ($request) {
            $q->where('status', $request->order_status);
        })
        ->when($request->filled('payment_status'), function ($q) use ($request) {
            $q->whereHas('latestTransaction', function ($p) use ($request) {
                $p->where('status', $request->payment_status);
            });
        })
        ->latest()
        ->paginate(10);

        $response = OrderResource::collection($orders)->response()->getData(true);

        return $this->success('Success', $response);
    }

    public function checkout(OrderRequest $request, PaymentManager $paymentManager)
    {

        DB::beginTransaction();

        try {

            $order = Order::create([
                'user_id' => auth('api')->id(),
                'status'  => OrderStatus::PENDING->value,
                'total'   => 0,
            ]);

            $total = 0;

            foreach ($request->items as $item) {
                $order->items()->create($item);
                $total += $item['quantity'] * $item['price'];

                InventoryReservation::create([
                    'order_id' => $order->id,
                    'quantity' => $item['quantity'],
                    'expires_at' => now()->addMinutes(15),
                ]);
            }

            $order->update(['total' => $total]);

            $method  = PaymentMethod::find($request->payment_method_id);
            $gateway = $paymentManager->resolve($method);

            $paymentResult = $gateway->initiate($order);

            $order->transactions()->create([
                'payment_method_id' => $method->id,
                'status' => 'pending',
                'external_reference' => $paymentResult['reference'] ?? null,
            ]);

            DB::commit();

            return $this->success('Checkout initiated', [
                'order_id' => $order->id,
                'payment'  => $paymentResult,
            ]);

        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->error('Checkout failed', null, 500);
        }

    }

    public function successful(StatusRequest $request,)
    {

        $order = Order::findOrFail($request->order_id);

        DB::transaction(function () use ($order, $request) {

            $order->transactions()
                ->where('status', 'pending')
                ->latest()
                ->first()
                ?->update([
                    'status' => 'success',
                    'external_reference' => $request->reference,
                ]);

            $order->update([
                'status' => OrderStatus::CONFIRMED->value,
            ]);

            foreach ($order->reservations as $reservation) {
                $reservation->delete();
            }

        });

        $response = new OrderResource($order);

        return $this->success('Payment successful', $response);
    }

    public function failed(StatusRequest $request,)
    {

        $order = Order::findOrFail($request->order_id);
 
        DB::transaction(function () use ($order) {

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
        });

        $response = new OrderResource($order);

        return $this->success('Payment failed', $response);
    }

    public function destroy($id)
    {
        
        $order = Order::find($id);

        if (! $order) {
            return $this->error('Order not found', null, 404);
        }

        $latestTransaction = $order->latestTransaction;

        if ($latestTransaction && $latestTransaction->status === 'success') {
            return $this->error(
                'Paid orders cannot be deleted',
                null,
                409
            );
        }

        $order->delete();

        return $this->success(null, 'Order deleted');
    }
}
