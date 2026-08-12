<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EspacoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'descricao_espaco' => fake()->sentence(),
            'nome_espaco' => fake()->randomElement([
                'Salão de Festas',
                'Piscina',
                'Academia',
                'Churrasqueira',
                'Quadra Esportiva',
                'Playground'
            ]),
            'autorizacao' => fake()->boolean()
        ];
    }
}