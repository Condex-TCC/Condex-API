<?php

namespace Database\Factories;

use App\Models\Morador;
use App\Models\Reclamacoes;
use Illuminate\Database\Eloquent\Factories\Factory;

//Classe que cria os dados falsos
class ReclamacoesFactory extends Factory
{  

    //Função que gera os dados falsos
    public function definition(): array
    {   
        //Váriavel que armazena se o sindico vizualizou o comunicado
        $visualidado = $this->faker->boolean();

        //Váriavel que armazena se o sindico response o comunicado, mas somente se ele viu
        $resonsta = false;

        //Verifica se o sindico vizualizou
        if($visualidado == true){

            $resonsta = $this->faker->boolean();
        }

        //Retorna os dados falsos
        return [
            "reclamacao" => $this->faker->text(),
            "resposta_reclamacao" => $resonsta == true ? $this->faker->text() : null,
            "visualizado_reclamacao" => $visualidado,
            "fk_id_morador_reclamacao" =>  Morador::inRandomOrder()->first()->pk_id_morador,
            "fk_id_sindico_reclamacao" => 1,
        ];
    }
}
