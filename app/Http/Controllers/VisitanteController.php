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

    // Returns all visitors
    public function index(Request $request)
    {
        $query = Visitante::query();

        if ($request->filled('nome')) {
            $query->where(
                'nome_visitante',
                'LIKE',
                '%' . $request->input('nome') . '%'
            );
        }

        if ($request->filled('cpf')) {
            $query->where(
                'cpf_visitante',
                'LIKE',
                '%' . $request->input('cpf') . '%'
            );
        }

        $visitantes = $query->get();

        return $this->responseJson(
            'Visitantes recuperados com sucesso!',
            200,
            [
                'visitantes' => VisitanteResources::collection($visitantes)
            ]
        );
    }

    // Allows the doorman to register a visitor
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                'Os dados informados são inválidos.',
                422,
                $validator->errors()->toArray()
            );
        }

        $dadosMapeados = [
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
        ];

        $novoVisitante = Visitante::create($dadosMapeados);

        return $this->responseJson(
            'Visitante criado com sucesso!',
            201,
            [
                'visitante' => new VisitanteResources($novoVisitante)
            ]
        );
    }

    // Returns visitors linked to the authenticated resident through visits
    public function indexMorador(Request $request)
    {
        $morador = $request->user();

        $query = Visitante::whereHas('visitas', function ($query) use ($morador) {
            $query->where('fk_morador', $morador->pk_id_morador);
        });

        if ($request->filled('nome')) {
            $query->where(
                'nome_visitante',
                'LIKE',
                '%' . $request->input('nome') . '%'
            );
        }

        if ($request->filled('cpf')) {
            $query->where(
                'cpf_visitante',
                'LIKE',
                '%' . $request->input('cpf') . '%'
            );
        }

        $visitantes = $query->get();

        return $this->responseJson(
            'Visitantes recuperados com sucesso!',
            200,
            [
                'visitantes' => VisitanteResources::collection($visitantes)
            ]
        );
    }

    // Allows the resident to register a visitor
    public function storeMorador(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                'Os dados informados são inválidos.',
                422,
                $validator->errors()->toArray()
            );
        }

        $dadosMapeados = [
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
        ];

        $novoVisitante = Visitante::create($dadosMapeados);

        return $this->responseJson(
            'Visitante cadastrado com sucesso!',
            201,
            [
                'visitante' => new VisitanteResources($novoVisitante)
            ]
        );
    }

    // Returns a specific visitor
    public function show(string $id)
    {
        $visitante = Visitante::findOrFail($id);

        return $this->responseJson(
            'Visitante recuperado com sucesso!',
            200,
            [
                'visitante' => new VisitanteResources($visitante)
            ]
        );
    }

    // Updates visitor information
    public function update(Request $request, string $id)
    {
        $visitante = Visitante::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante,' . $id . ',pk_id_visitante',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                'Os dados informados são inválidos.',
                422,
                $validator->errors()->toArray()
            );
        }

        $dadosMapeados = [
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
        ];

        $atualizado = $visitante->update($dadosMapeados);

        if (!$atualizado) {
            return $this->errorJson(
                'Não foi possível atualizar o visitante.',
                400
            );
        }

        return $this->responseJson(
            'Visitante atualizado com sucesso!',
            200,
            [
                'visitante' => new VisitanteResources($visitante->refresh())
            ]
        );
    }

    // Deletes a visitor
    public function destroy(string $id)
    {
        $visitante = Visitante::findOrFail($id);

        // Prevents deletion when the visitor has registered visits
        if ($visitante->visitas()->exists()) {
            return $this->errorJson(
                'Não é possível excluir um visitante que possui visitas registradas.',
                409
            );
        }

        $deletado = $visitante->delete();

        if (!$deletado) {
            return $this->errorJson(
                'Não foi possível excluir o visitante.',
                400
            );
        }

        return $this->responseJson(
            'Visitante excluído com sucesso!',
            200
        );
    }
}
