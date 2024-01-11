<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Country;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\User::factory(1)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\Category::factory(20)->create();
        \App\Models\SubCategory::factory(50)->create();
        \App\Models\Brand::factory(30)->create();
        \App\Models\Product::factory(100)->create();
        \App\Models\ShippingCharge::factory(Country::get()->count())->create();
    }
}
