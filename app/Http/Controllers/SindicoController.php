<?php

namespace App\Http\Controllers;

use App\Models\Sindico;
use App\Http\Resources\SindicoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\HttpResposta;

class SindicoController extends Controller
{
    use HttpResposta;

    // Recupera todos os síndicos
    public function index()
    {
        $sindicos = Sindico::all();

        return $this->responseJson(
            'Síndicos encontrados com sucesso.',
            200,
            SindicoResource::collection($sindicos)->resolve()
        );
    }

    // Recupera um síndico específico
    public function show($id)
    {
        $sindico = Sindico::find($id);

        if (!$sindico) {
            return $this->errorJson(
                'Síndico não encontrado.',
                404
            );
        }

        return $this->responseJson(
            'Síndico encontrado com sucesso.',
            200,
            (new SindicoResource($sindico))->resolve()
        );
    }

    // Cria um novo síndico
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nome" => "required|string|max:150",
            "telefone" => "required|string|max:20",
            "email" => "required|email|max:200|unique:sindicos,email_sindico",
            "senha" => "required|string|min:6"
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                'Erro de validação.',
                422,
                $validator->errors()->toArray()
            );
        }

        $sindico = Sindico::create([
            "nome_sindico" => $request->input("nome"),
            "telefone_sindico" => $request->input("telefone"),
            "email_sindico" => $request->input("email"),
            "senha_sindico" => Hash::make($request->input("senha"))
        ]);

        return $this->responseJson(
            'Síndico cadastrado com sucesso.',
            201,
            (new SindicoResource($sindico))->resolve()
        );
    }

    // Atualiza um síndico
    public function update(Request $request, $id)
    {
        $sindico = Sindico::find($id);

        if (!$sindico) {
            return $this->errorJson(
                'Síndico não encontrado.',
                404
            );
        }

        $validator = Validator::make($request->all(), [
            "nome" => "sometimes|string|max:150",
            "telefone" => "sometimes|string|max:20",
            "email" => "sometimes|email|max:200|unique:sindicos,email_sindico," . $id . ",pk_id_sindico",
            "senha" => "sometimes|string|min:6"
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                'Erro de validação.',
                422,
                $validator->errors()->toArray()
            );
        }

        if ($request->has("nome")) {
            $sindico->nome_sindico = $request->input("nome");
        }

        if ($request->has("telefone")) {
            $sindico->telefone_sindico = $request->input("telefone");
        }

        if ($request->has("email")) {
            $sindico->email_sindico = $request->input("email");
        }

        if ($request->has("senha")) {
            $sindico->senha_sindico = Hash::make($request->input("senha"));
        }

        $sindico->save();

        return $this->responseJson(
            'Síndico atualizado com sucesso.',
            200,
            (new SindicoResource($sindico))->resolve()
        );
    }

    // Deleta um síndico
    public function destroy($id)
    {
        $sindico = Sindico::find($id);

        if (!$sindico) {
            return $this->errorJson(
                'Síndico não encontrado.',
                404
            );
        }

        $sindico->delete();

        return $this->responseJson(
            'Síndico deletado com sucesso.',
            200
        );
    }
}