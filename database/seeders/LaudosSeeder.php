<?php

namespace Database\Seeders;

use App\Models\Laudos;
use Illuminate\Database\Seeder;

//Classe que popula o banco de dados com informações falsas
class LaudosSeeder extends Seeder
{
    //Função que gera as funções falsas
    public function run(): void
    {
        //Laudos fixos para garantir nomes e caminhos coerentes
        $laudos = [
            [
                'nome_laudo' => 'Laudo de vistoria estrutural',
                'caminho_laudo' => 'laudos/vistoria-estrutural.pdf',
            ],
            [
                'nome_laudo' => 'Laudo de manutenção do elevador',
                'caminho_laudo' => 'laudos/manutencao-elevador.pdf',
            ],
            [
                'nome_laudo' => 'Laudo de dedetização',
                'caminho_laudo' => 'laudos/dedetizacao.pdf',
            ],
            [
                'nome_laudo' => 'Laudo de limpeza da caixa d\'água',
                'caminho_laudo' => 'laudos/limpeza-caixa-dagua.pdf',
            ],
            [
                'nome_laudo' => 'Laudo de inspeção elétrica',
                'caminho_laudo' => 'laudos/inspecao-eletrica.pdf',
            ],
            [
                'nome_laudo' => 'Laudo de prevenção e combate a incêndio',
                'caminho_laudo' => 'laudos/prevencao-incendio.pdf',
            ],
            [
                'nome_laudo' => 'Laudo de impermeabilização da laje',
                'caminho_laudo' => 'laudos/impermeabilizacao-laje.pdf',
            ],
            [
                'nome_laudo' => 'Laudo de acessibilidade das áreas comuns',
                'caminho_laudo' => 'laudos/acessibilidade.pdf',
            ],
        ];

        foreach ($laudos as $laudo) {
            Laudos::factory()->create($laudo);
        }
    }
}
