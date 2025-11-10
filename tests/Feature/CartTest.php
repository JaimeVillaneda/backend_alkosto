<?php

namespace Tests\Feature;

use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->product = Product::factory()->create(['stock' => 10, 'price' => 99.99]);
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_acceder_al_carrito()
    {
        $response = $this->getJson('/api/cart');
        $response->assertStatus(401);
    }

    /** @test */
    public function puede_obtener_carrito_vacio()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->getJson('/api/cart');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'items' => [],
                         'total' => 0,
                         'items_count' => 0
                     ]
                 ]);
    }

    /** @test */
    public function puede_agregar_producto_al_carrito()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/cart', [
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'quantity' => 2,
                         'unit_price' => 99.99
                     ]
                 ]);

        $this->assertDatabaseHas('carts', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);
    }

    /** @test */
    public function no_puede_agregar_producto_sin_stock()
    {
        $this->actingAs($this->user, 'sanctum');

        $productWithoutStock = Product::factory()->create(['stock' => 0]);

        $response = $this->postJson('/api/cart', [
            'product_id' => $productWithoutStock->id,
            'quantity' => 1
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Stock insuficiente. Stock disponible: 0'
                 ]);
    }

    /** @test */
    public function puede_actualizar_cantidad_en_carrito()
    {
        $this->actingAs($this->user, 'sanctum');

        $cartItem = Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => $this->product->price
        ]);

        $response = $this->putJson("/api/cart/{$cartItem->id}", [
            'product_id' => $this->product->id,
            'quantity' => 3
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'quantity' => 3
                     ]
                 ]);
    }

    /** @test */
    public function puede_eliminar_producto_del_carrito()
    {
        $this->actingAs($this->user, 'sanctum');

        $cartItem = Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => $this->product->price
        ]);

        $response = $this->deleteJson("/api/cart/{$cartItem->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Producto eliminado del carrito exitosamente.'
                 ]);

        $this->assertDatabaseMissing('carts', ['id' => $cartItem->id]);
    }

    /** @test */
    public function puede_vaciar_carrito()
    {
        $this->actingAs($this->user, 'sanctum');

        Cart::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson('/api/cart');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Carrito vaciado exitosamente.'
                 ]);

        $this->assertEquals(0, Cart::where('user_id', $this->user->id)->count());
    }

    /** @test */
    public function no_puede_modificar_carrito_de_otro_usuario()
    {
        $this->actingAs($this->user, 'sanctum');

        $otherUser = User::factory()->create();
        $cartItem = Cart::create([
            'user_id' => $otherUser->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => $this->product->price
        ]);

        $response = $this->putJson("/api/cart/{$cartItem->id}", [
            'product_id' => $this->product->id,
            'quantity' => 5
        ]);

        $response->assertStatus(403);
    }
}