<?php

namespace Database\Factories;

use App\Models\Morador;
use App\Models\Resposta;
use Illuminate\Database\Eloquent\Factories\Factory;

class RespostaFactory extends Factory
{
    protected $model = Resposta::class;

    public function definition(): array
    {
        return [
            'fk_id_morador' => Morador::inRandomOrder()->first()->pk_id_morador,
            'descricao_resposta' => fake()->sentence(12),
        ];
    }
}