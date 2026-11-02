<?php

namespace Database\Seeders;

use App\Models\Regras;
use Illuminate\Database\Seeder;

//Classe que popula o banco de dados com informações falsas
class RegrasSeeder extends Seeder
{
    //Função que gera as funções falsas
    public function run(): void
    {
        //Regras fixas para garantir nomes e descrições coerentes
        $regras = [
            [
                'nome_regra' => 'Horário de silêncio',
                'descricao_regra' => 'É proibido produzir ruídos que perturbem o sossego dos moradores entre 22h e 7h, conforme o regulamento interno do condomínio.',
            ],
            [
                'nome_regra' => 'Uso do salão de festas',
                'descricao_regra' => 'O salão de festas deve ser reservado com antecedência e o evento deve terminar até as 23h. O responsável pela reserva deve deixar o local limpo.',
            ],
            [
                'nome_regra' => 'Uso da piscina',
                'descricao_regra' => 'A piscina pode ser utilizada das 8h às 22h. Crianças menores de 12 anos devem estar acompanhadas por um responsável.',
            ],
            [
                'nome_regra' => 'Circulação de visitantes',
                'descricao_regra' => 'Todos os visitantes devem ser cadastrados na portaria e portar documento de identificação durante a permanência no condomínio.',
            ],
            [
                'nome_regra' => 'Uso das vagas de garagem',
                'descricao_regra' => 'Cada morador deve utilizar apenas a vaga vinculada à sua unidade. É proibido estacionar em vagas de terceiros.',
            ],
            [
                'nome_regra' => 'Descarte de lixo',
                'descricao_regra' => 'O lixo deve ser acondicionado em sacos fechados e depositado nos coletores da área comum nos horários estabelecidos.',
            ],
            [
                'nome_regra' => 'Animais de estimação',
                'descricao_regra' => 'Os animais devem circular nas áreas comuns sempre acompanhados pelo responsável e com coleira. É obrigatório recolher as fezes.',
            ],
            [
                'nome_regra' => 'Mudanças e obras',
                'descricao_regra' => 'Mudanças e obras devem ser comunicadas à administração com antecedência e realizadas de segunda a sexta-feira, das 8h às 17h.',
            ],
            [
                'nome_regra' => 'Uso da churrasqueira',
                'descricao_regra' => 'A churrasqueira deve ser reservada previamente e limpa após o uso. O horário de utilização é das 11h às 22h.',
            ],
            [
                'nome_regra' => 'Taxa condominial',
                'descricao_regra' => 'A taxa condominial deve ser paga até o dia 10 de cada mês. O atraso sujeita o morador à multa e juros previstos na convenção.',
            ],
        ];

        foreach ($regras as $regra) {
            Regras::factory()->create($regra);
        }
    }
}
