<?php

namespace App\Http\Requests\Api\User;

use App\Http\Requests\Api\ApiFormRequest;

class UserRequest extends ApiFormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'phone' => 'required|string|min:10|max:15|unique:users',
            'password' => 'required|min:8'
        ];
    }
}
