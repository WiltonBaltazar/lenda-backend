<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'name' => 'Premium',
            'duration_days' => 30,
            'description' => '<ul><li>Acesso ao conteúdo gratuíto sem publicidades</li><li>Acesso a todos e-livros e áudiolivros</li><li>Participar de discussões online exclusivas para membros</li><li>Receber atualizações e lançamentos antecipados</li></ul>',
            'price' => 300,
            'bg_color' => '#FFFFFF',
            'text_color' => '#000000',
            'slug' => Str::slug('Premium'),
        ]);
    }
}
