<?php

namespace Database\Factories;

use App\Models\Porteiro;
use App\Models\Morador;
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

            'fk_id_morador_encomenda' => Morador::inRandomOrder()->first()->pk_id_morador,

            'data_retirada' => null
        ];
    }
}
