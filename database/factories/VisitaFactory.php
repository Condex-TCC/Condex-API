<?php

namespace Database\Factories;

use App\Models\Morador;
use App\Models\Porteiro;
use App\Models\Visita;
use App\Models\Visitante;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitaFactory extends Factory
{
    protected $model = Visita::class;

    public function definition(): array
    {
        //Entrada em algum momento dos últimos 30 dias
        $entrada = $this->faker->dateTimeBetween('-30 days', 'now');

        return [
            'entrada' => $entrada,
            'saida' => $this->faker->dateTimeBetween($entrada, '+8 hours'),
            'fk_visitante' => Visitante::inRandomOrder()->firstOrFail()->pk_id_visitante,
            'fk_morador' => Morador::inRandomOrder()->firstOrFail()->pk_id_morador,
            'fk_porteiro' => Porteiro::inRandomOrder()->firstOrFail()->pk_id_porteiro,
        ];
    }

    //Estado de visita ainda em aberto, sem registro de saída
    public function emAberto(): static
    {
        return $this->state(fn (array $attributes) => [
            'saida' => null,
        ]);
    }

    //Estado de visita já finalizada
    public function finalizada(): static
    {
        return $this->state(function (array $attributes) {
            $entrada = $attributes['entrada'] ?? $this->faker->dateTimeBetween('-30 days', 'now');

            return [
                'entrada' => $entrada,
                'saida' => $this->faker->dateTimeBetween($entrada, '+8 hours'),
            ];
        });
    }
}
