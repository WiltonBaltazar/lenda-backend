<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Newsletter;

class NewsletterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Newsletter::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'slug' => Str::slug($this->faker->sentence(4)),
            'body' => $this->faker->text(),
            'image_cover' => $this->faker->word(),
            'author' => $this->faker->word(),
        ];
    }
}
