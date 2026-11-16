<?php

namespace Database\Seeders;

use App\Models\Porteiro;
use Database\Factories\PorteiroFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

//Classe que popula o banco de dados com porteiros
class PorteiroSeeder extends Seeder
{
    //Função que gera os porteiros
    public function run(): void
    {
        $porteiros = [
            [
                'nome_porteiro' => 'João da silva',
                'email_porteiro' => 'joao.da.silva@hotmail.com',
            ],
            [
                'nome_porteiro' => 'Lenadro Costa',
                'email_porteiro' => 'lenadro.costa@hotmail.com',
            ],
            [
                'nome_porteiro' => 'Fernando Alexandre',
                'email_porteiro' => 'fernando.alexandre@hotmail.com',
            ],
        ];

        foreach ($porteiros as $dados) {
            Porteiro::factory()->create([
                'nome_porteiro' => $dados['nome_porteiro'],
                'email_porteiro' => $dados['email_porteiro'],
                'telefone_porteiro' => '19999101302',
                'cpf_porteiro' => PorteiroFactory::gerarCpfValido(),
                'senha_porteiro' => Hash::make('password'),
                'primeiro_acesso_porteiro' => true,
                'codigo_senha_porteiro' => null,
            ]);
        }
    }
}
