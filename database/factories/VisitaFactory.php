<?php

namespace Database\Factories;

use App\Models\Morador;
use App\Models\Porteiro;
use App\Models\Visita;
use App\Models\Visitante;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitaFactory extends Factory
{
    protected $model = Visita::class;

    //Horário de funcionamento da portaria para visitas
    private const HORA_ABERTURA = 8;
    private const HORA_FECHAMENTO = 20;

    //Durações comuns de uma visita, em minutos
    private array $duracoes = [30, 45, 60, 90, 120, 180];

    public function definition(): array
    {
        //Por padrão, a visita está agendada e aguardando autorização do morador
        return $this->visitaAgendada(null);
    }

    //Visita agendada, aguardando autorização do morador
    public function agendada(): static
    {
        return $this->state(fn (array $attributes) => $this->visitaAgendada(null));
    }

    //Visita agendada e autorizada, mas ainda não registrada na portaria
    public function autorizada(): static
    {
        return $this->state(fn (array $attributes) => $this->visitaAgendada(true));
    }

    //Visita agendada e negada pelo morador
    public function negada(): static
    {
        return $this->state(fn (array $attributes) => $this->visitaAgendada(false));
    }

    //Visita em andamento: entrada registrada e saída ainda não registrada
    public function emAberto(): static
    {
        return $this->state(fn (array $attributes) => $this->visitaEmAberto());
    }

    //Visita já finalizada, com entrada e saída registradas na portaria
    public function finalizada(): static
    {
        return $this->state(fn (array $attributes) => $this->visitaFinalizada());
    }

    //Visita autorizada em que o visitante não compareceu
    public function naoCompareceu(): static
    {
        return $this->state(fn (array $attributes) => $this->visitaNaoCompareceu());
    }

    private function visitaAgendada(?bool $autorizacao): array
    {
        [$entradaPrevista, $saidaPrevista] = $this->janelaVisita('+1 day', '+30 days');

        return array_merge($this->relacionamentos(), [
            'hora_entrada_visita' => $entradaPrevista,
            'hora_saida_visita' => $saidaPrevista,
            'autorizacao_visita' => $autorizacao,
            'hora_registro_entrada_visita' => null,
            'fk_id_porteiro_entrada' => null,
            'hora_registro_saida_visita' => null,
            'fk_id_porteiro_saida' => null,
        ]);
    }

    private function visitaEmAberto(): array
    {
        //Entrada real registrada nas últimas horas
        $registroEntrada = $this->faker->dateTimeBetween('-3 hours', '-30 minutes');

        //A entrada prevista pode ter sido alguns minutos antes do registro real
        $entradaPrevista = (clone $registroEntrada)->modify(
            '-' . $this->faker->numberBetween(0, 20) . ' minutes'
        );

        //Como a visita ainda está em andamento, a saída prevista fica à frente
        $saidaPrevista = $this->faker->dateTimeBetween('+15 minutes', '+2 hours');

        return array_merge($this->relacionamentos(), [
            'hora_entrada_visita' => $entradaPrevista,
            'hora_saida_visita' => $saidaPrevista,
            'autorizacao_visita' => true,
            'hora_registro_entrada_visita' => $registroEntrada,
            'fk_id_porteiro_entrada' => $this->porteiroAleatorio(),
            'hora_registro_saida_visita' => null,
            'fk_id_porteiro_saida' => null,
        ]);
    }

    private function visitaFinalizada(): array
    {
        [$entradaPrevista, $saidaPrevista] = $this->janelaVisita('-30 days', '-1 day');

        //O registro real pode ter pequenos atrasos em relação ao previsto
        $registroEntrada = $this->registroProximo($entradaPrevista);
        $registroSaida = $this->registroProximo($saidaPrevista);

        //Garante que a saída seja sempre posterior à entrada
        if ($registroSaida <= $registroEntrada) {
            $registroSaida = (clone $registroEntrada)->modify('+30 minutes');
        }

        return array_merge($this->relacionamentos(), [
            'hora_entrada_visita' => $entradaPrevista,
            'hora_saida_visita' => $saidaPrevista,
            'autorizacao_visita' => true,
            'hora_registro_entrada_visita' => $registroEntrada,
            'fk_id_porteiro_entrada' => $this->porteiroAleatorio(),
            'hora_registro_saida_visita' => $registroSaida,
            'fk_id_porteiro_saida' => $this->porteiroAleatorio(),
        ]);
    }

    private function visitaNaoCompareceu(): array
    {
        [$entradaPrevista, $saidaPrevista] = $this->janelaVisita('-30 days', '-1 day');

        return array_merge($this->relacionamentos(), [
            'hora_entrada_visita' => $entradaPrevista,
            'hora_saida_visita' => $saidaPrevista,
            'autorizacao_visita' => true,
            'hora_registro_entrada_visita' => null,
            'fk_id_porteiro_entrada' => null,
            'hora_registro_saida_visita' => null,
            'fk_id_porteiro_saida' => null,
        ]);
    }

    private function janelaVisita(string $inicio, string $fim): array
    {
        //Sorteia o dia e um horário dentro do período de visitas do condomínio
        $dia = $this->faker->dateTimeBetween($inicio, $fim);

        $entrada = (clone $dia)->setTime(
            $this->faker->numberBetween(self::HORA_ABERTURA, self::HORA_FECHAMENTO - 2),
            $this->faker->randomElement([0, 15, 30, 45])
        );

        $saida = (clone $entrada)->modify(
            '+' . $this->faker->randomElement($this->duracoes) . ' minutes'
        );

        return [$entrada, $saida];
    }

    private function registroProximo(\DateTime $horario): \DateTime
    {
        //Simula atrasos ou adiantamentos de até 20 minutos no registro
        return (clone $horario)->modify(
            sprintf('%+d minutes', $this->faker->numberBetween(-10, 20))
        );
    }

    private function relacionamentos(): array
    {
        return [
            'fk_id_visitante' => Visitante::inRandomOrder()->firstOrFail()->pk_id_visitante,
            'fk_id_morador' => Morador::inRandomOrder()->firstOrFail()->pk_id_morador,
        ];
    }

    private function porteiroAleatorio(): int
    {
        return Porteiro::inRandomOrder()->firstOrFail()->pk_id_porteiro;
    }
}
