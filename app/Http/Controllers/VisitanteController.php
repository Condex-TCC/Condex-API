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

    // Retorna todos os visitantes
    public function index()
    {
        $visitantes = Visitante::all();

        return $this->responseJson(
            "Visitantes recuperados com sucesso!",
            200,
            [
                VisitanteResources::collection($visitantes)
            ]
        );
    }

    // Porteiro cadastra um visitante
    public function store(Request $request)
    {
        $porteiro = $request->user();

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante',
            'morador' => 'required|exists:moradors,pk_id_morador',
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
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
            'fk_morador' => $request->input('morador'),
            'fk_funcionario' => $porteiro->pk_id_porteiro,
        ];

        $novoVisitante = Visitante::create($dadosMapeados);

        return $this->responseJson(
            "Visitante criado com sucesso!",
            201,
            [
                new VisitanteResources($novoVisitante)
            ]
        );
    }

    // Retorna os visitantes cadastrados pelo morador logado
public function indexMorador(Request $request)
{
    // Pegando o morador logado através do token Sanctum
    $morador = $request->user();

    // Buscando somente os visitantes desse morador
    $visitantes = Visitante::where(
        'fk_morador',
        $morador->pk_id_morador
    )->get();

    // Retornando os visitantes encontrados
    return $this->responseJson(
        "Visitantes do morador recuperados com sucesso!",
        200,
        [$visitantes]
    );
}

    // Morador cadastra um visitante
    public function storeMorador(Request $request)
    {
        $morador = $request->user();

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante',
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
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
            'fk_morador' => $morador->pk_id_morador,
            'fk_funcionario' => null,
        ];

        $novoVisitante = Visitante::create($dadosMapeados);

        return $this->responseJson(
            "Visitante criado com sucesso!",
            201,
            [
                new VisitanteResources($novoVisitante)
            ]
        );
    }

    // Retorna um visitante específico
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

    // Atualiza um visitante
    public function update(Request $request, string $id)
    {
        $visitante = Visitante::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante,' . $id . ',pk_id_visitante',
            'morador' => 'required|exists:moradors,pk_id_morador',
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
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
            'fk_morador' => $request->input('morador'),
        ];

        $atualizado = $visitante->update($dadosMapeados);

        if (!$atualizado) {
            return $this->errorJson(
                "Não foi possível atualizar o visitante.",
                400
            );
        }

        $visitanteAtualizado = Visitante::findOrFail($id);

        return $this->responseJson(
            "Visitante atualizado com sucesso!",
            200,
            [
                new VisitanteResources($visitanteAtualizado)
            ]
        );
    }

    // Remove um visitante
    public function destroy(string $id)
    {
        $visitante = Visitante::findOrFail($id);

        $deletado = $visitante->delete();

        if (!$deletado) {
            return $this->errorJson(
                "Não foi possível deletar o visitante.",
                400
            );
        }

        return $this->responseJson(
            "Visitante deletado com sucesso!",
            200
        );
    }
}