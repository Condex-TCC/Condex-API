<?php

namespace Database\Factories;

use App\Models\Categoria_unidade;
use Illuminate\Database\Eloquent\Factories\Factory;

//Classe que cria os dados falsos
class CategoriaUnidadeFactory extends Factory
{
    protected $model = Categoria_unidade::class;

    //Função que gera os dados falsos
    public function definition(): array
    {   
        //Retorna os dados falsos
        return [
            "descricao_categoria_unidade" => $this->faker->text(), //Gera um texto falso
        ];
    }
}
