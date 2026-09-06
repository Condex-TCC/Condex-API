<?php

namespace Database\Factories;

use App\Models\Espaco;
use App\Models\Morador;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reserva>
 */
class ReservaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'fk_id_morador' => Morador::inRandomOrder()->first()->pk_id_morador,
            'fk_id_espaco' => Espaco::inRandomOrder()->first()->pk_id_espaco,
            'data_reserva' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'hora_inicio' => fake()->randomElement([
                '08:00',
                '10:00',
                '12:00',
                '14:00',
                '16:00',
                '18:00',
            ]),
            'hora_fim' => fake()->randomElement([
                '10:00',
                '12:00',
                '14:00',
                '16:00',
                '18:00',
                '20:00',
            ]),
            'status_reserva' => fake()->randomElement([
                'pendente',
                'confirmada',
                'cancelada',
            ]),
        ];
    }
}