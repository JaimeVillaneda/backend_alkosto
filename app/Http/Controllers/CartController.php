<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use App\Http\Requests\CartRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * @group Carrito de Compras
 *
 * Endpoints para la gestión del carrito de compras.
 */

// GET    /api/cart          # Obtener carrito
// POST   /api/cart          # Agregar producto al carrito
// PUT    /api/cart/{id}     # Actualizar cantidad
// DELETE /api/cart/{id}     # Eliminar producto del carrito
// DELETE /api/cart          # Vaciar carrito

class CartController extends Controller
{
    /**
     * Obtener carrito
     *
     * Obtiene todos los productos en el carrito del usuario autenticado.
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "items": [
     *       {
     *         "id": 1,
     *         "product_id": 5,
     *         "quantity": 2,
     *         "unit_price": 99.99,
     *         "subtotal": 199.98,
     *         "product": {
     *           "id": 5,
     *           "name": "Laptop Gaming",
     *           "sku": "SKU-ABC123"
     *         }
     *       }
     *     ],
     *     "total": 199.98,
     *     "items_count": 1
     *   },
     *   "message": "Carrito obtenido exitosamente."
     * }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            
            $cartItems = Cart::with('product:id,name,sku,stock,is_active')
                ->where('user_id', $userId)
                ->get();

            $total = $cartItems->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            });
            
            $itemsCount = $cartItems->sum('quantity');

            $cartData = [
                'items' => $cartItems,
                'total' => $total,
                'items_count' => $itemsCount
            ];

            return response()->json([
                'success' => true,
                'data' => $cartData,
                'message' => 'Carrito obtenido exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener el carrito.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Agregar producto al carrito
     *
     * Agrega un producto al carrito del usuario. Si el producto ya existe, suma la cantidad.
     *
     * @bodyParam product_id integer requerido ID del producto. Ejemplo: 5
     * @bodyParam quantity integer requerido Cantidad a agregar. Ejemplo: 2
     *
     * @response 201 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "product_id": 5,
     *     "quantity": 2,
     *     "unit_price": 99.99
     *   },
     *   "message": "Producto agregado al carrito exitosamente."
     * }
     */
    public function store(CartRequest $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            $productId = $request->product_id;
            $quantity = $request->quantity;

            // Verificar que el producto existe y está activo
            $product = Product::where('is_active', true)->find($productId);
            
            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'El producto no está disponible.'
                ], 404);
            }

            // Verificar stock disponible
            if ($product->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Stock disponible: ' . $product->stock
                ], 422);
            }

            DB::beginTransaction();

            // Buscar si el producto ya está en el carrito
            $cartItem = Cart::where('user_id', $userId)
                ->where('product_id', $productId)
                ->first();

            if ($cartItem) {
                // Actualizar cantidad si ya existe
                $newQuantity = $cartItem->quantity + $quantity;
                
                // Verificar stock nuevamente con la nueva cantidad
                if ($product->stock < $newQuantity) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'Stock insuficiente. Stock disponible: ' . $product->stock . ', en carrito: ' . $cartItem->quantity
                    ], 422);
                }

                $cartItem->update([
                    'quantity' => $newQuantity,
                    'unit_price' => $product->price // Actualizar precio por si cambió
                ]);
            } else {
                // Crear nuevo item en el carrito
                $cartItem = Cart::create([
                    'user_id' => $userId,
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'unit_price' => $product->price
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $cartItem->load('product:id,name,sku'),
                'message' => 'Producto agregado al carrito exitosamente.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar producto al carrito.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Actualizar cantidad en carrito
     *
     * Actualiza la cantidad de un producto específico en el carrito.
     *
     * @urlParam id integer requerido ID del item del carrito. Ejemplo: 1
     * @bodyParam quantity integer requerido Nueva cantidad. Ejemplo: 3
     *
     * @response 200 {
     *   "success": true,
     *   "data": {
     *     "id": 1,
     *     "quantity": 3,
     *     "unit_price": 99.99
     *   },
     *   "message": "Carrito actualizado exitosamente."
     * }
     */
    public function update(CartRequest $request, Cart $cart): JsonResponse
    {
        try {
            // Verificar que el carrito pertenece al usuario
            if ($cart->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para modificar este carrito.'
                ], 403);
            }

            $quantity = $request->quantity;
            $product = $cart->product;

            // Verificar stock disponible
            if ($product->stock < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stock insuficiente. Stock disponible: ' . $product->stock
                ], 422);
            }

            $cart->update([
                'quantity' => $quantity,
                'unit_price' => $product->price // Actualizar precio por si cambió
            ]);

            return response()->json([
                'success' => true,
                'data' => $cart->load('product:id,name,sku'),
                'message' => 'Carrito actualizado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el carrito.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Eliminar producto del carrito
     *
     * Elimina un producto específico del carrito.
     *
     * @urlParam id integer requerido ID del item del carrito. Ejemplo: 1
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Producto eliminado del carrito exitosamente."
     * }
     */
    public function destroy(Request $request, Cart $cart): JsonResponse
    {
        try {
            // Verificar que el carrito pertenece al usuario
            if ($cart->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tienes permisos para eliminar este item.'
                ], 403);
            }

            $cart->delete();

            return response()->json([
                'success' => true,
                'message' => 'Producto eliminado del carrito exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar producto del carrito.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    /**
     * Vaciar carrito
     *
     * Elimina todos los productos del carrito del usuario.
     *
     * @response 200 {
     *   "success": true,
     *   "message": "Carrito vaciado exitosamente."
     * }
     */
    public function clear(Request $request): JsonResponse
    {
        try {
            $userId = $request->user()->id;
            
            Cart::where('user_id', $userId)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Carrito vaciado exitosamente.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al vaciar el carrito.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }
}