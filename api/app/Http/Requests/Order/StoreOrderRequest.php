<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'exists:customers,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Il cliente è obbligatorio',
            'customer_id.exists' => 'Il cliente selezionato non esiste',
            'items.required' => 'Almeno un prodotto è obbligatorio',
            'items.*.product_id.required' => 'Il prodotto è obbligatorio',
            'items.*.product_id.exists' => 'Il prodotto selezionato non esiste',
            'items.*.quantity.required' => 'La quantità è obbligatoria',
            'items.*.quantity.min' => 'La quantità deve essere almeno 1',
        ];
    }
}
