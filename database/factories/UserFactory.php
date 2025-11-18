<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [

            'name' => $this->faker->unique()->text(5),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('123456'),
            'role_id' => \App\Models\Role::inRandomOrder()->first()->id,
            'description' => $this->faker->word(),
            'dob' => $this->faker->date(),
            'profile' => $this->faker->word(),
            'country_id' => \App\Models\Country::inRandomOrder()->first()->id,
            'state_id' => \App\Models\State::inRandomOrder()->first()->id,
            'city_id' => \App\Models\City::inRandomOrder()->first()->id,
            'gender' => 'M',
            'status' => 'Y',
        ];
    }
}
