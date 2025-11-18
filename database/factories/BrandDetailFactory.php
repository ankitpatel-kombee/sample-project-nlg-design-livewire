<?php

namespace Database\Factories;

use App\Models\BrandDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = BrandDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [

            'brand_id' => $this->faker->unique()->numberBetween(1, 100),
            'description' => $this->faker->sentence,
            'country_id' => $this->faker->unique()->numberBetween(1, 100),
            'state_id' => $this->faker->unique()->numberBetween(1, 100),
            'city_id' => $this->faker->unique()->numberBetween(1, 100),
            'status' => $this->faker->word(),
        ];
    }
}
