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
    // Adiciona a trait com as respostas padrões para APIs
    use HttpResposta;


    // =========================================================
    // VISUALIZAR TODOS OS COMUNICADOS
    // =========================================================

    public function index()
    {
        // Pegando todos os comunicados do banco de dados
        $comunicados = Comunicado::all();

        // Aplicando o Resource para tratar o JSON
        $jsonTratado = ComunicadoResources::collection($comunicados);

        // Retornando os comunicados
        return $this->responseJson(
            "Comunicados recuperados com sucesso!",
            200,
            [
                $jsonTratado
            ]
        );
    }


    // =========================================================
    // VISUALIZAR UM COMUNICADO ESPECÍFICO
    // =========================================================

    public function show(string $id)
    {
        // Recuperando o comunicado pelo ID
        $comunicado = Comunicado::findOrFail($id);

        // Retornando o comunicado
        return $this->responseJson(
            "Comunicado recuperado com sucesso!",
            200,
            [
                new ComunicadoResources($comunicado)
            ]
        );
    }


    // =========================================================
// CADASTRAR UM COMUNICADO
// =========================================================

public function store(Request $request)
{
    // Pegando o síndico logado através do Sanctum
    $sindico = $request->user();

    // Pegando o ID do síndico
    $idSindico = $sindico->pk_id_sindico;

    // Validando os dados recebidos pela API
    $validator = Validator::make($request->all(), [
        "descricao" => 'required|string|max:255',
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

    // Mapeando os dados recebidos para os campos do banco
    $dadosMapeados = [
        "descricao_comunicado" => $request->input("descricao"),
        "fk_id_sindico_comunicados" => $idSindico,
    ];

    // Criando o comunicado
    $novoComunicado = Comunicado::create($dadosMapeados);

    // Buscando todos os moradores
    $moradores = Morador::all();

    // Criando um envio para cada morador
    foreach ($moradores as $morador) {

        Envio::create([
            "fk_id_comunicados" => $novoComunicado->pk_id_comunicados,
            "fk_id_morador" => $morador->pk_id_morador,
            "fk_id_resposta" => null,
            "fk_id_contra_resposta" => null,
        ]);
    }

    // Retornando o comunicado criado
    return $this->responseJson(
        "Comunicado criado e enviado aos moradores com sucesso!",
        201,
        [
            new ComunicadoResources($novoComunicado)
        ]
    );
}
}