<?php

namespace Database\Factories;

use App\Models\Sindico;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

//Classe que cria os dados falsos
class SindicoFactory extends Factory
{
    //Função que gera os dados falsos
    public function definition(): array
    {
        //Retorna os dados falsos
        return [
            "nome_sindico" => $this->faker->name(), //Gera um nome falso
            "telefone_sindico" => $this->faker->phoneNumber(), //Gera um número de telfone falso
            "email_sindico" => $this->faker->email(), //Gera um email falos
            "senha_sindico" => Hash::make("password") //gera uma hash, mas todas as senhas são password
        ];
    }
}
