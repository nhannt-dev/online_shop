<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->name();
        $slug = Str::slug($title);

        $cate = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $cateRandKey = array_rand($cate);
        
        $subCate = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $subCateRandKey = array_rand($subCate);

        $brand = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $brandRandKey = array_rand($brand);
        return [
            'title' => $title,
            'slug' => $slug,
            'category_id' => $cate[$cateRandKey],
            'sub_category_id' => $subCate[$subCateRandKey],
            'brand_id' => $brand[$brandRandKey],
            'price' => rand(10, 1000),
            'sku' => rand(1000, 100000000),
            'track_qty' => 'Yes',
            'qty' => rand(10, 50),
            'is_featured' => 'Yes',
            'status' => rand(0, 1),
        ];
    }
}
