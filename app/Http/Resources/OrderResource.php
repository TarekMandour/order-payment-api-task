<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'     => $this->id,
            'status' => $this->status,
            'total'  => $this->total,
            'items' => $this->items->map(function ($item) {
                return [
                    'product_name' => $item->product_name,
                    'quantity'     => $item->quantity,
                    'price'        => $item->price,
                ];
            }),
            'payment' => $this->latestTransaction ? [
                'status'    => $this->latestTransaction->status,
                'reference' => $this->latestTransaction->external_reference,
            ] : null,

            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}
