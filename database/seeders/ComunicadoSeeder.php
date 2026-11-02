<?php

namespace Database\Seeders;

use App\Models\Comunicado;
use Illuminate\Database\Seeder;

class ComunicadoSeeder extends Seeder
{
    public function run(): void
    {
        //Comunicados com datas variadas para simular o histórico do condomínio
        $comunicados = [
            [
                'titulo_comunicado' => 'Manutenção preventiva do elevador',
                'descricao_comunicado' => 'Informamos que o elevador do Bloco A passará por manutenção preventiva na próxima segunda-feira, das 8h às 12h. Durante esse período, o uso do elevador estará indisponível.',
                'dias_atras' => 45,
            ],
            [
                'titulo_comunicado' => 'Limpeza da caixa d\'água',
                'descricao_comunicado' => 'A limpeza da caixa d\'água será realizada na próxima quarta-feira, das 9h às 16h. Pedimos que os moradores reservem água para o consumo durante o período.',
                'dias_atras' => 38,
            ],
            [
                'titulo_comunicado' => 'Assembleia geral ordinária',
                'descricao_comunicado' => 'Convocamos todos os moradores para a assembleia geral ordinária, que ocorrerá no salão de festas no dia 20 às 19h. A pauta inclui a prestação de contas e o orçamento do próximo ano.',
                'dias_atras' => 30,
            ],
            [
                'titulo_comunicado' => 'Reforma da área de lazer',
                'descricao_comunicado' => 'A partir da próxima semana, a área de lazer passará por reforma para melhorias na piscina e no deck. Durante as obras, o acesso ao local ficará restrito aos funcionários.',
                'dias_atras' => 21,
            ],
            [
                'titulo_comunicado' => 'Campanha de doação de agasalhos',
                'descricao_comunicado' => 'Estamos arrecadando agasalhos e cobertores para a campanha do agasalho. As doações podem ser entregues na portaria até o final do mês.',
                'dias_atras' => 14,
            ],
            [
                'titulo_comunicado' => 'Controle de acesso de visitantes',
                'descricao_comunicado' => 'Reforçamos que todos os visitantes devem ser cadastrados previamente pelos moradores. A portaria realizará a conferência dos documentos na entrada.',
                'dias_atras' => 7,
            ],
            [
                'titulo_comunicado' => 'Dedetização das áreas comuns',
                'descricao_comunicado' => 'A dedetização das áreas comuns será realizada no próximo sábado, das 7h às 11h. Recomendamos manter janelas e portas fechadas durante o procedimento.',
                'dias_atras' => 3,
            ],
            [
                'titulo_comunicado' => 'Horário de funcionamento da portaria',
                'descricao_comunicado' => 'A portaria funcionará em regime de plantão durante o feriado. Para emergências, utilize o telefone fixo da guarita.',
                'dias_atras' => 1,
            ],
        ];

        foreach ($comunicados as $dados) {
            //Cria o comunicado usando a factory e os dados definidos
            $comunicado = Comunicado::factory()->create([
                'titulo_comunicado' => $dados['titulo_comunicado'],
                'descricao_comunicado' => $dados['descricao_comunicado'],
            ]);

            //Ajusta a data de criação para deixar o histórico coerente
            $comunicado->forceFill([
                'created_at' => now()->subDays($dados['dias_atras']),
            ])->saveQuietly();
        }
    }
}
