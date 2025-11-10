<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = \App\Models\Product::class;

    public function definition()
    {
        return [
            'name' => $this->faker->words(3, true),
            'description' => $this->faker->paragraph,
            'sku' => 'SKU-' . $this->faker->unique()->bothify('??##??##'),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'cost_price' => $this->faker->randomFloat(2, 5, 800),
            'stock' => $this->faker->numberBetween(0, 100),
            'min_stock' => $this->faker->numberBetween(1, 10),
            'max_stock' => $this->faker->numberBetween(50, 200),
            'is_active' => $this->faker->boolean(90),
            'category_id' => \App\Models\Category::factory(),
            'brand' => $this->faker->company,
            'weight' => $this->faker->randomFloat(2, 0.1, 20),
            'dimensions' => $this->faker->randomElement(['10x5x2 cm', '15x8x3 cm', '20x10x5 cm']),
            'images' => $this->faker->optional(0.7)->passthrough(
                $this->faker->randomElements([
                    'https://picsum.photos/400/300',
                    'https://picsum.photos/400/301',
                    'https://picsum.photos/400/302'
                ], $this->faker->numberBetween(1, 3))
            )
        ];
    }

    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }

    public function lowStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stock' => $this->faker->numberBetween(0, 2),
                'min_stock' => $this->faker->numberBetween(3, 5),
            ];
        });
    }

    public function outOfStock()
    {
        return $this->state(function (array $attributes) {
            return [
                'stock' => 0,
            ];
        });
    }

    public function withCategory($categoryId)
    {
        return $this->state(function (array $attributes) use ($categoryId) {
            return [
                'category_id' => $categoryId,
            ];
        });
    }
}