<?php

namespace Database\Seeders;

use App\Models\Morador;
use App\Models\Visita;
use App\Models\Visitante;
use Illuminate\Database\Seeder;

class VisitaSeeder extends Seeder
{
    public function run(): void
    {
        $moradores = Morador::orderBy('pk_id_morador')->get();
        $visitantes = Visitante::orderBy('pk_id_visitante')->get();

        foreach ($moradores as $indice => $morador) {
            //Visita já finalizada para cada morador
            Visita::factory()
                ->finalizada()
                ->create([
                    'fk_visitante' => $visitantes[$indice % $visitantes->count()]->pk_id_visitante,
                    'fk_morador' => $morador->pk_id_morador,
                ]);

            //Visita em aberto para alguns moradores, simulando visitantes ainda no condomínio
            if ($indice % 2 === 0) {
                Visita::factory()
                    ->emAberto()
                    ->create([
                        'fk_visitante' => $visitantes[($indice + 1) % $visitantes->count()]->pk_id_visitante,
                        'fk_morador' => $morador->pk_id_morador,
                    ]);
            }
        }
    }
}
