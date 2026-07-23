<?php

namespace Database\Seeders;

use App\Models\Morador;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//Classe que popula o banco de dados com informações falsas
class MoradorSeeder extends Seeder
{
    //Função que gera as funções falsas
    public function run(): void
    {
        //Crinado os dados falsos
        Morador::factory(20)->create();
    }
}
