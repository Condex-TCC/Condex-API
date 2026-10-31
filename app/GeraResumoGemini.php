<?php

namespace App;

use App\Models\Comunicado;
use App\Models\Envio;
use App\Models\Reclamacoes;
use App\Models\Reserva;
use App\Service\ServiceGemini;

/*
|--------------------------------------------------------------------------
| O QUE É ESTE ARQUIVO (TRAIT)
|--------------------------------------------------------------------------
|
| Uma TRAIT é um "pacote de métodos" que pode ser colado dentro de qualquer
| classe com a palavra "use". Não dá para criar um objeto direto de uma trait:
| ela só existe para ser usada por outras classes (aqui, os controllers).
|
| Este é o elo do meio do fluxo:
|
|   Controller  --(use GeraResumoGemini)-->  Trait  --(new)-->  ServiceGemini  -->  Gemini
|
| Por que ela existe? Para o controller não precisar conhecer as consultas ao
| banco nem o formato dos dados enviados ao Gemini. O controller só chama
| gerarResumoCondominio() e recebe o texto pronto.
|
*/

//Trait que oferece a geração do resumo do condomínio para os controllers
trait GeraResumoGemini
{
    //Função que o controller chama para gerar o resumo
    //Retorna o texto gerado pelo Gemini (ou a mensagem de falha padrão do service)
    protected function gerarResumoCondominio(): string
    {
        //Pegando as 50 reclamações mais recentes
        //O "desc" garante que as criadas por último apareçam primeiro
        $reclamacoes = Reclamacoes::orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        //Pegando as 50 reservas mais recentes
        $reservas = Reserva::orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        //Pegando o último comunicado cadastrado
        $ultimoComunicado = Comunicado::orderBy('created_at', 'desc')->first();

        //Variável que armazena a quantidade de dúvidas do último comunicado
        $quantidadeDuvidas = 0;

        //Só consulta as dúvidas se existir um comunicado cadastrado
        if ($ultimoComunicado) {

            //Conta os envios do último comunicado que possuem uma pergunta do morador
            $quantidadeDuvidas = Envio::where('fk_id_comunicados', $ultimoComunicado->pk_id_comunicados)
                ->whereNotNull('resposta')
                ->count();
        }

        //Montando o array com os dados que serão enviados ao Gemini
        $dados = [
            'reclamacoes' => $reclamacoes,
            'reservas' => $reservas,
            'quantidade_duvidas_ultimo_comunicado' => $quantidadeDuvidas,
        ];

        //Convertendo os dados para JSON, que é o formato esperado pelo service
        $dadosJson = json_encode($dados, JSON_UNESCAPED_UNICODE);

        //Cria o service, envia os dados e retorna a resposta gerada pela IA
        return (new ServiceGemini())->generate($dadosJson);
    }
}
