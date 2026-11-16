<?php

namespace Database\Factories;

use App\Models\Porteiro;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

//Classe que cria os dados de porteiros
class PorteiroFactory extends Factory
{
    protected $model = Porteiro::class;

    //Função que gera os dados de um porteiro com acesso completo
    public function definition(): array
    {
        $nome = $this->faker->name();

        return [
            'nome_porteiro' => $nome,
            'email_porteiro' => self::gerarEmailHotmail($nome),
            'telefone_porteiro' => '19999101302',
            'cpf_porteiro' => self::gerarCpfValido(),
            'senha_porteiro' => Hash::make('password'),
            'primeiro_acesso_porteiro' => true,
            'codigo_senha_porteiro' => null,
        ];
    }

    //Gera um email @hotmail.com a partir do nome
    public static function gerarEmailHotmail(string $nome): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nome);
        $ascii = strtolower($ascii ?: $nome);
        $slug = preg_replace('/[^a-z0-9]+/', '.', $ascii);
        $slug = trim((string) $slug, '.');

        return $slug . '@hotmail.com';
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
