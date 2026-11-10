<?php

namespace Database\Seeders;

use App\Models\Morador;
use App\Models\Porteiro;
use App\Models\Visita;
use App\Models\Visitante;
use Illuminate\Database\Seeder;

class VisitaSeeder extends Seeder
{
    public function run(): void
    {
        //Apenas os 5 primeiros moradores estão com o primeiro acesso completo
        $moradores = Morador::orderBy('pk_id_morador')->take(5)->get();
        $visitantes = Visitante::orderBy('pk_id_visitante')->get();
        $porteiros = Porteiro::orderBy('pk_id_porteiro')->get();

        //Sem moradores, visitantes ou porteiros não é possível registrar visitas
        if ($moradores->isEmpty() || $visitantes->isEmpty() || $porteiros->isEmpty()) {
            return;
        }

        foreach ($visitantes as $indice => $visitante) {
            //Cada visitante tem um morador de referência, simulando visitas recorrentes
            $morador = $moradores[$indice % $moradores->count()];

            //Histórico com duas visitas já finalizadas para cada visitante
            foreach ([0, 1] as $indiceHistorico) {
                Visita::factory()
                    ->finalizada()
                    ->create([
                        'fk_id_visitante' => $visitante->pk_id_visitante,
                        'fk_id_morador' => $morador->pk_id_morador,
                        'fk_id_porteiro_entrada' => $porteiros[($indice + $indiceHistorico) % $porteiros->count()]->pk_id_porteiro,
                        'fk_id_porteiro_saida' => $porteiros[($indice + $indiceHistorico + 1) % $porteiros->count()]->pk_id_porteiro,
                    ]);
            }

            //Distribui as visitas futuras e em andamento entre os cenários de teste
            $cenario = $indice % 10;

            $factory = match (true) {
                $cenario <= 3 => Visita::factory()->agendada(),
                $cenario <= 6 => Visita::factory()->autorizada(),
                $cenario === 7 => Visita::factory()->negada(),
                $cenario === 8 => Visita::factory()->emAberto(),
                default => Visita::factory()->naoCompareceu(),
            };

            $factory->create([
                'fk_id_visitante' => $visitante->pk_id_visitante,
                'fk_id_morador' => $morador->pk_id_morador,
            ]);
        }
    }
}
