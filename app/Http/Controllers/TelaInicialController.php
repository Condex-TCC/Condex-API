<?php

namespace App\Http\Controllers;

use App\Http\Resources\ComunicadoResources;
use Illuminate\Http\Request;
use App\HttpResposta;
use App\GeraResumoGemini;
use App\Models\Comunicado;
use App\Models\Envio;
use App\Models\Morador;
use App\Models\Porteiro;
use App\Models\Reclamacoes;
use App\Models\Reserva;

class TelaInicialController extends Controller
{
    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    //Adiciona a trait que gera o resumo do condomínio com o Gemini
    use GeraResumoGemini;

    //Função que exibe os dados na tala inicial na tala do sindico
    public function exibeTelaInicialSinicio(){

        //Váriavel que vão armazenar as informações vão ser enviadas no json final
        $quantReclamacoesNaoRespondidas = 0;
        $quantComunicadosNaoRespondios = 0;
        $quantReservasPendentes = 0;
        $top3UltimosComunicados = [];
        $statusCondominio = [];
        $resumoCondominio = "";

        //Pegando os registros de quantidade
        $quantReclamacoesNaoRespondidas = Reclamacoes::where("resposta_reclamacao", null)->count();
        $quantComunicadosNaoRespondios = Envio::where("resposta", "!=", null)
            ->where("contra_resposta", null)->count();
        $quantReservasPendentes = Reserva::where("status_reserva", "pendente")->count();

        //Pegando os dados do dos ultimos 3 comunicados
        $top3UltimosComunicados = Comunicado::orderBy('created_at', 'desc')->limit(3)->get();

        //Pegando os dados do status do condominio
        $morador = Morador::count();
        $porteiro =  Porteiro::count();

        $statusCondominio = [
            "morador" => $morador, //Pegando a quantidade de moradors
            "porteiro" => $porteiro, //Pegando o total
            "total" => $morador + $porteiro, //Realiza a soma total
        ];

        //Gerando o resumo do condomínio com o Gemini
        $resumoCondominio = $this->gerarResumoCondominio();


        //Retorna os dados com formatação padrão 
        return $this->responseJson(
            "Os dados do condominio foram obtidos com sucesso!",
            200,
            [
                "quant_reclamacoes" => $quantReclamacoesNaoRespondidas,
                "quant_comunicados" => $quantComunicadosNaoRespondios,
                "quant_reservas" => $quantReservasPendentes,
                "status_condominio" => $statusCondominio,
                "top_comunicados" => ComunicadoResources::collection($top3UltimosComunicados),
                "resumo_condominio" => $resumoCondominio
            ]
        );
    }
}
