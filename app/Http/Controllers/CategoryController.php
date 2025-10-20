<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Requests\CategoryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Categorías
 *
 * Endpoints para la gestión de categorías dentro del sistema.
 * Permite listar, crear, actualizar, eliminar, restaurar y consultar el árbol jerárquico de categorías.
 */

//GET    /api/categories              # Listar categorías
//POST   /api/categories              # Crear categoría
//GET    /api/categories/{id}         # Obtener categoría específica
//PUT    /api/categories/{id}         # Actualizar categoría
//DELETE /api/categories/{id}         # Eliminar categoría (soft delete)
//GET    /api/categories-tree         # Obtener árbol de categorías
//POST   /api/categories/{id}/restore # Restaurar categoría eliminada

class CategoryController extends Controller
{
    /**
     * Listar categorías
     *
     * Obtiene una lista paginada de las categorías registradas en el sistema.
     * Se pueden aplicar filtros, búsqueda y ordenamiento.
     *
     * @queryParam is_active boolean Opcional. Filtra por estado activo/inactivo. Ejemplo: true
     * @queryParam parent_id integer Opcional. Filtra por categoría padre. Ejemplo: 1
     * @queryParam search string Opcional. Busca por nombre o descripción. Ejemplo: "Electrónica"
     * @queryParam sort_field string Campo por el cual ordenar. Por defecto: created_at
     * @queryParam sort_direction string Dirección del ordenamiento ("asc" o "desc"). Por defecto: desc
     * @queryParam per_page integer Cantidad de registros por página. Por defecto: 15
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "data": [
     *       {
     *         "id": 1,
     *         "name": "Electrónica",
     *         "slug": "electronica",
     *         "description": "Artículos de tecnología y gadgets",
     *         "is_active": true,
     *         "parent_id": null
     *       }
     *     ]
     *   },
     *   "message": "Categorías obtenidas exitosamente."
     * }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::with('parent', 'children');
            
            // Filtros
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            if ($request->has('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            }
            
            if ($request->has('search')) {
                $query->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('description', 'like', '%' . $request->search . '%');
            }

            // Ordenamiento
            $sortField = $request->get('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $categories = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Categorías obtenidas exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener las categorías.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Crear una nueva categoría
     *
     * Registra una nueva categoría en el sistema.
     *
     * @bodyParam name string requerido Nombre de la categoría. Ejemplo: "Electrónica"
     * @bodyParam description string Descripción de la categoría. Ejemplo: "Artículos tecnológicos"
     * @bodyParam parent_id integer ID de la categoría padre. Puede ser null.
     * @bodyParam is_active boolean Estado de la categoría. Por defecto: true
     *
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Electrónica",
     *     "description": "Artículos tecnológicos"
     *   },
     *   "message": "Categoría creada exitosamente."
     * }
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        try {
            $category = Category::create($request->validated());

            return response()->json([
                'success' => true,
                'data' => $category->load('parent'),
                'message' => 'Categoría creada exitosamente.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear la categoría.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener una categoría específica
     *
     * Muestra los detalles de una categoría identificada por su ID.
     *
     * @urlParam id integer requerido ID de la categoría. Ejemplo: 5
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 5,
     *     "name": "Accesorios",
     *     "description": "Artículos complementarios"
     *   },
     *   "message": "Categoría obtenida exitosamente."
     * }
     */
    public function show(Category $category): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $category->load('parent', 'children'),
                'message' => 'Categoría obtenida exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener la categoría.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Actualizar una categoría
     *
     * Modifica los datos de una categoría existente.
     *
     * @urlParam id integer requerido ID de la categoría. Ejemplo: 2
     * @bodyParam name string requerido Nuevo nombre de la categoría. Ejemplo: "Audio y Sonido"
     * @bodyParam description string Nueva descripción. Ejemplo: "Equipos de audio profesional"
     * @bodyParam is_active boolean Estado de la categoría.
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 2,
     *     "name": "Audio y Sonido"
     *   },
     *   "message": "Categoría actualizada exitosamente."
     * }
     */
    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $category->update($request->validated());

            return response()->json([
                'success' => true,
                'data' => $category->fresh(['parent', 'children']),
                'message' => 'Categoría actualizada exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la categoría.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Eliminar una categoría
     *
     * Elimina una categoría del sistema (soft delete).
     * No se permite eliminar una categoría que tenga subcategorías asociadas.
     *
     * @urlParam id integer requerido ID de la categoría. Ejemplo: 3
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Categoría eliminada exitosamente."
     * }
     *
     * @response 422 {
     *   "success": false,
     *   "message": "No se puede eliminar la categoría porque tiene subcategorías asociadas."
     * }
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            // Verificar si tiene categorías hijas
            if ($category->children()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede eliminar la categoría porque tiene subcategorías asociadas.'
                ], 422);
            }

            $category->delete();

            return response()->json([
                'success' => true,
                'message' => 'Categoría eliminada exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar la categoría.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Restaurar una categoría eliminada
     *
     * Restaura una categoría previamente eliminada (soft delete).
     *
     * @urlParam id integer requerido ID de la categoría a restaurar. Ejemplo: 4
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Categoría restaurada exitosamente."
     * }
     */
    public function restore($id): JsonResponse
    {
        try {
            $category = Category::withTrashed()->findOrFail($id);
            $category->restore();

            return response()->json([
                'success' => true,
                'data' => $category,
                'message' => 'Categoría restaurada exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar la categoría.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener árbol de categorías
     *
     * Devuelve la estructura jerárquica completa de las categorías activas.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Electrónica",
     *       "children": [
     *         {
     *           "id": 2,
     *           "name": "Teléfonos"
     *         }
     *       ]
     *     }
     *   ],
     *   "message": "Árbol de categorías obtenido exitosamente."
     * }
     */
    public function tree(): JsonResponse
    {
        try {
            $categories = Category::with(['children' => function ($query) {
                $query->active()->with('children');
            }])
            ->parent()
            ->active()
            ->get();

            return response()->json([
                'success' => true,
                'data' => $categories,
                'message' => 'Árbol de categorías obtenido exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el árbol de categorías.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}