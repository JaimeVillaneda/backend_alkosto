<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $productId = $this->route('product')?->id;

        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $productId,
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'max_stock' => 'nullable|integer|min:0|gt:min_stock',
            'is_active' => 'boolean',
            'category_id' => 'required|exists:categories,id',
            'brand' => 'nullable|string|max:255',
            'weight' => 'nullable|numeric|min:0',
            'dimensions' => 'nullable|string|max:100',
            'images' => 'nullable|array',
            'images.*' => 'string'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'El nombre del producto es obligatorio.',
            'price.required' => 'El precio del producto es obligatorio.',
            'price.min' => 'El precio no puede ser negativo.',
            'stock.required' => 'El stock del producto es obligatorio.',
            'stock.min' => 'El stock no puede ser negativo.',
            'category_id.required' => 'La categoría del producto es obligatoria.',
            'category_id.exists' => 'La categoría seleccionada no existe.',
            'max_stock.gt' => 'El stock máximo debe ser mayor al stock mínimo.',
            'sku.unique' => 'Ya existe un producto con este SKU.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->cost_price && $this->price < $this->cost_price) {
                $validator->errors()->add(
                    'price', 
                    'El precio de venta no puede ser menor al precio de costo.'
                );
            }
        });
    }
}