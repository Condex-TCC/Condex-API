<?php

namespace Database\Factories;

use App\Models\Sindico;
use Illuminate\Database\Eloquent\Factories\Factory;

//Classe que cria os dados falsos
class RegrasFactory extends Factory
{
    //Função que gera os dados falsos
    public function definition(): array
    {
        //Retorna os dados falsos
        return [
            "nome_regra" => "Regra do condominio - N° 777", //Gera os nomes das regras igual 
            "descricao_regra" => $this->faker->text(), //Gara um texto falso
            "fk_id_sindico_regras" => Sindico::all()->random()->pk_id_sindico, //Pega um id aleatorio do sindico
        ];
    }
}
