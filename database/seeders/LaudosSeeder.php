<?php

namespace Database\Seeders;

use App\Models\Laudos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//Classe que popula o banco de dados com informações falsas
class LaudosSeeder extends Seeder
{
    //Função que gera as funções falsas
    public function run(): void
    {
        //Crinado os dados falsos
        Laudos::factory(8)->create();
    }
}
