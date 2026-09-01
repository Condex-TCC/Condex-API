<?php

namespace Database\Factories;

use App\Models\Comunicado;
use App\Models\Sindico;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComunicadoFactory extends Factory
{
    protected $model = Comunicado::class;

    public function definition(): array
    {
        return [
            'descricao_comunicado' => fake()->sentence(15),
            'fk_id_sindico_comunicados' => Sindico::inRandomOrder()
                ->first()
                ->pk_id_sindico,
        ];
    }
}