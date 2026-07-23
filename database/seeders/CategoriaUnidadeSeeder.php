<?php

namespace Database\Seeders;

use App\Models\Categoria_unidade;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//Classe que popula o banco de dados com informações falsas
class CategoriaUnidadeSeeder extends Seeder
{
    //Função que gera as funções falsas
    public function run(): void
    {
        //Gera os dados falso
        Categoria_unidade::factory(15)->create();
    }
}
