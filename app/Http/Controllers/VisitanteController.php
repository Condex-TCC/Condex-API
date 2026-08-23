<?php

namespace App\Http\Controllers;

use App\Http\Resources\VisitanteResources;
use App\Models\Visitante;
use Illuminate\Http\Request;
use App\HttpResposta;
use Illuminate\Support\Facades\Validator;

class VisitanteController extends Controller
{
    use HttpResposta;

    // Get all visitors
    public function index()
    {
        $visitantes = Visitante::all();

        return $this->responseJson(
            "Visitors retrieved successfully!",
            200,
            [
                VisitanteResources::collection($visitantes)
            ]
        );
    }

    // Create a visitor
    public function store(Request $request)
    {
        // Pegando o porteiro logado através do token Sanctum
        $porteiro = $request->user();

        // Pegando o ID do porteiro
        $idPorteiro = $porteiro->pk_id_porteiro;

        // Validando os dados enviados
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante',
            'fk_morador' => 'required|integer',
        ]);

        // Caso os dados não passem na validação
        if ($validator->fails()) {
            return $this->errorJson(
                "The provided data is invalid!",
                400,
                [
                    $validator->errors()
                ]
            );
        }

        // Mapeando os dados para os campos do banco
        $dadosMapeados = [
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
            'fk_morador' => $request->input('fk_morador'),
            'fk_funcionario' => $idPorteiro,
        ];

        // Criando o visitante
        $novoVisitante = Visitante::create($dadosMapeados);

        // Retornando o visitante criado
        return $this->responseJson(
            "Visitante criado com sucesso!",
            201,
            [
                new VisitanteResources($novoVisitante)
            ]
        );
    }

    // Get a specific visitor
    public function show(string $id)
    {
        $visitante = Visitante::findOrFail($id);

        return $this->responseJson(
            "Visitante recuperado com sucesso!",
            200,
            [
                new VisitanteResources($visitante)
            ]
        );
    }

    // Update a visitor
    public function update(Request $request, string $id)
    {
        // Pegando o visitante
        $visitante = Visitante::findOrFail($id);

        // Validando apenas os dados que podem ser alterados
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante,' . $id . ',pk_id_visitante',
            'fk_morador' => 'required|integer',
        ]);

        // Caso os dados não passem na validação
        if ($validator->fails()) {
            return $this->errorJson(
                "The provided data is invalid!",
                400,
                [
                    $validator->errors()
                ]
            );
        }

        // Mapeando apenas os dados que podem ser atualizados
        $dadosMapeados = [
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
            'fk_morador' => $request->input('fk_morador'),
        ];

        // Atualizando o visitante
        $atualizado = $visitante->update($dadosMapeados);

        // Caso não seja possível atualizar
        if (!$atualizado) {
            return $this->errorJson(
                "Could not update the visitor.",
                400
            );
        }

        // Pegando o visitante atualizado
        $visitanteAtualizado = Visitante::findOrFail($id);

        return $this->responseJson(
            "Visitante atualizado com sucesso!",
            200,
            [
                new VisitanteResources($visitanteAtualizado)
            ]
        );
    }

    // Delete a visitor
    public function destroy(string $id)
    {
        $visitante = Visitante::findOrFail($id);

        $deletado = $visitante->delete();

        if (!$deletado) {
            return $this->errorJson(
                "Could not delete the visitor.",
                400
            );
        }

        return $this->responseJson(
            "Visitante deletado com sucesso!",
            200
        );
    }
}