<?php

namespace App\Http\Controllers;

use App\Http\Resources\EspacoResource;
use App\Models\Espaco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EspacoController extends Controller
{
    // Retorna todos os espaços
    public function index()
    {
        $espacos = Espaco::all();

        return EspacoResource::collection($espacos);
    }

    // Retorna um espaço específico
    public function show($id)
    {
        $espaco = Espaco::find($id);

        if (!$espaco) {
            return response()->json([
                'message' => 'Espaço não encontrado.'
            ], 404);
        }

        return new EspacoResource($espaco);
    }

    // Cadastra um novo espaço
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descricao_espaco' => 'required|string',
            'nome_espaco' => 'required|string|max:255',
            'autorizacao' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors()
            ], 422);
        }

        $espaco = Espaco::create([
            'descricao_espaco' => $request->descricao_espaco,
            'nome_espaco' => $request->nome_espaco,
            'autorizacao' => $request->autorizacao
        ]);

        return new EspacoResource($espaco);
    }

    // Atualiza um espaço
    public function update(Request $request, $id)
    {
        $espaco = Espaco::find($id);

        if (!$espaco) {
            return response()->json([
                'message' => 'Espaço não encontrado.'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'descricao_espaco' => 'sometimes|string',
            'nome_espaco' => 'sometimes|string|max:255',
            'autorizacao' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors()
            ], 422);
        }

        $espaco->update($request->only([
            'descricao_espaco',
            'nome_espaco',
            'autorizacao'
        ]));

        return new EspacoResource($espaco);
    }

    // Exclui um espaço
    public function destroy($id)
    {
        $espaco = Espaco::find($id);

        if (!$espaco) {
            return response()->json([
                'message' => 'Espaço não encontrado.'
            ], 404);
        }

        $espaco->delete();

        return response()->json([
            'message' => 'Espaço excluído com sucesso.'
        ]);
    }
}