<?php

namespace Database\Factories;

use App\Models\Visitante;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitanteFactory extends Factory
{
    protected $model = Visitante::class;

    public function definition(): array
    {
        return [
            'nome_visitante' => $this->faker->name(),
            'cpf_visitante' => $this->faker->unique()->numerify('###########'),
            'fk_morador' => 1,
            'fk_funcionario' => 1,
        ];
    }
}