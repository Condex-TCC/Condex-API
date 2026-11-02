<?php

namespace Database\Seeders;

use App\Models\Comunicado;
use App\Models\Envio;
use App\Models\Morador;
use Illuminate\Database\Seeder;

class EnvioSeeder extends Seeder
{
    public function run(): void
    {
        $comunicados = Comunicado::orderBy('pk_id_comunicados')->get();
        $moradores = Morador::orderBy('pk_id_morador')->get();

        foreach ($comunicados as $indiceComunicado => $comunicado) {
            foreach ($moradores as $indiceMorador => $morador) {
                //Varia o cenário de cada envio para gerar dados de teste variados
                $cenario = ($indiceComunicado + $indiceMorador) % 5;

                $factory = match ($cenario) {
                    0 => Envio::factory()->respondido(),
                    1 => Envio::factory()->comPergunta(),
                    2, 3 => Envio::factory()->visualizado(),
                    default => Envio::factory(),
                };

                //Cria o envio associando o comunicado e o morador do laço
                $factory->create([
                    'fk_id_comunicados' => $comunicado->pk_id_comunicados,
                    'fk_id_morador' => $morador->pk_id_morador,
                ]);
            }
        }
    }
}
