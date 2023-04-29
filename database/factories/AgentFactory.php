<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class AgentFactory extends Factory
{
    public function definition()
    {
        return [
            'first_name' => $this->faker->name(),
            'last_name' => $this->faker->name(),
            'date_of_birth' => $this->faker->date(),
            'email' => $this->faker->email(),
            'gender' => $this->faker->name(),
            'business_name' => $this->faker->name(),
            'business_address' => $this->faker->address(),
            'business_phone' => $this->faker->phoneNumber(),
            'state_id' => 1,
            'agent_type_id'=>7,
            'super_agent_code'=>9,
            'local_government_id' => 1,
            'account_number' => 1,
            'vfd_account_number' => 1,
            'funds_transfer_bank' => 1,
            'account_name' => 1,
            'bank_id' => $this->faker->unique()->randomDigit(),
            'bvn' => $this->faker->unique()->randomDigit(),
            'agent_code' => $this->faker->unique()->numberBetween(),
            'identity_type' => 1,
            'business_type' => 1,
            'wallet_no' => $this->faker->unique()->randomDigit(),
            'is_nuban' => 1,
            'api_key' => 'hjdhhjdh',
            'business_type_id'=>9,
            'activation_code' => $this->faker->unique()->randomDigit(),
            'uuid' => $this->faker->uuid(),
            'is_test_agent' => 1,
            'isw_version' => 1,
        ];
    }
}
