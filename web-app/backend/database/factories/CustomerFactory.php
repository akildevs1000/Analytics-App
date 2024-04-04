<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    public function definition()
    {
        $imageDirectory = public_path('customer/profile_picture');

        $files = glob($imageDirectory . '/*');

        $fileNames = array_map('basename', $files);

        return [
            'full_name' => $this->faker->name,
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            'profile_picture' => Arr::random($fileNames),
            'system_user_id' => rand(1000, 99999),
            'type' => $this->faker->randomElement(['vip', 'normal']),
            'date' => date("Y-m-d"),
            'company_id' => 1, //$this->faker->numberBetween(1, 10),
            'branch_id' => 9, // Assuming nullable field
            'phone_number' => $this->faker->phoneNumber,
            'status' => $this->faker->randomElement(['whitelisted', 'blacklisted']),
        ];
    }
}
