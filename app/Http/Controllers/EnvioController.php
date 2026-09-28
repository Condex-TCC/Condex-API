<?php

namespace App\Http\Controllers;

use App\Http\Resources\EnvioResources;
use App\Models\Comunicado;
use App\Models\Envio;
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
        $envios = Envio::with([
            'comunicado',
            'morador'
        ])->get();

        $jsonTratado = EnvioResources::collection($envios);

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
        $validator = Validator::make(
            $request->all(),
            [
                'descricao' => 'required|string|max:255',
            ]
        );

        if ($validator->fails()) {

            return $this->errorJson(
                "Os dados passados não estão corretos!",
                400,
                [
                    $validator->errors()
                ]
            );
        }

        $envio = Envio::find($id);

        if (!$envio) {

            return $this->errorJson(
                "Envio não encontrado!",
                404
            );
        }

        if ($envio->resposta == null) {

            return $this->errorJson(
                "Esse comunicado ainda não possui uma resposta!",
                400
            );
        }

        if ($envio->contra_resposta != null) {

            return $this->errorJson(
                "Essa resposta já possui uma contra-resposta!",
                400
            );
        }

        $envio->update([
            'contra_resposta' => $request->input('descricao')
        ]);

        $envio->load([
            'comunicado',
            'morador'
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
        $morador = $request->user();

        $validator = Validator::make(
            $request->all(),
            [
                'descricao' => 'required|string|max:255',
            ]
        );

        if ($validator->fails()) {

            return $this->errorJson(
                "Os dados passados não estão corretos!",
                400,
                [
                    $validator->errors()
                ]
            );
        }

        $comunicado = Comunicado::find($id);

        if (!$comunicado) {

            return $this->errorJson(
                "Comunicado não encontrado!",
                404
            );
        }

        $envio = Envio::where(
            'fk_id_comunicados',
            $id
        )
        ->where(
            'fk_id_morador',
            $morador->pk_id_morador
        )
        ->first();

        if (!$envio) {

            return $this->errorJson(
                "Esse comunicado não foi enviado para você!",
                404
            );
        }

        if ($envio->resposta != null) {

            return $this->errorJson(
                "Você já respondeu esse comunicado!",
                400
            );
        }

        $envio->update([
            'resposta' => $request->input('descricao')
        ]);

        $envio->load([
            'comunicado',
            'morador'
        ]);

        return $this->responseJson(
            "Resposta cadastrada com sucesso!",
            201,
            [
                new EnvioResources($envio)
            ]
        );
    }


    // =========================================================
    // MORADOR VISUALIZA SEU HISTÓRICO DE RESPOSTAS
    // =========================================================

    public function indexRespostasMorador(Request $request)
    {
        $morador = $request->user();

        $envios = Envio::with([
            'comunicado'
        ])
            ->where(
                'fk_id_morador',
                $morador->pk_id_morador
            )
            ->whereNotNull('resposta')
            ->get();

        $jsonTratado = EnvioResources::collection($envios);

        return $this->responseJson(
            "Histórico de respostas recuperado com sucesso!",
            200,
            [
                $jsonTratado
            ]
        );
    }
}