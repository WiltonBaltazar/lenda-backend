<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Audiobook;

class AudiobookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Audiobook::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'slug' => Str::slug($this->faker->sentence(4)),
            'cover_image' => $this->faker->word(),
            'year' => $this->faker->numberBetween(-10000, 10000),
            'narrator' => $this->faker->word(),
        ];
    }
}
