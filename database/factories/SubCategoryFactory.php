<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SubCategory>
 */
class SubCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->name();
        $slug = Str::slug($name);

        $cate = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        $cateRandKey = array_rand($cate);

        return [
            'name' => $name,
            'slug' => $slug,
            'status' => rand(0, 1),
            'category_id' => $cate[$cateRandKey]
        ];
    }
}
