<?php

namespace Database\Seeders;

use App\Models\Unidade;
use Illuminate\Database\Seeder;

//Classe que popula o banco de dados com informações falsas
class UnidadeSeeder extends Seeder
{
    //Função que gera as funções falsas
    public function run(): void
    {
        //Criando 10 unidades, uma para cada morador de teste
        Unidade::factory()->count(10)->create();
    }
}
