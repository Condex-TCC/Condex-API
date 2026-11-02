<?php

namespace Database\Seeders;

use App\Models\Espaco;
use App\Models\Morador;
use App\Models\Reserva;
use Illuminate\Database\Seeder;

class ReservaSeeder extends Seeder
{
    public function run(): void
    {
        $moradores = Morador::orderBy('pk_id_morador')->get();
        $espacos = Espaco::orderBy('pk_id_espaco')->get();

        //Horários utilizados nas reservas
        $horarios = [
            ['08:00', '10:00'],
            ['10:00', '12:00'],
            ['14:00', '16:00'],
            ['16:00', '18:00'],
            ['18:00', '20:00'],
        ];

        foreach ($moradores as $indiceMorador => $morador) {
            //Cada morador terá uma reserva pendente, uma confirmada e uma cancelada
            $situacoes = [
                [
                    'status' => 'pendente',
                    'data' => now()->addDays(7)->format('Y-m-d'),
                ],
                [
                    'status' => 'confirmada',
                    'data' => now()->addDays(14)->format('Y-m-d'),
                ],
                [
                    'status' => 'cancelada',
                    'data' => now()->subDays(5)->format('Y-m-d'),
                ],
            ];

            foreach ($situacoes as $indiceSituacao => $situacao) {
                $horario = $horarios[($indiceMorador + $indiceSituacao) % count($horarios)];

                Reserva::factory()->create([
                    'fk_id_morador' => $morador->pk_id_morador,
                    'fk_id_espaco' => $espacos[($indiceMorador + $indiceSituacao) % $espacos->count()]->pk_id_espaco,
                    'data_reserva' => $situacao['data'],
                    'hora_inicio' => $horario[0],
                    'hora_fim' => $horario[1],
                    'status_reserva' => $situacao['status'],
                ]);
            }
        }
    }
}
