<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test: Successful order creation with stock reduction.
     *
     * Questo test verifica che:
     * - Un ordine può essere creato correttamente
     * - Il numero ordine viene generato automaticamente
     * - La giacenza dei prodotti viene scalata
     * - Il totale dell'ordine viene calcolato correttamente
     */
    public function test_order_creation_reduces_product_stock(): void
    {
        // Arrange: Prepara i dati di test
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 10.00,
            'stock_quantity' => 100
        ]);

        // Act: Esegui l'azione (crea ordine)
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', [
                'customer_id' => $customer->id,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 5
                    ]
                ]
            ]);

        // Assert: Verifica i risultati
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'order_number',
                    'customer_id',
                    'total_amount',
                    'status',
                    'items'
                ]
            ]);

        // Verifica che il numero ordine sia nel formato corretto
        $orderNumber = $response->json('data.order_number');
        $this->assertMatchesRegularExpression('/^ORD-\d{4}-\d{4}$/', $orderNumber);

        // Verifica che il totale sia corretto (5 * 10.00 = 50.00)
        $this->assertEquals(50.00, $response->json('data.total_amount'));

        // Verifica che lo stato iniziale sia "in_attesa"
        $this->assertEquals('in_attesa', $response->json('data.status'));

        // Verifica che la giacenza sia stata scalata (100 - 5 = 95)
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => 95
        ]);

        // Verifica che l'ordine sia stato salvato nel database
        $this->assertDatabaseHas('orders', [
            'customer_id' => $customer->id,
            'total_amount' => 50.00,
            'status' => 'in_attesa'
        ]);
    }

    /**
     * Test: Order creation fails when insufficient stock.
     *
     * Questo test verifica che:
     * - Un ordine NON può essere creato se la giacenza è insufficiente
     * - La giacenza NON viene modificata in caso di errore
     * - Viene restituito un errore appropriato
     */
    public function test_order_creation_fails_with_insufficient_stock(): void
    {
        // Arrange
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Limited Product',
            'price' => 20.00,
            'stock_quantity' => 3  // Solo 3 pezzi disponibili
        ]);

        $initialStock = $product->stock_quantity;

        // Act: Tenta di ordinare 5 pezzi (più del disponibile)
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', [
                'customer_id' => $customer->id,
                'items' => [
                    [
                        'product_id' => $product->id,
                        'quantity' => 5  // Quantità maggiore dello stock
                    ]
                ]
            ]);

        // Assert: Verifica che l'operazione fallisca
        $response->assertStatus(422)
            ->assertJsonFragment([
                'message' => 'Stock insufficiente per Limited Product. Disponibili: 3, Richiesti: 5'
            ]);

        // Verifica che la giacenza NON sia stata modificata
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock_quantity' => $initialStock
        ]);

        // Verifica che NESSUN ordine sia stato creato
        $this->assertDatabaseCount('orders', 0);
    }

    /**
     * Test: Order creation with multiple products.
     *
     * Questo test verifica che:
     * - Un ordine può contenere più prodotti
     * - Il totale viene calcolato correttamente per più prodotti
     * - Tutte le giacenze vengono scalate correttamente
     */
    public function test_order_creation_with_multiple_products(): void
    {
        // Arrange
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $product1 = Product::factory()->create([
            'price' => 10.00,
            'stock_quantity' => 100
        ]);

        $product2 = Product::factory()->create([
            'price' => 25.50,
            'stock_quantity' => 50
        ]);

        // Act: Crea ordine con 2 prodotti diversi
        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/orders', [
                'customer_id' => $customer->id,
                'items' => [
                    [
                        'product_id' => $product1->id,
                        'quantity' => 3  // 3 * 10.00 = 30.00
                    ],
                    [
                        'product_id' => $product2->id,
                        'quantity' => 2  // 2 * 25.50 = 51.00
                    ]
                ]
            ]);

        // Assert
        $response->assertStatus(201);

        // Verifica totale: 30.00 + 51.00 = 81.00
        $this->assertEquals(81.00, $response->json('data.total_amount'));

        // Verifica giacenze scalate
        $this->assertDatabaseHas('products', [
            'id' => $product1->id,
            'stock_quantity' => 97  // 100 - 3
        ]);

        $this->assertDatabaseHas('products', [
            'id' => $product2->id,
            'stock_quantity' => 48  // 50 - 2
        ]);

        // Verifica che ci siano 2 righe ordine
        $this->assertDatabaseCount('order_items', 2);
    }
}
