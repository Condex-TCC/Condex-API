<?php

namespace Database\Seeders;

use App\Models\Morador;
use App\Models\Unidade;
use Database\Factories\MoradorFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

//Classe que popula o banco de dados com moradores
class MoradorSeeder extends Seeder
{
    //Função que gera os moradores
    public function run(): void
    {
        $unidades = Unidade::orderBy('pk_id_unidades')->get();

        $moradores = [
            [
                'nome_morador' => 'Murilo Trida Costa',
                'email_morador' => 'murilo.trida.costa@hotmail.com',
            ],
            [
                'nome_morador' => 'Lavínia Alessandra Silva',
                'email_morador' => 'lavinia.alessandra.silva@hotmail.com',
            ],
            [
                'nome_morador' => 'Letícia Bernardi',
                'email_morador' => 'leticia.bernardi@hotmail.com',
            ],
            [
                'nome_morador' => 'Matias Godoy Bigon',
                'email_morador' => 'matias.godoy.bigon@hotmail.com',
            ],
            [
                'nome_morador' => 'Lucas Cimenton Monocello',
                'email_morador' => 'lucas.cimenton.monocello@hotmail.com',
            ],
        ];

        foreach ($moradores as $index => $dados) {
            Morador::factory()->create([
                'nome_morador' => $dados['nome_morador'],
                'email_morador' => $dados['email_morador'],
                'cpf_morador' => MoradorFactory::gerarCpfValido(),
                'telefone_morador' => '19999101302',
                'senha_morador' => Hash::make('password'),
                'primeiro_acesso_morador' => true,
                'codigo_senha_morador' => null,
                'fk_id_unidade_morador' => $unidades[$index]->pk_id_unidades,
            ]);
        }
    }
}
