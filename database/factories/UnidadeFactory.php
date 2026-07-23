<?php

namespace Database\Factories;

use App\Models\Categoria_unidade;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\Factories\Factory;

//Classe que cria os dados falsos
class UnidadeFactory extends Factory
{
    //Função que gera os dados falsos
    public function definition(): array
    {   
        //Array com os os nomes dos blocos
        $blocos = ["Bloco A", "Bloco B", "Bloco C", "Bloco D"];

        //Retorna os dados falsos
        return [
            "bloco_unidade" => $this->faker->randomElement($blocos), //Sorteia um elemento aleatorio do array de blocos
            "numero_unidade" => $this->faker->numberBetween(0, 200), //Gera um número aleatorio entre 0 e 200
            "fk_id_unidade_categoria" => Categoria_unidade::all()->random()->pk_id_categoria_unidade, //Pega um id aleatorio
        ];
    }
}
