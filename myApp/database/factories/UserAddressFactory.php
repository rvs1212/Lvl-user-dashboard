<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserAddress>
 */
class UserAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'country'   => $this->faker->country(),
            'city'      => $this->faker->city(),
            'post_code' => $this->faker->postcode(),
            'street'    => $this->faker->streetAddress(),
        ];
    }
}
