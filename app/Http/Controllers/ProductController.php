<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Http\Requests\ProductRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @group Productos
 *
 * Endpoints para la gestión de productos dentro del sistema.
 * Permite listar, crear, actualizar, eliminar, restaurar y consultar productos.
 */

//GET    /api/products              # Listar productos
//POST   /api/products              # Crear producto
//GET    /api/products/{id}         # Obtener producto específico
//PUT    /api/products/{id}         # Actualizar producto
//DELETE /api/products/{id}         # Eliminar producto (soft delete)
//GET    /api/products-low-stock    # Obtener productos con stock bajo
//POST   /api/products/{id}/restore # Restaurar producto eliminado

class ProductController extends Controller
{
    /**
     * Listar productos
     *
     * Obtiene una lista paginada de los productos registrados en el sistema.
     * Se pueden aplicar filtros, búsqueda y ordenamiento.
     *
     * @queryParam is_active boolean Opcional. Filtra por estado activo/inactivo. Ejemplo: true
     * @queryParam category_id integer Opcional. Filtra por categoría. Ejemplo: 1
     * @queryParam low_stock boolean Opcional. Filtra productos con stock bajo. Ejemplo: true
     * @queryParam out_of_stock boolean Opcional. Filtra productos sin stock. Ejemplo: false
     * @queryParam search string Opcional. Busca por nombre, descripción o SKU. Ejemplo: "Laptop"
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
     *         "name": "Laptop Gaming",
     *         "sku": "SKU-ABC123",
     *         "price": 999.99,
     *         "stock": 10,
     *         "is_active": true,
     *         "category_id": 1
     *       }
     *     ]
     *   },
     *   "message": "Productos obtenidos exitosamente."
     * }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Product::with('category');
            
            // Filtros
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }
            
            if ($request->has('category_id')) {
                $query->where('category_id', $request->category_id);
            }
            
            if ($request->has('low_stock') && $request->boolean('low_stock')) {
                $query->lowStock();
            }
            
            if ($request->has('out_of_stock') && $request->boolean('out_of_stock')) {
                $query->outOfStock();
            }
            
            if ($request->has('search')) {
                $query->search($request->search);
            }

            // Ordenamiento
            $sortField = $request->get('sort_field', 'created_at');
            $sortDirection = $request->get('sort_direction', 'desc');
            $query->orderBy($sortField, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 15);
            $products = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Productos obtenidos exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Crear un nuevo producto
     *
     * Registra un nuevo producto en el sistema.
     *
     * @bodyParam name string requerido Nombre del producto. Ejemplo: "Laptop Gaming"
     * @bodyParam description string Descripción del producto. Ejemplo: "Laptop para gaming de alta gama"
     * @bodyParam sku string Código SKU único. Si no se proporciona, se genera automáticamente.
     * @bodyParam price number requerido Precio de venta. Ejemplo: 999.99
     * @bodyParam cost_price number Precio de costo. Ejemplo: 700.00
     * @bodyParam stock integer requerido Cantidad en stock. Ejemplo: 10
     * @bodyParam min_stock integer Stock mínimo. Ejemplo: 2
     * @bodyParam max_stock integer Stock máximo. Ejemplo: 50
     * @bodyParam is_active boolean Estado del producto. Por defecto: true
     * @bodyParam category_id integer requerido ID de la categoría. Ejemplo: 1
     * @bodyParam brand string Marca del producto. Ejemplo: "Dell"
     * @bodyParam weight number Peso en kg. Ejemplo: 2.5
     * @bodyParam dimensions string Dimensiones. Ejemplo: "30x20x5 cm"
     * @bodyParam images array Array de URLs de imágenes.
     *
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "name": "Laptop Gaming",
     *     "sku": "SKU-ABC123",
     *     "price": 999.99,
     *     "stock": 10
     *   },
     *   "message": "Producto creado exitosamente."
     * }
     */
    public function store(ProductRequest $request): JsonResponse
    {
        try {
            $product = Product::create($request->validated());

            return response()->json([
                'success' => true,
                'data' => $product->load('category'),
                'message' => 'Producto creado exitosamente.'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el producto.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener un producto específico
     *
     * Muestra los detalles de un producto identificado por su ID.
     *
     * @urlParam id integer requerido ID del producto. Ejemplo: 5
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 5,
     *     "name": "Laptop Gaming",
     *     "sku": "SKU-ABC123",
     *     "price": 999.99,
     *     "stock": 10
     *   },
     *   "message": "Producto obtenido exitosamente."
     * }
     */
    public function show(Product $product): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'data' => $product->load('category'),
                'message' => 'Producto obtenido exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el producto.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Actualizar un producto
     *
     * Modifica los datos de un producto existente.
     *
     * @urlParam id integer requerido ID del producto. Ejemplo: 2
     * @bodyParam name string requerido Nuevo nombre del producto. Ejemplo: "Laptop Gaming Pro"
     * @bodyParam description string Nueva descripción.
     * @bodyParam price number Nuevo precio.
     * @bodyParam stock integer Nuevo stock.
     * @bodyParam is_active boolean Estado del producto.
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 2,
     *     "name": "Laptop Gaming Pro"
     *   },
     *   "message": "Producto actualizado exitosamente."
     * }
     */
    public function update(ProductRequest $request, Product $product): JsonResponse
    {
        try {
            $product->update($request->validated());

            return response()->json([
                'success' => true,
                'data' => $product->fresh('category'),
                'message' => 'Producto actualizado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el producto.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Eliminar un producto
     *
     * Elimina un producto del sistema (soft delete).
     *
     * @urlParam id integer requerido ID del producto. Ejemplo: 3
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Producto eliminado exitosamente."
     * }
     */
    public function destroy(Product $product): JsonResponse
    {
        try {
            $product->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar el producto.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Restaurar un producto eliminado
     *
     * Restaura un producto previamente eliminado (soft delete).
     *
     * @urlParam id integer requerido ID del producto a restaurar. Ejemplo: 4
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Producto restaurado exitosamente."
     * }
     */
    public function restore($id): JsonResponse
    {
        try {
            $product = Product::withTrashed()->findOrFail($id);
            $product->restore();

            return response()->json([
                'success' => true,
                'data' => $product->load('category'),
                'message' => 'Producto restaurado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al restaurar el producto.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Obtener productos con stock bajo
     *
     * Devuelve los productos que tienen stock igual o menor al stock mínimo definido.
     *
     * @response 200 {
     *   "success": true,
     *   "data": [
     *     {
     *       "id": 1,
     *       "name": "Laptop Gaming",
     *       "stock": 2,
     *       "min_stock": 5
     *     }
     *   ],
     *   "message": "Productos con stock bajo obtenidos exitosamente."
     * }
     */
    public function lowStock(): JsonResponse
    {
        try {
            $products = Product::with('category')
                ->lowStock()
                ->active()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $products,
                'message' => 'Productos con stock bajo obtenidos exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener los productos con stock bajo.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}