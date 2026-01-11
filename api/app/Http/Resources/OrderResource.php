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
            'id' => $this->id,
            'order_number' => $this->order_number,
            'customer_id' => $this->customer_id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'customer_name' => $this->getCustomerName(),
            'date' => $this->date?->format('Y-m-d'),
            'total_amount' => $this->total_amount,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'status_color' => $this->status->color(),
            'can_be_modified' => $this->canBeModified(),
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }

    /**
     * Get customer name or "Cliente eliminato" if soft deleted.
     */
    private function getCustomerName(): string
    {
        if (!$this->customer) {
            return 'Cliente eliminato';
        }

        return $this->customer->deleted_at
            ? 'Cliente eliminato'
            : $this->customer->name;
    }
}
