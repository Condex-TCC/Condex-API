<?php

namespace Database\Seeders;

use App\Models\Regras;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//Classe que popula o banco de dados com informações falsas
class RegrasSeeder extends Seeder
{
    //Função que gera as funções falsas
    public function run(): void
    {
        //Crinado os dados falsos
        Regras::factory(20)->create();
    }
}
