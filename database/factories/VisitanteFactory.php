<?php

namespace Database\Factories;

use App\Models\Visitante;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitanteFactory extends Factory
{
    protected $model = Visitante::class;

    public function definition(): array
    {
        //Nomes e sobrenomes comuns, para gerar visitantes mais realistas
        $nomes = [
            'Ana', 'Bruno', 'Carla', 'Daniel', 'Eduarda',
            'Felipe', 'Gabriela', 'Henrique', 'Isabela', 'João',
            'Larissa', 'Marcos', 'Natália', 'Otávio', 'Patrícia',
            'Rafael', 'Sofia', 'Thiago', 'Vanessa', 'Wagner',
        ];

        $sobrenomes = [
            'Almeida', 'Barbosa', 'Cardoso', 'Dias', 'Ferreira',
            'Gomes', 'Lima', 'Martins', 'Nogueira', 'Oliveira',
            'Pereira', 'Ribeiro', 'Santos', 'Silva', 'Souza', 'Teixeira',
        ];

        //Garante dois sobrenomes diferentes, como em nomes completos reais
        $sobrenomesSorteados = $this->faker->randomElements($sobrenomes, 2);

        return [
            'nome_visitante' => $this->faker->randomElement($nomes) . ' ' . implode(' ', $sobrenomesSorteados),
            'cpf_visitante' => $this->faker->unique()->numerify('###.###.###-##'),
            'rg_morador' => $this->faker->unique()->numerify('##.###.###-#'),
        ];
    }
}
