<?php

namespace Database\Seeders;

use App\Models\Espaco;
use Illuminate\Database\Seeder;

class EspacoSeeder extends Seeder
{
    public function run(): void
    {
        //Espaços comuns de um condomínio, com nomes e descrições coerentes
        $espacos = [
            [
                'nome_espaco' => 'Salão de Festas',
                'descricao_espaco' => 'Espaço amplo para eventos e confraternizações dos moradores',
                'autorizacao' => true,
            ],
            [
                'nome_espaco' => 'Piscina',
                'descricao_espaco' => 'Área de lazer com piscina adulto e infantil',
                'autorizacao' => false,
            ],
            [
                'nome_espaco' => 'Academia',
                'descricao_espaco' => 'Espaço equipado para a prática de atividades físicas',
                'autorizacao' => false,
            ],
            [
                'nome_espaco' => 'Churrasqueira',
                'descricao_espaco' => 'Área com churrasqueira, mesas e bancos para uso dos moradores',
                'autorizacao' => true,
            ],
            [
                'nome_espaco' => 'Quadra Esportiva',
                'descricao_espaco' => 'Quadra poliesportiva para futebol, vôlei e basquete',
                'autorizacao' => true,
            ],
            [
                'nome_espaco' => 'Playground',
                'descricao_espaco' => 'Espaço recreativo com brinquedos para as crianças',
                'autorizacao' => false,
            ],
        ];

        //Cria cada espaço com os dados definidos acima
        foreach ($espacos as $espaco) {
            Espaco::factory()->create($espaco);
        }
    }
}
