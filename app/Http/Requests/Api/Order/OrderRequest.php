<?php

namespace App\Http\Requests\Api\Order;

use App\Http\Requests\Api\ApiFormRequest;

class OrderRequest extends ApiFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'items' => ['required','array','min:1'],
            'items.*.product_name' => ['required','string'],
            'items.*.quantity' => ['required','integer','min:1'],
            'items.*.price' => ['required','numeric','min:0'],
            'payment_method_id' => ['required','exists:payment_methods,id'],
        ];
    }
}
