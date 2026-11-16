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
            'cpf_visitante' => self::gerarCpfValido(),
            'rg_morador' => $this->faker->unique()->numerify('#########'),
        ];
    }

    //Gera um CPF válido contendo apenas números
    public static function gerarCpfValido(): string
    {
        do {
            $n = [];
            for ($i = 0; $i < 9; $i++) {
                $n[$i] = random_int(0, 9);
            }

            $d1 = 0;
            for ($i = 0, $peso = 10; $i < 9; $i++, $peso--) {
                $d1 += $n[$i] * $peso;
            }
            $r1 = $d1 % 11;
            $n[9] = ($r1 < 2) ? 0 : 11 - $r1;

            $d2 = 0;
            for ($i = 0, $peso = 11; $i < 10; $i++, $peso--) {
                $d2 += $n[$i] * $peso;
            }
            $r2 = $d2 % 11;
            $n[10] = ($r2 < 2) ? 0 : 11 - $r2;

            $cpf = implode('', $n);
        } while (preg_match('/^(\\d)\\1{10}$/', $cpf));

        return $cpf;
    }
}
