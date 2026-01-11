<?php

namespace App\Http\Requests\Order;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['sometimes', 'required', 'exists:customers,id'],
            'status' => ['sometimes', Rule::enum(OrderStatus::class)],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $order = $this->route('order');

            // Check if order can be modified
            if (!$order->canBeModified()) {
                $validator->errors()->add(
                    'order',
                    'Non è possibile modificare un ordine completato o annullato'
                );
                return;
            }

            // Check status transition validity
            if ($this->has('status')) {
                $newStatus = OrderStatus::from($this->status);

                if (!$order->canTransitionTo($newStatus)) {
                    $validator->errors()->add(
                        'status',
                        "Transizione non valida da {$order->status->value} a {$newStatus->value}"
                    );
                }
            }
        });
    }
}
