<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_acceder_a_productos()
    {
        $response = $this->getJson('/api/products');
        $response->assertStatus(401);
    }

    /** @test */
    public function puede_obtener_lista_de_productos()
    {
        $this->actingAs($this->user, 'sanctum');

        Product::factory()->count(3)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => [
                                 'id', 'name', 'sku', 'price', 'stock', 
                                 'is_active', 'category_id'
                             ]
                         ]
                     ],
                     'message'
                 ]);
    }

    /** @test */
    public function puede_crear_un_producto()
    {
        $this->actingAs($this->user, 'sanctum');

        $productData = [
            'name' => 'Nuevo Producto',
            'description' => 'Descripción de prueba',
            'price' => 99.99,
            'stock' => 10,
            'category_id' => $this->category->id,
            'is_active' => true
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Nuevo Producto',
                         'price' => 99.99
                     ]
                 ]);

        $this->assertDatabaseHas('products', ['name' => 'Nuevo Producto']);
    }

    /** @test */
    public function no_puede_crear_producto_con_sku_duplicado()
    {
        $this->actingAs($this->user, 'sanctum');

        $existingProduct = Product::factory()->create([
            'sku' => 'SKU-UNICO',
            'category_id' => $this->category->id
        ]);

        $response = $this->postJson('/api/products', [
            'name' => 'Producto Duplicado',
            'sku' => 'SKU-UNICO',
            'price' => 50.00,
            'stock' => 5,
            'category_id' => $this->category->id
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['sku']);
    }

    /** @test */
    public function puede_obtener_un_producto_especifico()
    {
        $this->actingAs($this->user, 'sanctum');

        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $product->id,
                         'name' => $product->name
                     ]
                 ]);
    }

    /** @test */
    public function puede_actualizar_un_producto()
    {
        $this->actingAs($this->user, 'sanctum');

        $product = Product::factory()->create([
            'name' => 'Producto Original',
            'category_id' => $this->category->id
        ]);

        $updatedData = [
            'name' => 'Producto Actualizado',
            'price' => 149.99,
            'stock' => 20,
            'category_id' => $this->category->id
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Producto Actualizado',
                         'price' => 149.99
                     ]
                 ]);

        $this->assertDatabaseHas('products', ['name' => 'Producto Actualizado']);
    }

    /** @test */
    public function puede_eliminar_un_producto()
    {
        $this->actingAs($this->user, 'sanctum');

        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Producto eliminado exitosamente.'
                 ]);

        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    /** @test */
    public function puede_restaurar_producto_eliminado()
    {
        $this->actingAs($this->user, 'sanctum');

        $product = Product::factory()->create(['category_id' => $this->category->id]);
        $product->delete();

        $response = $this->postJson("/api/products/{$product->id}/restore");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Producto restaurado exitosamente.'
                 ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function puede_obtener_productos_con_stock_bajo()
    {
        $this->actingAs($this->user, 'sanctum');

        Product::factory()->lowStock()->count(2)->create(['category_id' => $this->category->id]);
        Product::factory()->count(3)->create(['category_id' => $this->category->id]); // Stock normal

        $response = $this->getJson('/api/products-low-stock');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => ['id', 'name', 'stock', 'min_stock']
                     ]
                 ]);
    }

    /** @test */
    public function puede_filtrar_productos_por_categoria()
    {
        $this->actingAs($this->user, 'sanctum');

        $anotherCategory = Category::factory()->create();
        
        Product::factory()->count(2)->create(['category_id' => $this->category->id]);
        Product::factory()->count(1)->create(['category_id' => $anotherCategory->id]);

        $response = $this->getJson("/api/products?category_id={$this->category->id}");

        $response->assertStatus(200);
        
        $data = $response->json();
        foreach ($data['data']['data'] as $product) {
            $this->assertEquals($this->category->id, $product['category_id']);
        }
    }

    /** @test */
    public function puede_filtrar_productos_por_stock_bajo()
    {
        $this->actingAs($this->user, 'sanctum');

        Product::factory()->lowStock()->count(2)->create(['category_id' => $this->category->id]);
        Product::factory()->count(3)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/products?low_stock=true');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertLessThanOrEqual(2, count($data['data']['data']));
    }

    /** @test */
    public function puede_buscar_productos_por_nombre_o_sku()
    {
        $this->actingAs($this->user, 'sanctum');

        $product = Product::factory()->create([
            'name' => 'Producto Especial', 
            'sku' => 'SKU-ESPECIAL',
            'category_id' => $this->category->id
        ]);

        $response = $this->getJson('/api/products?search=Especial');

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Producto Especial']);
    }

    /** @test */
    public function validacion_funciona_correctamente_para_productos()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/products', [
            'description' => 'Sin nombre ni precio'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'price', 'stock', 'category_id']);
    }

    /** @test */
    public function no_puede_crear_producto_con_precio_menor_al_costo()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/products', [
            'name' => 'Producto con precio inválido',
            'price' => 50.00,
            'cost_price' => 60.00,
            'stock' => 10,
            'category_id' => $this->category->id
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['price']);
    }
}