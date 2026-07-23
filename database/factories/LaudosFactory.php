<?php

namespace Database\Factories;

use App\Models\Laudos;
use App\Models\Sindico;
use Illuminate\Database\Eloquent\Factories\Factory;

//Classe que cria os dados falsos
class LaudosFactory extends Factory
{
    //Função que gera os dados falsos
    public function definition(): array
    {
        //Retorna os dados falsos
        return [
            "nome_laudo" => "Laudo do condiminio - N° 777", //Gera um nome do laudo falso, mas é estatico
            "caminho_laudo" => "teste.pdf", //Gera um caminho para laudo de teste
            "fk_id_sindico_laudo" => Sindico::all()->random()->pk_id_sindico, //Pega um id aelatorio do sindico
        ];
    }
}
