<?php

namespace Database\Factories;

use App\Models\Unidade;
use Illuminate\Database\Eloquent\Factories\Factory;

//Classe que cria os dados falsos
class UnidadeFactory extends Factory
{
    //Model utilizado pela factory
    protected $model = Unidade::class;

    //Função que gera os dados falsos
    public function definition(): array
    {
        //Array com os nomes dos blocos
        $blocos = ["Bloco A", "Bloco B", "Bloco C", "Bloco D"];

        //Array com as descrições das unidades
        $descricoes = [
            "Apartamento com 2 quartos e 1 vaga de garagem",
            "Apartamento com 3 quartos e 2 vagas de garagem",
            "Cobertura com varanda e churrasqueira",
            "Apartamento térreo com quintal",
        ];

        //Retorna os dados falsos
        return [
            "bloco_unidade" => $this->faker->randomElement($blocos), //Sorteia um elemento aleatorio do array de blocos
            "numero_unidade" => (string) $this->faker->unique()->numberBetween(101, 499), //Gera um número único para a unidade
            "descricao_unidade" => $this->faker->randomElement($descricoes), //Sorteia uma descrição para a unidade
        ];
    }
}
