<?php

namespace Database\Factories;

use App\Models\Porteiro;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

//Classe que cria os dados falsos
class PorteiroFactory extends Factory
{
    //Função que gera os dados falsos
    public function definition(): array
    {
        //Retorna os dados falsos
        return [
            'nome_porteiro' => $this->faker->name(), //Gera um nome falso
            'email_porteiro' => $this->faker->email(), //Gera um email falso
            'senha_porteiro' => Hash::make('password') //Gera um hash, onde a senha sempre será password
        ];
    }
}
