<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.fake()->unique()->numerify('###'),
            'price' => fake()->numberBetween(100000, 5000000),
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }
}
