<?php

namespace Tests\Unit;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderStatusTransitionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Valid status transitions are allowed.
     *
     * Questo test verifica che le transizioni di stato VALIDE
     * siano permesse secondo la matrice definita nel modello Order.
     */
    public function test_valid_status_transitions_are_allowed(): void
    {
        // Test 1: IN_ATTESA -> IN_LAVORAZIONE (valida)
        $order = new Order(['status' => OrderStatus::IN_ATTESA]);
        $this->assertTrue(
            $order->canTransitionTo(OrderStatus::IN_LAVORAZIONE),
            'Dovrebbe essere possibile passare da IN_ATTESA a IN_LAVORAZIONE'
        );

        // Test 2: IN_ATTESA -> ANNULLATO (valida)
        $order = new Order(['status' => OrderStatus::IN_ATTESA]);
        $this->assertTrue(
            $order->canTransitionTo(OrderStatus::ANNULLATO),
            'Dovrebbe essere possibile annullare un ordine IN_ATTESA'
        );

        // Test 3: IN_LAVORAZIONE -> COMPLETATO (valida)
        $order = new Order(['status' => OrderStatus::IN_LAVORAZIONE]);
        $this->assertTrue(
            $order->canTransitionTo(OrderStatus::COMPLETATO),
            'Dovrebbe essere possibile completare un ordine IN_LAVORAZIONE'
        );

        // Test 4: IN_LAVORAZIONE -> ANNULLATO (valida)
        $order = new Order(['status' => OrderStatus::IN_LAVORAZIONE]);
        $this->assertTrue(
            $order->canTransitionTo(OrderStatus::ANNULLATO),
            'Dovrebbe essere possibile annullare un ordine IN_LAVORAZIONE'
        );
    }

    /**
     * Test: Invalid status transitions are blocked.
     *
     * Questo test verifica che le transizioni di stato INVALIDE
     * vengano bloccate correttamente.
     */
    public function test_invalid_status_transitions_are_blocked(): void
    {
        // Test 1: COMPLETATO -> IN_LAVORAZIONE (invalida)
        $order = new Order(['status' => OrderStatus::COMPLETATO]);
        $this->assertFalse(
            $order->canTransitionTo(OrderStatus::IN_LAVORAZIONE),
            'NON dovrebbe essere possibile tornare da COMPLETATO a IN_LAVORAZIONE'
        );

        // Test 2: COMPLETATO -> ANNULLATO (invalida)
        $order = new Order(['status' => OrderStatus::COMPLETATO]);
        $this->assertFalse(
            $order->canTransitionTo(OrderStatus::ANNULLATO),
            'NON dovrebbe essere possibile annullare un ordine COMPLETATO'
        );

        // Test 3: ANNULLATO -> IN_LAVORAZIONE (invalida)
        $order = new Order(['status' => OrderStatus::ANNULLATO]);
        $this->assertFalse(
            $order->canTransitionTo(OrderStatus::IN_LAVORAZIONE),
            'NON dovrebbe essere possibile riattivare un ordine ANNULLATO'
        );

        // Test 4: IN_ATTESA -> COMPLETATO (invalida - deve passare per IN_LAVORAZIONE)
        $order = new Order(['status' => OrderStatus::IN_ATTESA]);
        $this->assertFalse(
            $order->canTransitionTo(OrderStatus::COMPLETATO),
            'NON dovrebbe essere possibile completare direttamente un ordine IN_ATTESA'
        );
    }

    /**
     * Test: Completed and cancelled orders cannot be modified.
     *
     * Questo test verifica che ordini completati o annullati
     * NON possano essere modificati.
     */
    public function test_completed_orders_cannot_be_modified(): void
    {
        // Ordine completato NON può essere modificato
        $completedOrder = new Order(['status' => OrderStatus::COMPLETATO]);
        $this->assertFalse(
            $completedOrder->canBeModified(),
            'Un ordine COMPLETATO NON dovrebbe essere modificabile'
        );

        // Ordine annullato NON può essere modificato
        $cancelledOrder = new Order(['status' => OrderStatus::ANNULLATO]);
        $this->assertFalse(
            $cancelledOrder->canBeModified(),
            'Un ordine ANNULLATO NON dovrebbe essere modificabile'
        );
    }

    /**
     * Test: Pending and processing orders can be modified.
     *
     * Questo test verifica che ordini in attesa o in lavorazione
     * POSSANO essere modificati.
     */
    public function test_pending_and_processing_orders_can_be_modified(): void
    {
        // Ordine in attesa PUÒ essere modificato
        $pendingOrder = new Order(['status' => OrderStatus::IN_ATTESA]);
        $this->assertTrue(
            $pendingOrder->canBeModified(),
            'Un ordine IN_ATTESA dovrebbe essere modificabile'
        );

        // Ordine in lavorazione PUÒ essere modificato
        $processingOrder = new Order(['status' => OrderStatus::IN_LAVORAZIONE]);
        $this->assertTrue(
            $processingOrder->canBeModified(),
            'Un ordine IN_LAVORAZIONE dovrebbe essere modificabile'
        );
    }

    /**
     * Test: OrderStatus enum provides correct labels.
     *
     * Questo test verifica che l'enum OrderStatus ritorni
     * le etichette corrette per ogni stato.
     */
    public function test_order_status_enum_labels(): void
    {
        $this->assertEquals('In Attesa', OrderStatus::IN_ATTESA->label());
        $this->assertEquals('In Lavorazione', OrderStatus::IN_LAVORAZIONE->label());
        $this->assertEquals('Completato', OrderStatus::COMPLETATO->label());
        $this->assertEquals('Annullato', OrderStatus::ANNULLATO->label());
    }

    /**
     * Test: OrderStatus enum provides correct colors.
     *
     * Questo test verifica che l'enum OrderStatus ritorni
     * i colori corretti per la visualizzazione nel frontend.
     */
    public function test_order_status_enum_colors(): void
    {
        $this->assertEquals('gray', OrderStatus::IN_ATTESA->color());
        $this->assertEquals('blue', OrderStatus::IN_LAVORAZIONE->color());
        $this->assertEquals('green', OrderStatus::COMPLETATO->color());
        $this->assertEquals('red', OrderStatus::ANNULLATO->color());
    }
}
