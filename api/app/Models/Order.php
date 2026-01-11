<?php

namespace App\Models;

use App\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_id',
        'date',
        'total_amount',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'total_amount' => 'decimal:2',
        'status' => OrderStatus::class,
    ];

    /**
     * Get the customer that owns the order.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    /**
     * Get all items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if order can be modified.
     */
    public function canBeModified(): bool
    {
        return !in_array($this->status, [
            OrderStatus::COMPLETATO,
            OrderStatus::ANNULLATO,
        ]);
    }

    /**
     * Check if status transition is valid.
     */
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        $validTransitions = [
            OrderStatus::IN_ATTESA->value => [
                OrderStatus::IN_LAVORAZIONE->value,
                OrderStatus::ANNULLATO->value,
            ],
            OrderStatus::IN_LAVORAZIONE->value => [
                OrderStatus::COMPLETATO->value,
                OrderStatus::ANNULLATO->value,
            ],
            OrderStatus::COMPLETATO->value => [],
            OrderStatus::ANNULLATO->value => [],
        ];

        return in_array(
            $newStatus->value,
            $validTransitions[$this->status->value] ?? []
        );
    }
}
