<?php

namespace Database\Factories;

use App\Models\Resposta;
use Illuminate\Database\Eloquent\Factories\Factory;

class RespostaFactory extends Factory
{
    protected $model = Resposta::class;

    public function definition(): array
    {
        return [
            'descricao_resposta' => fake()->sentence(12),
        ];
    }
}