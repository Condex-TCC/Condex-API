<?php

namespace App\Http\Controllers;

use App\Http\Resources\ComunicadoResources;
use Illuminate\Http\Request;
use App\HttpResposta;
use App\Models\Comunicado;
use App\Models\Envio;
use App\Models\Morador;
use Illuminate\Support\Facades\Validator;

class ComunicadoController extends Controller
{
    use HttpResposta;

    // =========================================================
    // MORADOR VISUALIZA OS COMUNICADOS ENVIADOS PARA ELE
    // =========================================================

    public function index(Request $request)
    {
        // Recuperando o morador logado
        $morador = $request->user();

        // Recuperando somente os comunicados enviados para o morador
        $comunicados = Comunicado::whereHas('envios', function ($query) use ($morador) {
            $query->where(
                'fk_id_morador',
                $morador->pk_id_morador
            );
        })->get();

        // Tratando os dados com Resource
        $jsonTratado = ComunicadoResources::collection($comunicados);

        return $this->responseJson(
            "Comunicados recuperados com sucesso!",
            200,
            [
                $jsonTratado
            ]
        );
    }

    // =========================================================
    // MORADOR VISUALIZA UM COMUNICADO ESPECÍFICO
    // =========================================================

    public function show(Request $request, string $id)
    {
        // Recuperando o morador logado
        $morador = $request->user();

        // Recuperando o comunicado somente se ele foi enviado
        // para o morador logado
        $comunicado = Comunicado::where(
            'pk_id_comunicados',
            $id
        )
        ->whereHas('envios', function ($query) use ($morador) {
            $query->where(
                'fk_id_morador',
                $morador->pk_id_morador
            );
        })
        ->first();

        // Caso o comunicado não exista ou não tenha sido enviado
        // para o morador
        if (!$comunicado) {
            return $this->errorJson(
                "Comunicado não encontrado!",
                404
            );
        }

        return $this->responseJson(
            "Comunicado recuperado com sucesso!",
            200,
            [
                new ComunicadoResources($comunicado)
            ]
        );
    }

    // =========================================================
    // SÍNDICO CADASTRA E ENVIA UM COMUNICADO
    // =========================================================

    public function store(Request $request)
    {
        $sindico = $request->user();

        $idSindico = $sindico->pk_id_sindico;

        $validator = Validator::make($request->all(), [
            "descricao" => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                "Os dados passados não estão corretos!",
                400,
                [
                    $validator->errors()
                ]
            );
        }

        $dadosMapeados = [
            "descricao_comunicado" => $request->input("descricao"),
            "fk_id_sindico_comunicados" => $idSindico,
        ];

        $novoComunicado = Comunicado::create($dadosMapeados);

        $moradores = Morador::all();

        foreach ($moradores as $morador) {
            Envio::create([
                "fk_id_comunicados" => $novoComunicado->pk_id_comunicados,
                "fk_id_morador" => $morador->pk_id_morador,
                "fk_id_resposta" => null,
                "fk_id_contra_resposta" => null,
            ]);
        }

        return $this->responseJson(
            "Comunicado criado e enviado aos moradores com sucesso!",
            201,
            [
                new ComunicadoResources($novoComunicado)
            ]
        );
    }
}