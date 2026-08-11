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
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante',
            'fk_morador' => 'required|integer',
            'fk_funcionario' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                "The provided data is invalid!",
                400,
                [
                    $validator->errors()
                ]
            );
        }

        $dadosMapeados = [
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
            'fk_morador' => $request->input('fk_morador'),
            'fk_funcionario' => $request->input('fk_funcionario'),
        ];

        $novoVisitante = Visitante::create($dadosMapeados);

        return $this->responseJson(
            "Visitor created successfully!",
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
            "Visitor retrieved successfully!",
            200,
            [
                new VisitanteResources($visitante)
            ]
        );
    }

    // Update a visitor
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante,' . $id . ',pk_id_visitante',
            'fk_morador' => 'required|integer',
            'fk_funcionario' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                "The provided data is invalid!",
                400,
                [
                    $validator->errors()
                ]
            );
        }

        $visitante = Visitante::findOrFail($id);

        $dadosMapeados = [
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
            'fk_morador' => $request->input('fk_morador'),
            'fk_funcionario' => $request->input('fk_funcionario'),
        ];

        $atualizado = $visitante->update($dadosMapeados);

        if (!$atualizado) {
            return $this->errorJson(
                "Could not update the visitor.",
                400
            );
        }

        $visitanteAtualizado = Visitante::findOrFail($id);

        return $this->responseJson(
            "Visitor updated successfully!",
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
            "Visitor deleted successfully!",
            200
        );
    }
}