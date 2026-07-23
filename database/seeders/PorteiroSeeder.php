<?php

namespace Database\Seeders;

use App\Models\Porteiro;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//Classe que popula o banco de dados com informações falsas
class PorteiroSeeder extends Seeder
{
    //Função que gera as funções falsas
    public function run(): void
    {
        //Crinado os dados falsos
        Porteiro::factory(3)->create();
    }
}
