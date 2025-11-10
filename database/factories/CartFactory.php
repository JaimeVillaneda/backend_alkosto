<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CartFactory extends Factory
{
    protected $model = \App\Models\Cart::class;

    public function definition()
    {
        $product = \App\Models\Product::factory()->create();

        return [
            'user_id' => \App\Models\User::factory(),
            'product_id' => $product->id,
            'quantity' => $this->faker->numberBetween(1, 5),
            'unit_price' => $product->price,
        ];
    }
}