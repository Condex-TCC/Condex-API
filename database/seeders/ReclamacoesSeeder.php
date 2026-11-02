<?php

namespace Database\Seeders;

use App\Models\Morador;
use App\Models\Reclamacoes;
use Illuminate\Database\Seeder;

//Classe que popula o banco de dados com informações falsas
class ReclamacoesSeeder extends Seeder
{
    //Função que gera as funções falsas
    public function run(): void
    {
        $moradores = Morador::orderBy('pk_id_morador')->get();

        //Cada morador terá uma reclamação pendente e uma já tratada pelo síndico
        foreach ($moradores as $indiceMorador => $morador) {
            //Reclamação ainda não visualizada
            Reclamacoes::factory()
                ->naoVisualizada()
                ->create([
                    'fk_id_morador_reclamacao' => $morador->pk_id_morador,
                ]);

            //Alterna entre reclamação apenas visualizada e reclamação respondida
            if ($indiceMorador % 2 === 0) {
                Reclamacoes::factory()
                    ->respondida()
                    ->create([
                        'fk_id_morador_reclamacao' => $morador->pk_id_morador,
                    ]);
            } else {
                Reclamacoes::factory()
                    ->visualizada()
                    ->create([
                        'fk_id_morador_reclamacao' => $morador->pk_id_morador,
                    ]);
            }
        }
    }
}
