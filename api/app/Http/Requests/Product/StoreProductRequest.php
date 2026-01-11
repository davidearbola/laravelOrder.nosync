<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'unique:products,code', 'max:50'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Il codice prodotto è obbligatorio',
            'code.unique' => 'Questo codice prodotto esiste già',
            'name.required' => 'Il nome è obbligatorio',
            'price.required' => 'Il prezzo è obbligatorio',
            'price.min' => 'Il prezzo deve essere positivo',
            'stock_quantity.required' => 'La giacenza è obbligatoria',
            'stock_quantity.min' => 'La giacenza deve essere positiva',
        ];
    }
}
