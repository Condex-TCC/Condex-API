<?php

namespace Database\Factories;

use App\Models\Morador;
use App\Models\Unidade;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

//Classe que cria os dados falsos
class MoradorFactory extends Factory
{
    //Model utilizado pela factory
    protected $model = Morador::class;

    //Função que gera os dados falsos de um morador completo
    public function definition(): array
    {
        //Retorna os dados falsos
        return [
            "nome_morador" => $this->faker->name(), //Gera um nome falso
            "cpf_morador" => $this->faker->unique()->numerify('###.###.###-##'), //Gera um CPF falso
            "email_morador" => $this->faker->unique()->safeEmail(), //Gera um email falso
            "telefone_morador" => $this->faker->phoneNumber(), //Gera um número de telefone falso
            "senha_morador" => Hash::make("password"), //Gera uma senha estatica, password
            "primeiro_acesso_morador" => true, //Morador com o perfil completo
            "codigo_senha_morador" => null, //Não possui código de redefinição pendente
            "fk_id_unidade_morador" => Unidade::inRandomOrder()->firstOrFail()->pk_id_unidades, //Pega um id aleatorio de unidade
        ];
    }

    //Estado que cria um morador com apenas os dados necessários para o primeiro acesso
    public function primeiroAcesso(): static
    {
        return $this->state(fn (array $attributes) => [
            "nome_morador" => null,
            "cpf_morador" => null,
            "telefone_morador" => null,
            "senha_morador" => Hash::make("password"), //Mantém a senha padrão para conseguir logar
            "primeiro_acesso_morador" => false, //Ainda precisa completar o primeiro acesso
            "codigo_senha_morador" => null,
        ]);
    }
}
