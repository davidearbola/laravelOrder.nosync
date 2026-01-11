<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer')->id;

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', "unique:customers,email,{$customerId}"],
            'address' => ['sometimes', 'required', 'string', 'max:500'],
            'phone' => ['sometimes', 'required', 'string', 'max:20'],
        ];
    }
}
