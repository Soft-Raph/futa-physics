<?php

namespace Database\Factories;

use App\Models\Agent;
use Illuminate\Database\Eloquent\Factories\Factory;

class ApiKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'uuid' => $this->faker->uuid(),
            'api_key' => $this->faker->uuid(),
            'api_secret' => $this->faker->uuid(),
            'resource_namespace' => get_class(new Agent()),
        ];
    }
}
