<?php

namespace Database\Seeders;

use App\Models\Reclamacoes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//Classe que popula o banco de dados com informações falsas
class ReclamacoesSeeder extends Seeder
{
    //Função que gera as funções falsas
    public function run(): void
    {
        //Crinado os dados falsos
        Reclamacoes::factory(15)->create();
    }
}
