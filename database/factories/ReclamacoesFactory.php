<?php

namespace Database\Factories;

use App\Models\Morador;
use App\Models\Reclamacoes;
use App\Models\Sindico;
use Illuminate\Database\Eloquent\Factories\Factory;

//Classe que cria os dados falsos
class ReclamacoesFactory extends Factory
{
    protected $model = Reclamacoes::class;

    //Função que gera os dados falsos
    public function definition(): array
    {
        //Reclamações realistas para os testes do condomínio
        $reclamacoes = [
            'O barulho de obras no apartamento vizinho está acontecendo fora do horário permitido.',
            'A lâmpada do corredor do meu andar está queimada há vários dias.',
            'O portão da garagem está abrindo e fechando sozinho durante a noite.',
            'Há vazamento de água na área comum próxima ao elevador.',
            'Os cachorros de um morador estão fazendo necessidades na área de lazer.',
            'A música alta no salão de festas está ultrapassando o horário permitido.',
            'O interfone da minha unidade não está funcionando corretamente.',
            'Há lixo acumulado próximo à entrada do bloco B.',
            'A vaga de garagem destinada à minha unidade está sendo ocupada por outro veículo.',
            'O elevador está apresentando ruídos estranhos durante o uso.',
        ];

        //A reclamação é considerada visualizada de forma aleatória
        $visualizado = $this->faker->boolean();

        //Só existe resposta se o síndico já visualizou a reclamação
        $resposta = $visualizado && $this->faker->boolean()
            ? $this->faker->randomElement([
                'A administração já acionou a equipe responsável e o problema será resolvido em breve.',
                'A ocorrência foi registrada e os moradores envolvidos serão notificados.',
                'A manutenção foi agendada para esta semana. Agradecemos o contato.',
                'O setor responsável foi informado e fará a verificação no local.',
                'A situação já está sendo acompanhada pela administração do condomínio.',
            ])
            : null;

        //Retorna os dados falsos
        return [
            'reclamacao' => $this->faker->randomElement($reclamacoes),
            'resposta_reclamacao' => $resposta,
            'visualizado_reclamacao' => $visualizado,
            'fk_id_morador_reclamacao' => Morador::inRandomOrder()->firstOrFail()->pk_id_morador,
            'fk_id_sindico_reclamacao' => Sindico::inRandomOrder()->firstOrFail()->pk_id_sindico,
        ];
    }

    //Estado de reclamação ainda não visualizada pelo síndico
    public function naoVisualizada(): static
    {
        return $this->state(fn (array $attributes) => [
            'visualizado_reclamacao' => false,
            'resposta_reclamacao' => null,
        ]);
    }

    //Estado de reclamação visualizada, mas ainda sem resposta
    public function visualizada(): static
    {
        return $this->state(fn (array $attributes) => [
            'visualizado_reclamacao' => true,
            'resposta_reclamacao' => null,
        ]);
    }

    //Estado de reclamação visualizada e respondida pelo síndico
    public function respondida(): static
    {
        return $this->state(fn (array $attributes) => [
            'visualizado_reclamacao' => true,
            'resposta_reclamacao' => $this->faker->randomElement([
                'A administração já acionou a equipe responsável e o problema será resolvido em breve.',
                'A ocorrência foi registrada e os moradores envolvidos serão notificados.',
                'A manutenção foi agendada para esta semana. Agradecemos o contato.',
                'O setor responsável foi informado e fará a verificação no local.',
                'A situação já está sendo acompanhada pela administração do condomínio.',
            ]),
        ]);
    }
}
