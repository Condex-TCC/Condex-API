<?php

namespace Database\Factories;

use App\Models\Morador;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

//Classe que cria os dados falsos
class MoradorFactory extends Factory
{
    //Função que gera os dados falsos
    public function definition(): array
    {
        //Retorna os dados falsos
        return [
            "nome_morador" => $this->faker->name(), //Gera um nome falso
            "cpf_morador" => $this->faker->numberBetween(1000, 100000),
            "email_morador" => $this->faker->email(), //Gera um email falso
            "telefone_morador" => $this->faker->phoneNumber(), //Gera um número de telefone falso
            "senha_morador" => Hash::make("password"), //Gera uma senha estatica, password
            "fk_id_unidade_morador" => Unidade::all()->random()->pk_id_unidades, //Pega um id aleatorio de unidade
        ];
    }
}
