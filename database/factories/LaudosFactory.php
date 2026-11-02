<?php

namespace Database\Factories;

use App\Models\Sindico;
use Illuminate\Database\Eloquent\Factories\Factory;

//Classe que cria os dados falsos
class LaudosFactory extends Factory
{
    //Função que gera os dados falsos
    public function definition(): array
    {
        //Laudos realistas do condomínio
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

        $laudo = $this->faker->randomElement($laudos);

        return [
            'nome_laudo' => $laudo['nome_laudo'],
            'caminho_laudo' => $laudo['caminho_laudo'],
            'fk_id_sindico_laudo' => Sindico::inRandomOrder()->firstOrFail()->pk_id_sindico,
        ];
    }
}
