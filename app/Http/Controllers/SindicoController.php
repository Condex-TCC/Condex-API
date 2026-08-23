<?php

namespace App\Http\Controllers;

use App\Models\Sindico;
use App\Http\Resources\SindicoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SindicoController extends Controller
{
    // Recupera todos os síndicos
    public function index()
    {
        $sindicos = Sindico::all();

        return SindicoResource::collection($sindicos);
    }

    // Recupera um síndico específico
    public function show($id)
    {
        $sindico = Sindico::find($id);

        if (!$sindico) {
            return response()->json([
                "message" => "Síndico não encontrado."
            ], 404);
        }

        return new SindicoResource($sindico);
    }

    // Cria um novo síndico
    public function store(Request $request)
    {
        $request->validate([
            "nome_sindico" => "required|string|max:150",
            "telefone_sindico" => "required|string|max:20",
            "email_sindico" => "required|email|max:200|unique:sindicos,email_sindico",
            "senha_sindico" => "required|string|min:6"
        ]);

        $sindico = Sindico::create([
            "nome_sindico" => $request->nome_sindico,
            "telefone_sindico" => $request->telefone_sindico,
            "email_sindico" => $request->email_sindico,
            "senha_sindico" => Hash::make($request->senha_sindico)
        ]);

        return new SindicoResource($sindico);
    }

    // Atualiza um síndico
    public function update(Request $request, $id)
    {
        $sindico = Sindico::find($id);

        if (!$sindico) {
            return response()->json([
                "message" => "Síndico não encontrado."
            ], 404);
        }

        $request->validate([
            "nome_sindico" => "sometimes|string|max:150",
            "telefone_sindico" => "sometimes|string|max:20",
            "email_sindico" => "sometimes|email|max:200|unique:sindicos,email_sindico," . $id . ",pk_id_sindico",
            "senha_sindico" => "sometimes|string|min:6"
        ]);

        if ($request->has("nome_sindico")) {
            $sindico->nome_sindico = $request->nome_sindico;
        }

        if ($request->has("telefone_sindico")) {
            $sindico->telefone_sindico = $request->telefone_sindico;
        }

        if ($request->has("email_sindico")) {
            $sindico->email_sindico = $request->email_sindico;
        }

        if ($request->has("senha_sindico")) {
            $sindico->senha_sindico = Hash::make($request->senha_sindico);
        }

        $sindico->save();

        return new SindicoResource($sindico);
    }

    // Deleta um síndico
    public function destroy($id)
    {
        $sindico = Sindico::find($id);

        if (!$sindico) {
            return response()->json([
                "message" => "Síndico não encontrado."
            ], 404);
        }

        $sindico->delete();

        return response()->json([
            "message" => "Síndico deletado com sucesso."
        ]);
    }
}