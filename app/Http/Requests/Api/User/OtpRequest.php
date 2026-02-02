<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiFormRequest;

class OtpRequest extends ApiFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'phone' => 'required|string|min:10|max:15',
            'otp' => 'required|string|size:4',
        ];
    }
}
