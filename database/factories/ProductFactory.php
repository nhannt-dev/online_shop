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

        $cate = [];
        for ($i = 1; $i <= 20; $i++) $cate[] = $i;
        $cateRandKey = array_rand($cate);
        
        $subCate = [];
        for ($i = 1; $i <= 50; $i++) $subCate[] = $i;
        $subCateRandKey = array_rand($subCate);
        
        $brand = [];
        for ($i = 1; $i <= 30; $i++) $brand[] = $i;
        $brandRandKey = array_rand($brand);
        return [
            'title' => $title,
            'slug' => $slug,
            'category_id' => $cate[$cateRandKey],
            'short_description' => "It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for 'lorem ipsum' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).",
            'description' => "Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.",
            'shipping_returns' => "There are many variations of passages of Lorem Ipsum available, but the majority have suffered alteration in some form, by injected humour, or randomised words which don't look even slightly believable. If you are going to use a passage of Lorem Ipsum, you need to be sure there isn't anything embarrassing hidden in the middle of text. All the Lorem Ipsum generators on the Internet tend to repeat predefined chunks as necessary, making this the first true generator on the Internet. It uses a dictionary of over 200 Latin words, combined with a handful of model sentence structures, to generate Lorem Ipsum which looks reasonable. The generated Lorem Ipsum is therefore always free from repetition, injected humour, or non-characteristic words etc.",
            'sub_category_id' => $subCate[$subCateRandKey],
            'brand_id' => $brand[$brandRandKey],
            'price' => rand(10, 1000),
            'sku' => rand(1000, 100000000),
            'track_qty' => 'Yes',
            'qty' => rand(1, 20),
            'is_featured' => 'Yes',
            'status' => rand(0, 1),
        ];
    }
}
