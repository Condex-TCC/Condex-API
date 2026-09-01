<?php

namespace App\Http\Controllers;

use App\Http\Resources\EnvioResources;
use App\Models\Comunicado;
use App\Models\ContraResposta;
use App\Models\Envio;
use App\Models\Resposta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\HttpResposta;

class EnvioController extends Controller
{
    use HttpResposta;


    // =========================================================
    // SÍNDICO VISUALIZA AS RESPOSTAS DOS MORADORES
    // =========================================================

    public function index()
    {
        // Recuperando os envios com suas relações
        $envios = Envio::with([
            'comunicado',
            'resposta',
            'contraResposta'
        ])->get();

        // Tratando os dados com Resource
        $jsonTratado = EnvioResources::collection($envios);

        // Retornando os dados
        return $this->responseJson(
            "Respostas recuperadas com sucesso!",
            200,
            [
                $jsonTratado
            ]
        );
    }


    // =========================================================
    // SÍNDICO CADASTRA UMA CONTRA-RESPOSTA
    // =========================================================

    public function store(Request $request, string $id)
    {
        // Validando os dados enviados
        $validator = Validator::make(
            $request->all(),
            [
                'descricao_contra_resposta' => 'required|string|max:255',
            ]
        );

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

        // Recuperando o envio
        $envio = Envio::findOrFail($id);

        // Verificando se já existe uma contra-resposta
        if ($envio->fk_id_contra_resposta != null) {

            return $this->errorJson(
                "Essa resposta já possui uma contra-resposta!",
                400
            );
        }

        // Criando a contra-resposta
        $contraResposta = ContraResposta::create([
            'descricao_contra_resposta' => $request->input('descricao_contra_resposta'),
        ]);

        // Atualizando o envio com a contra-resposta
        $envio->update([
            'fk_id_contra_resposta' =>
                $contraResposta->pk_id_contra_resposta
        ]);

        // Retornando o envio atualizado
        $envio->load([
            'comunicado',
            'resposta',
            'contraResposta'
        ]);

        return $this->responseJson(
            "Contra-resposta cadastrada com sucesso!",
            200,
            [
                new EnvioResources($envio)
            ]
        );
    }


    // =========================================================
// MORADOR RESPONDE UM COMUNICADO
// =========================================================

public function respond(Request $request, string $id)
{
    // Recuperando o morador logado
    $morador = $request->user();

    // Validando a resposta
    $validator = Validator::make(
        $request->all(),
        [
            'descricao_resposta' => 'required|string|max:255',
        ]
    );

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

    // Verificando se o comunicado existe
    $comunicado = Comunicado::find($id);

    if (!$comunicado) {

        return $this->errorJson(
            "Comunicado não encontrado!",
            404
        );
    }

    // Verificando se o morador já respondeu esse comunicado
    $jaRespondeu = Resposta::where(
        'fk_id_morador',
        $morador->pk_id_morador
    )
    ->whereHas('envios', function ($query) use ($id) {

        $query->where(
            'fk_id_comunicados',
            $id
        );

    })
    ->exists();

    if ($jaRespondeu) {

        return $this->errorJson(
            "Você já respondeu esse comunicado!",
            400
        );
    }

    // Criando a resposta
    $resposta = Resposta::create([
        'fk_id_morador' => $morador->pk_id_morador,
        'descricao_resposta' => $request->input('descricao_resposta'),
    ]);

    // Criando o envio da resposta para o comunicado
    $envio = Envio::create([
        'fk_id_comunicados' => $id,
        'fk_id_resposta' => $resposta->pk_id_resposta,
        'fk_id_contra_resposta' => null,
    ]);

    // Carregando os relacionamentos
    $envio->load([
        'comunicado',
        'resposta.morador',
        'contraResposta'
    ]);

    // Retornando os dados
    return $this->responseJson(
        "Resposta cadastrada com sucesso!",
        201,
        [
            new EnvioResources($envio)
        ]
    );
}
}