<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Audiobook;
use App\Models\AudiobookChapter;

class AudiobookChapterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = AudiobookChapter::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'audiobook_id' => Audiobook::factory(),
            'title' => $this->faker->sentence(4),
            'image_cover' => $this->faker->word(),
            'audio_file' => $this->faker->word(),
        ];
    }
}
