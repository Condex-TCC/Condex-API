<?php

namespace App\Http\Controllers;

use App\Http\Resources\AutorizacaoResources;
use App\Models\AutorizacaoVisitante;
use Illuminate\Http\Request;
use App\HttpResposta;
use Illuminate\Support\Facades\Validator;

class AutorizacaoVisitanteController extends Controller
{
    // Adiciona a trait com as respostas padrões para APIs
    use HttpResposta;


    
    // MORADOR AUTORIZA UM VISITANTE
    public function authorizeVisitor(Request $request)
    {
        // Pegando o morador que está logado
        $morador = $request->user();

        // Validando os dados enviados
        $validator = Validator::make($request->all(), [
            "fk_id_visitante" => "required|exists:visitantes,pk_id_visitante"
        ]);

        // Caso a validação falhe
        if ($validator->fails()) {

            return $this->errorJson(
                "Os dados passados não estão corretos!",
                400,
                [
                    $validator->errors()
                ]
            );
        }

        // Verificando se esse visitante já possui uma autorização ativa
        $autorizacaoExistente = AutorizacaoVisitante::where(
            "fk_id_visitante",
            $request->input("fk_id_visitante")
        )
        ->whereIn("status", [
            "autorizado",
            "entrada_realizada"
        ])
        ->first();

        // Se já existir uma autorização ativa
        if ($autorizacaoExistente) {

            return $this->errorJson(
                "Esse visitante já possui uma autorização ativa!",
                400
            );
        }

        // Criando a autorização
        $autorizacao = AutorizacaoVisitante::create([
            "fk_id_visitante" => $request->input("fk_id_visitante"),
            "fk_id_morador" => $morador->pk_id_morador,
            "status" => "autorizado",
            "data_autorizacao" => now()
        ]);

        // Retornando a autorização criada
        return $this->responseJson(
            "Visitante autorizado com sucesso!",
            201,
            [
                new AutorizacaoResources($autorizacao)
            ]
        );
    }


    
    // PORTEIRO CONSULTA VISITANTES AUTORIZADOS
    public function getAuthorizedVisitors()
    {
        // Pegando somente autorizações que ainda permitem entrada
        $autorizacoes = AutorizacaoVisitante::with([
        "visitante",
        "morador"
    ])
        ->where("status", "autorizado")
        ->get();

        // Tratando os dados com Resource
        $jsonTratado = AutorizacaoResources::collection($autorizacoes);

        // Retornando os visitantes autorizados
        return $this->responseJson(
            "Visitantes autorizados recuperados com sucesso!",
            200,
            [
                $jsonTratado
            ]
        );
    }


    
    // PORTEIRO LIBERA A ENTRADA
    public function allowEntry(string $id)
    {
        // Procurando a autorização
        $autorizacao = AutorizacaoVisitante::find($id);

        // Caso não encontre
        if (!$autorizacao) {

            return $this->errorJson(
                "Autorização não encontrada!",
                404
            );
        }

        // Verificando se o visitante realmente está autorizado
        if ($autorizacao->status !== "autorizado") {

            return $this->errorJson(
                "Esse visitante não possui uma autorização válida para entrada!",
                400
            );
        }

        // Registrando a entrada
        $autorizacao->update([
            "status" => "entrada_realizada",
            "entrada_em" => now()
        ]);

        // Retornando os dados atualizados
        return $this->responseJson(
            "Entrada do visitante liberada com sucesso!",
            200,
            [
                new AutorizacaoResources($autorizacao)
            ]
        );
    }


    
    // PORTEIRO REGISTRA A SAÍDA
    public function registerExit(string $id)
    {
        // Procurando a autorização
        $autorizacao = AutorizacaoVisitante::find($id);

        // Caso não encontre
        if (!$autorizacao) {

            return $this->errorJson(
                "Autorização não encontrada!",
                404
            );
        }

        // Verificando se o visitante realmente entrou
        if ($autorizacao->status !== "entrada_realizada") {

            return $this->errorJson(
                "Esse visitante não possui uma entrada registrada!",
                400
            );
        }

        // Registrando a saída
        $autorizacao->update([
            "status" => "saida_realizada",
            "saida_em" => now()
        ]);

        // Retornando os dados atualizados
        return $this->responseJson(
            "Saída do visitante registrada com sucesso!",
            200,
            [
                new AutorizacaoResources($autorizacao)
            ]
        );
    }
}
