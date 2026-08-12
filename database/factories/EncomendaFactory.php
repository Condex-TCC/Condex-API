<?php

namespace Database\Factories;

use App\Models\Porteiro;
use Illuminate\Database\Eloquent\Factories\Factory;

class EncomendaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome_encomenda' => fake()->randomElement([
                'Caixa Amazon',
                'Pacote Mercado Livre',
                'Encomenda Shopee',
                'Caixa Magazine Luiza',
                'Pacote Correios'
            ]),

            'descricao_encomenda' => fake()->sentence(),

            'fk_id_porteiro_encomenda' => Porteiro::inRandomOrder()->first()->pk_id_porteiro,

            'data_retirada' => null
        ];
    }
}