<?php

namespace App\Http\Controllers;

use App\Http\Resources\EspacoResource;
use App\Models\Espaco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\HttpResposta;

class EspacoController extends Controller
{
    use HttpResposta;

    // Retorna todos os espaços
    public function index()
    {
        $espacos = Espaco::all();

        return $this->responseJson(
            'Espaços encontrados com sucesso.',
            200,
            EspacoResource::collection($espacos)->resolve()
        );
    }

    // Retorna um espaço específico
    public function show($id)
    {
        $espaco = Espaco::find($id);

        if (!$espaco) {
            return $this->errorJson(
                'Espaço não encontrado.',
                404
            );
        }

        return $this->responseJson(
            'Espaço encontrado com sucesso.',
            200,
            (new EspacoResource($espaco))->resolve()
        );
    }

    // Cadastra um novo espaço
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'descricao' => 'required|string',
            'nome' => 'required|string|max:255',
            'disponivel' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                'Erro de validação.',
                422,
                $validator->errors()->toArray()
            );
        }

        $dadosMapeados = [
            'descricao_espaco' => $request->input('descricao'),
            'nome_espaco' => $request->input('nome'),
            'autorizacao' => $request->input('disponivel'),
        ];

        $espaco = Espaco::create($dadosMapeados);

        return $this->responseJson(
            'Espaço cadastrado com sucesso.',
            201,
            (new EspacoResource($espaco))->resolve()
        );
    }

    // Atualiza um espaço
    public function update(Request $request, $id)
    {
        $espaco = Espaco::find($id);

        if (!$espaco) {
            return $this->errorJson(
                'Espaço não encontrado.',
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'descricao' => 'sometimes|string',
            'nome' => 'sometimes|string|max:255',
            'disponivel' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                'Erro de validação.',
                422,
                $validator->errors()->toArray()
            );
        }

        $dadosMapeados = [];

        if ($request->has('descricao')) {
            $dadosMapeados['descricao_espaco'] = $request->input('descricao');
        }

        if ($request->has('nome')) {
            $dadosMapeados['nome_espaco'] = $request->input('nome');
        }

        if ($request->has('disponivel')) {
            $dadosMapeados['autorizacao'] = $request->input('disponivel');
        }

        $espaco->update($dadosMapeados);

        return $this->responseJson(
            'Espaço atualizado com sucesso.',
            200,
            (new EspacoResource($espaco))->resolve()
        );
    }

    // Exclui um espaço
    public function destroy($id)
    {
        $espaco = Espaco::find($id);

        if (!$espaco) {
            return $this->errorJson(
                'Espaço não encontrado.',
                404
            );
        }

        $espaco->delete();

        return $this->responseJson(
            'Espaço excluído com sucesso.',
            200
        );
    }
}