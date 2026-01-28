<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Ebook;
use App\Models\EbookSerie;

class EbookFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Ebook::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->sentence(4);
        $serie = EbookSerie::factory();

        return [
            'ebook_serie_id' => $serie,
            'title' => $title,
            'slug' => Str::slug($title) . '-' . Str::random(6),
            'cover_image' => $this->faker->lexify('ebook-cover-????.jpg'),
            'chapters' => $this->faker->numberBetween(5, 40),
            'file' => $this->faker->lexify('ebook-file-????.pdf'),
            'year' => $this->faker->date(),
            'is_free' => $this->faker->boolean(30),
        ];
    }
}
