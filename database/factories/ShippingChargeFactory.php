<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShippingCharge>
 */
class ShippingChargeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $country = [];
        for ($i = 1; $i <= 242; $i++) $country[] = $i;

        $countryRandKey = array_rand($country);

        return [
            'amount' => rand(5, 30),
            'country_id' => $country[$countryRandKey]
        ];
    }
}
