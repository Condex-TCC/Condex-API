<?php

namespace Database\Factories;

use App\Models\Espaco;
use Illuminate\Database\Eloquent\Factories\Factory;

class EspacoFactory extends Factory
{
    //Model utilizado pela factory
    protected $model = Espaco::class;

    public function definition(): array
    {
        //Array com os espaços e suas descrições
        $espacos = [
            [
                'nome_espaco' => 'Salão de Festas',
                'descricao_espaco' => 'Espaço amplo para eventos e confraternizações',
            ],
            [
                'nome_espaco' => 'Piscina',
                'descricao_espaco' => 'Área de lazer com piscina e deck',
            ],
            [
                'nome_espaco' => 'Academia',
                'descricao_espaco' => 'Espaço equipado para atividades físicas',
            ],
            [
                'nome_espaco' => 'Churrasqueira',
                'descricao_espaco' => 'Área com churrasqueira e mesas',
            ],
            [
                'nome_espaco' => 'Quadra Esportiva',
                'descricao_espaco' => 'Quadra para práticas esportivas',
            ],
            [
                'nome_espaco' => 'Playground',
                'descricao_espaco' => 'Espaço recreativo para crianças',
            ],
        ];

        //Sorteia um espaço e adiciona a informação de autorização
        return fake()->randomElement($espacos) + [
            'autorizacao' => fake()->boolean(70) //A maioria dos espaços exige autorização
        ];
    }
}
