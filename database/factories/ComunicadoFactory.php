<?php

namespace Database\Factories;

use App\Models\Comunicado;
use App\Models\Sindico;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComunicadoFactory extends Factory
{
    protected $model = Comunicado::class;

    public function definition(): array
    {
        //Comunicados realistas com título e descrição relacionados
        $comunicados = [
            [
                'titulo_comunicado' => 'Manutenção preventiva do elevador',
                'descricao_comunicado' => 'Informamos que o elevador do Bloco A passará por manutenção preventiva na próxima segunda-feira, das 8h às 12h. Durante esse período, o uso do elevador estará indisponível.',
            ],
            [
                'titulo_comunicado' => 'Limpeza da caixa d\'água',
                'descricao_comunicado' => 'A limpeza da caixa d\'água será realizada na próxima quarta-feira, das 9h às 16h. Pedimos que os moradores reservem água para o consumo durante o período.',
            ],
            [
                'titulo_comunicado' => 'Assembleia geral ordinária',
                'descricao_comunicado' => 'Convocamos todos os moradores para a assembleia geral ordinária, que ocorrerá no salão de festas no dia 20 às 19h. A pauta inclui a prestação de contas e o orçamento do próximo ano.',
            ],
            [
                'titulo_comunicado' => 'Reforma da área de lazer',
                'descricao_comunicado' => 'A partir da próxima semana, a área de lazer passará por reforma para melhorias na piscina e no deck. Durante as obras, o acesso ao local ficará restrito aos funcionários.',
            ],
            [
                'titulo_comunicado' => 'Campanha de doação de agasalhos',
                'descricao_comunicado' => 'Estamos arrecadando agasalhos e cobertores para a campanha do agasalho. As doações podem ser entregues na portaria até o final do mês.',
            ],
            [
                'titulo_comunicado' => 'Controle de acesso de visitantes',
                'descricao_comunicado' => 'Reforçamos que todos os visitantes devem ser cadastrados previamente pelos moradores. A portaria realizará a conferência dos documentos na entrada.',
            ],
            [
                'titulo_comunicado' => 'Dedetização das áreas comuns',
                'descricao_comunicado' => 'A dedetização das áreas comuns será realizada no próximo sábado, das 7h às 11h. Recomendamos manter janelas e portas fechadas durante o procedimento.',
            ],
            [
                'titulo_comunicado' => 'Horário de funcionamento da portaria',
                'descricao_comunicado' => 'A portaria funcionará em regime de plantão durante o feriado. Para emergências, utilize o telefone fixo da guarita.',
            ],
        ];

        //Sorteia um comunicado do array
        $comunicado = $this->faker->randomElement($comunicados);

        return [
            'titulo_comunicado' => $comunicado['titulo_comunicado'],
            'descricao_comunicado' => $comunicado['descricao_comunicado'],
            'fk_id_sindico_comunicados' => Sindico::inRandomOrder()->firstOrFail()->pk_id_sindico,
        ];
    }
}
