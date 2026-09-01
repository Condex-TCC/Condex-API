<?php

namespace App\Http\Controllers;

use App\Http\Resources\ComunicadoResources;
use Illuminate\Http\Request;
use App\HttpResposta;
use App\Models\Comunicado;
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

        // Mapeando os dados recebidos
        $dadosMapeados = [
            "descricao_comunicado" => $request->input("descricao_comunicado"),
            "fk_id_sindico_comunicados" => $idSindico,
        ];

        // Validando os dados
        $validator = Validator::make($dadosMapeados, [
            "descricao_comunicado" => 'required|string|max:255',
            "fk_id_sindico_comunicados" => 'required|numeric',
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

        // Criando o comunicado
        $novoComunicado = Comunicado::create($dadosMapeados);

        // Retornando o comunicado criado
        return $this->responseJson(
            "Comunicado criado com sucesso!",
            200,
            [
                new ComunicadoResources($novoComunicado)
            ]
        );
    }
}