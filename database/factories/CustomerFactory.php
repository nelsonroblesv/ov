<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id'=>$this->faker->numberBetween(1,5),
           'alias'=>$this->faker->unique()->userName(),
           'name'=>$this->faker->name(),
           'email'=>$this->faker->unique->email(),
           'phone'=>$this->faker->unique()->phoneNumber(),
           'birthday'=>$this->faker->dateTime(),
           'created_at'=>$this->faker->dateTime(),
          'type'=>$this->faker->randomElement(['par', 'non']),
          
        ];
    }
}
