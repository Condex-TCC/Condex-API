<?php

namespace App\Http\Controllers;

use App\HttpResposta;
use App\Models\Visita;
use App\Models\Visitante;
use Illuminate\Http\Request;

class VisitaController extends Controller
{
    use HttpResposta;

    
public function index(Request $request)
{
    // Inicia a consulta com o relacionamento do visitante
    $query = Visita::with('visitante');

    // Filtra pelo nome do visitante
    if ($request->filled('nome')) {
        $query->whereHas('visitante', function ($consulta) use ($request) {
            $consulta->where(
                'nome_visitante',
                'LIKE',
                '%' . $request->input('nome') . '%'
            );
        });
    }

    // Filtra pelo CPF do visitante
    if ($request->filled('cpf')) {
        $query->whereHas('visitante', function ($consulta) use ($request) {
            $consulta->where(
                'cpf_visitante',
                'LIKE',
                '%' . $request->input('cpf') . '%'
            );
        });
    }

    // Mantém a ordenação do histórico
    $visitas = $query
        ->orderByDesc('entrada')
        ->get()
        ->toArray();

    return $this->responseJson(
        "Histórico de visitas recuperado com sucesso!",
        200,
        $visitas
    );
}


// Registra a entrada de um visitante autorizado pelo interfone
public function allowEntry(Request $request)
{
    $validator = \Illuminate\Support\Facades\Validator::make(
        $request->all(),
        [
            'fk_porteiro' => 'required|integer|exists:porteiros,pk_id_porteiro',
            'fk_morador' => 'required|integer|exists:moradors,pk_id_morador',
            'fk_visitante' => 'required|integer|exists:visitantes,pk_id_visitante',
        ]
    );

    if ($validator->fails()) {
        return $this->errorJson(
            'Os dados informados são inválidos.',
            422,
            $validator->errors()->toArray()
        );
    }

    $porteiro = $request->user();

    if (
        !$porteiro ||
        (int) $porteiro->pk_id_porteiro !==
        (int) $request->input('fk_porteiro')
    ) {
        return $this->errorJson(
            'O ID informado não corresponde ao porteiro autenticado.',
            403
        );
    }

    $visitante = Visitante::find(
    $request->input('fk_visitante')
);

if (!$visitante) {
    return $this->errorJson(
        'Visitante não encontrado.',
        404
    );
}

    if (
        (int) $visitante->fk_morador !==
        (int) $request->input('fk_morador')
    ) {
        return $this->errorJson(
            'O visitante não pertence ao morador informado.',
            422
        );
    }

    $visitaAberta = Visita::where(
        'fk_visitante',
        $visitante->pk_id_visitante
    )
        ->whereNotNull('entrada')
        ->whereNull('saida')
        ->exists();

    if ($visitaAberta) {
        return $this->errorJson(
            'Este visitante já possui uma visita em andamento.',
            400
        );
    }

    $visita = Visita::create([
        'fk_visitante' => $visitante->pk_id_visitante,
        'entrada' => now(),
        'saida' => null,
    ]);

    return $this->responseJson(
        'Entrada do visitante registrada com sucesso!',
        201,
        [$visita->load('visitante')->toArray()]
    );
}



    // Registra a saída do visitante
    public function registerExit(string $id)
    {
        $visitante = Visitante::find($id);

        if (!$visitante) {
            return $this->errorJson(
                "Visitante não encontrado.",
                404
            );
        }

        $visita = Visita::where(
            'fk_visitante',
            $visitante->pk_id_visitante
        )
            ->whereNotNull('entrada')
            ->whereNull('saida')
            ->orderByDesc('entrada')
            ->first();

        if (!$visita) {
            return $this->errorJson(
                "Não existe uma visita em andamento para este visitante.",
                404
            );
        }

        $visita->update([
            'saida' => now(),
        ]);

        return $this->responseJson(
            "Saída do visitante registrada com sucesso!",
            200,
            [$visita->load('visitante')->toArray()]
        );
    }
}