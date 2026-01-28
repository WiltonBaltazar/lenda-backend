<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\EbookSerie;

class EbookSerieFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = EbookSerie::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);

        return [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . Str::random(5),
            'cover_image' => $this->faker->image('storage/app/public/images', 640, 480, null, false) ?: $this->faker->lexify('series-????.jpg'),
            'description' => $this->faker->paragraph(),
        ];
    }
}


