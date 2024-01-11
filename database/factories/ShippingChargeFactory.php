<?php

namespace Database\Factories;

use App\Models\Country;
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
        $ct = Country::get();
        for ($i = 1; $i <= $ct->count(); $i++) $country[] = $i;

        $countryRandKey = array_rand($country);

        return [
            'amount' => rand(5, 30),
            'country_id' => $country[$countryRandKey]
        ];
    }
}
