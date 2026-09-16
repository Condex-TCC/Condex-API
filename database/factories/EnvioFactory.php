<?php

namespace Database\Factories;

use App\Models\Comunicado;
use App\Models\Envio;
use App\Models\Morador;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnvioFactory extends Factory
{
    protected $model = Envio::class;

    public function definition(): array
    {
        return [
            'fk_id_comunicados' => Comunicado::inRandomOrder()
                ->first()
                ->pk_id_comunicados,

            'fk_id_morador' => Morador::inRandomOrder()
                ->first()
                ->pk_id_morador,

            'fk_id_resposta' => null,

            'fk_id_contra_resposta' => null,
        ];
    }
}