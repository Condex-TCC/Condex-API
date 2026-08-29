<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReacaoResources;
use App\Models\Comunicado;
use App\Models\Reacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\HttpResposta;

class ReacaoController extends Controller
{
    use HttpResposta;


    // =========================================================
    // MORADOR REAGE A UM COMUNICADO
    // =========================================================

    public function store(Request $request, string $id)
    {
        // Recuperando o morador logado
        $morador = $request->user();

        // Validando a reação
        $validator = Validator::make(
            $request->all(),
            [
                'tipo_reacao' => 'required|string|in:curtir,amei,triste,importante',
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

        // Verificando se o morador já reagiu
        $reacaoExistente = Reacao::where(
            'fk_id_morador',
            $morador->pk_id_morador
        )
        ->where(
            'fk_id_comunicado',
            $id
        )
        ->first();

        if ($reacaoExistente) {

            return $this->errorJson(
                "Você já reagiu a esse comunicado!",
                400
            );
        }

        // Criando a reação
        $reacao = Reacao::create([
            'fk_id_morador' => $morador->pk_id_morador,
            'fk_id_comunicado' => $id,
            'tipo_reacao' => $request->input('tipo_reacao'),
        ]);

        // Carregando os relacionamentos
        $reacao->load([
            'morador',
            'comunicado'
        ]);

        // Retornando a reação criada
        return $this->responseJson(
            "Reação cadastrada com sucesso!",
            201,
            [
                new ReacaoResources($reacao)
            ]
        );
    }


    // =========================================================
    // MORADOR VISUALIZA SUA REAÇÃO
    // =========================================================

    public function show(Request $request, string $id)
    {
        // Recuperando o morador logado
        $morador = $request->user();

        // Procurando a reação do morador naquele comunicado
        $reacao = Reacao::with([
            'morador',
            'comunicado'
        ])
        ->where(
            'fk_id_morador',
            $morador->pk_id_morador
        )
        ->where(
            'fk_id_comunicado',
            $id
        )
        ->first();

        if (!$reacao) {

            return $this->errorJson(
                "Você ainda não reagiu a esse comunicado!",
                404
            );
        }

        return $this->responseJson(
            "Reação recuperada com sucesso!",
            200,
            [
                new ReacaoResources($reacao)
            ]
        );
    }
}