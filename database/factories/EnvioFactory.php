<?php

namespace Database\Factories;

use App\Models\Comunicado;
use App\Models\Envio;
use App\Models\Morador;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnvioFactory extends Factory
{
    protected $model = Envio::class;

    //Perguntas e respostas relacionadas, usadas nos estados abaixo
    private array $perguntas = [
        'Gostaria de saber se haverá alguma compensação pelo período sem elevador.' => 'A administração irá avaliar a situação e retornará com mais informações.',
        'A reforma da área de lazer afetará o uso do salão de festas?' => 'Não, o salão de festas continuará disponível durante a reforma.',
        'Podemos deixar as doações na portaria durante o final de semana?' => 'Sim, as doações podem ser entregues na portaria em qualquer horário.',
        'A dedetização será feita também nas garagens?' => 'Sim, a dedetização inclui as garagens e as áreas de circulação.',
        'Qual será o horário de funcionamento da portaria no feriado?' => 'A portaria funcionará em regime de plantão durante todo o feriado.',
    ];

    public function definition(): array
    {
        return [
            'fk_id_comunicados' => Comunicado::inRandomOrder()
                ->firstOrFail()
                ->pk_id_comunicados,

            'fk_id_morador' => Morador::inRandomOrder()
                ->firstOrFail()
                ->pk_id_morador,

            //Por padrão o comunicado ainda não foi visualizado nem respondido
            'resposta' => null,
            'contra_resposta' => null,
            'visualizado' => false,
        ];
    }

    //Estado de comunicado já visualizado pelo morador
    public function visualizado(): static
    {
        return $this->state(fn (array $attributes) => [
            'visualizado' => true,
        ]);
    }

    //Estado em que o morador visualizou e enviou uma pergunta
    public function comPergunta(): static
    {
        return $this->state(fn (array $attributes) => [
            'visualizado' => true,
            'resposta' => $this->faker->randomElement(array_keys($this->perguntas)),
            'contra_resposta' => null,
        ]);
    }

    //Estado em que o morador enviou uma pergunta e o síndico já respondeu
    public function respondido(): static
    {
        return $this->state(function (array $attributes) {
            $pergunta = $this->faker->randomElement(array_keys($this->perguntas));

            return [
                'visualizado' => true,
                'resposta' => $pergunta,
                'contra_resposta' => $this->perguntas[$pergunta],
            ];
        });
    }
}
