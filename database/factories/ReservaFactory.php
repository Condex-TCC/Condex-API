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
        //Pares de horários válidos, garantindo que o fim seja sempre depois do início
        $horarios = [
            ['08:00', '10:00'],
            ['10:00', '12:00'],
            ['12:00', '14:00'],
            ['14:00', '16:00'],
            ['16:00', '18:00'],
            ['18:00', '20:00'],
        ];

        $horario = $this->faker->randomElement($horarios);

        return [
            'fk_id_morador' => Morador::inRandomOrder()->firstOrFail()->pk_id_morador,
            'fk_id_espaco' => Espaco::inRandomOrder()->firstOrFail()->pk_id_espaco,
            'data_reserva' => $this->faker->dateTimeBetween('-10 days', '+30 days')->format('Y-m-d'),
            'hora_inicio' => $horario[0],
            'hora_fim' => $horario[1],
            'status_reserva' => $this->faker->randomElement([
                'pendente',
                'confirmada',
                'cancelada',
            ]),
        ];
    }

    //Estado de reserva pendente de aprovação
    public function pendente(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_reserva' => 'pendente',
        ]);
    }

    //Estado de reserva confirmada
    public function confirmada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_reserva' => 'confirmada',
        ]);
    }

    //Estado de reserva cancelada
    public function cancelada(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_reserva' => 'cancelada',
        ]);
    }
}
