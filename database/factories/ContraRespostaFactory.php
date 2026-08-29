<?php

namespace Database\Factories;

use App\Models\ContraResposta;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContraRespostaFactory extends Factory
{
    protected $model = ContraResposta::class;

    public function definition(): array
    {
        return [
            'descricao_contra_resposta' => fake()->sentence(12),
        ];
    }
}