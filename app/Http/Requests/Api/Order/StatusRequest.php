<?php

namespace App\Http\Requests\Api\Order;

use App\Http\Requests\Api\ApiFormRequest;

class StatusRequest extends ApiFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'order_id'  => 'required|exists:orders,id',
            'reference' => 'nullable|string',
        ];
    }
}
