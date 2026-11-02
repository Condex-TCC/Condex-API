<?php

namespace Database\Seeders;

use App\Models\Morador;
use App\Models\Unidade;
use Illuminate\Database\Seeder;

//Classe que popula o banco de dados com informações falsas
class MoradorSeeder extends Seeder
{
    //Função que gera as funções falsas
    public function run(): void
    {
        //As 10 unidades criadas serão distribuídas entre os moradores
        $unidades = Unidade::orderBy('pk_id_unidades')->get();

        //Criando 5 moradores com todos os dados preenchidos
        foreach ($unidades->take(5) as $unidade) {
            Morador::factory()->create([
                'fk_id_unidade_morador' => $unidade->pk_id_unidades,
            ]);
        }

        //Criando 5 moradores apenas com email, unidade e senha para o primeiro acesso
        foreach ($unidades->slice(5, 5) as $unidade) {
            Morador::factory()->primeiroAcesso()->create([
                'fk_id_unidade_morador' => $unidade->pk_id_unidades,
            ]);
        }
    }
}
