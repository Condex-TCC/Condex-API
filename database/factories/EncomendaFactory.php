<?php

namespace Database\Factories;

use App\Models\Morador;
use App\Models\Porteiro;
use Illuminate\Database\Eloquent\Factories\Factory;

class EncomendaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome_encomenda' => $this->faker->randomElement([
                'Caixa Amazon',
                'Pacote Mercado Livre',
                'Encomenda Shopee',
                'Caixa Magazine Luiza',
                'Pacote Correios',
                'Caixa Americanas',
                'Pacote AliExpress',
            ]),

            'descricao_encomenda' => $this->faker->randomElement([
                'Pacote de pequeno porte recebido na portaria',
                'Caixa média entregue pelo transportador',
                'Encomenda frágil, manusear com cuidado',
                'Pacote lacrado entregue na recepção',
                'Caixa grande recebida no período da manhã',
            ]),

            'fk_id_porteiro_encomenda' => Porteiro::inRandomOrder()->firstOrFail()->pk_id_porteiro,

            'fk_id_morador_encomenda' => Morador::inRandomOrder()->firstOrFail()->pk_id_morador,

            //Por padrão a encomenda ainda está aguardando retirada
            'data_retirada' => null,
        ];
    }

    //Estado de encomenda já retirada pelo morador
    public function retirada(): static
    {
        return $this->state(fn (array $attributes) => [
            'data_retirada' => $this->faker->dateTimeBetween('-10 days', 'now'),
        ]);
    }

    //Estado de encomenda aguardando retirada
    public function aguardandoRetirada(): static
    {
        return $this->state(fn (array $attributes) => [
            'data_retirada' => null,
        ]);
    }
}
