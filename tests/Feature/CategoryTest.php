<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    /** @test */
    public function usuario_no_autenticado_no_puede_acceder_a_categorias()
    {
        $unauthenticatedResponse = $this->getJson('/api/categories');
        $unauthenticatedResponse->assertStatus(401);
    }

    /** @test */
    public function puede_obtener_lista_de_categorias()
    {
        $this->actingAs($this->user, 'sanctum');

        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => ['id', 'name', 'slug', 'description', 'is_active', 'parent_id']
                         ]
                     ],
                     'message'
                 ]);
    }

    /** @test */
    public function puede_crear_una_categoria()
    {
        $this->actingAs($this->user, 'sanctum');

        $categoryData = [
            'name' => 'Nueva Categoría',
            'description' => 'Descripción de prueba',
            'is_active' => true
        ];

        $response = $this->postJson('/api/categories', $categoryData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Nueva Categoría'
                     ]
                 ]);

        $this->assertDatabaseHas('categories', ['name' => 'Nueva Categoría']);
    }

    /** @test */
    public function no_puede_crear_categoria_con_nombre_duplicado()
    {
        $this->actingAs($this->user, 'sanctum');

        Category::factory()->create(['name' => 'Categoría Existente']);

        $response = $this->postJson('/api/categories', [
            'name' => 'Categoría Existente',
            'description' => 'Descripción de prueba'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function puede_obtener_una_categoria_especifica()
    {
        $this->actingAs($this->user, 'sanctum');

        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $category->id,
                         'name' => $category->name
                     ]
                 ]);
    }

    /** @test */
    public function puede_actualizar_una_categoria()
    {
        $this->actingAs($this->user, 'sanctum');

        $category = Category::factory()->create(['name' => 'Categoría Original']);

        $updatedData = [
            'name' => 'Categoría Actualizada',
            'description' => 'Descripción actualizada'
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $updatedData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Categoría Actualizada'
                     ]
                 ]);

        $this->assertDatabaseHas('categories', ['name' => 'Categoría Actualizada']);
        $this->assertDatabaseMissing('categories', ['name' => 'Categoría Original']);
    }

    /** @test */
    public function puede_eliminar_una_categoria()
    {
        $this->actingAs($this->user, 'sanctum');

        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Categoría eliminada exitosamente.'
                 ]);

        $this->assertSoftDeleted('categories', ['id' => $category->id]);
    }

    /** @test */
    public function no_puede_eliminar_categoria_con_subcategorias()
    {
        $this->actingAs($this->user, 'sanctum');

        $parentCategory = Category::factory()->create();
        $childCategory = Category::factory()->create(['parent_id' => $parentCategory->id]);

        $response = $this->deleteJson("/api/categories/{$parentCategory->id}");

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'No se puede eliminar la categoría porque tiene subcategorías asociadas.'
                 ]);

        $this->assertDatabaseHas('categories', ['id' => $parentCategory->id]);
    }

    /** @test */
    public function puede_obtener_arbol_de_categorias()
    {
        $this->actingAs($this->user, 'sanctum');

        $parent = Category::factory()->create();
        $child = Category::factory()->create(['parent_id' => $parent->id]);

        $response = $this->getJson('/api/categories-tree');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'children' => [
                                 '*' => ['id', 'name', 'parent_id']
                             ]
                         ]
                     ]
                 ]);
    }

    /** @test */
    public function puede_filtrar_categorias_por_estado_activo()
    {
        $this->actingAs($this->user, 'sanctum');

        Category::factory()->count(2)->create(['is_active' => true]);
        Category::factory()->count(1)->create(['is_active' => false]);

        $response = $this->getJson('/api/categories?is_active=true');

        $response->assertStatus(200);
        
        $data = $response->json();
        foreach ($data['data']['data'] as $category) {
            $this->assertTrue($category['is_active']);
        }
    }

    /** @test */
    public function puede_restaurar_categoria_eliminada()
    {
        $this->actingAs($this->user, 'sanctum');

        $category = Category::factory()->create();
        $category->delete();

        $response = $this->postJson("/api/categories/{$category->id}/restore");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Categoría restaurada exitosamente.'
                 ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function validacion_funciona_correctamente()
    {
        $this->actingAs($this->user, 'sanctum');

        $response = $this->postJson('/api/categories', [
            'description' => 'Sin nombre'
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }
}
