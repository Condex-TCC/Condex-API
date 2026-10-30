<?php

namespace Database\Factories;

use App\Models\Visita;
use App\Models\Visitante;
use App\Models\Morador;
use App\Models\Porteiro;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitaFactory extends Factory
{
    protected $model = Visita::class;

    public function definition(): array
    {
        $entrada = $this->faker->dateTimeBetween('-30 days', 'now');

        return [
            'entrada' => $entrada,
            'saida' => $this->faker->dateTimeBetween(
                $entrada,
                '+8 hours'
            ),
            'fk_visitante' => Visitante::inRandomOrder()
                ->firstOrFail()->pk_id_visitante,
            'fk_morador' => Morador::inRandomOrder()
                ->firstOrFail()->pk_id_morador,
            'fk_porteiro' => Porteiro::inRandomOrder()
                ->firstOrFail()->pk_id_porteiro,
        ];
    }
}
