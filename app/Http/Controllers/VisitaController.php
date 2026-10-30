<?php

namespace App\Http\Controllers;

use App\HttpResposta;
use App\Models\Visita;
use App\Models\Visitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VisitaController extends Controller
{
    use HttpResposta;

    // Returns the visit history
    public function index(Request $request)
    {
        $query = Visita::with(['visitante', 'morador', 'porteiro']);

        // Filters by visitor name
        if ($request->filled('nome')) {
            $query->whereHas('visitante', function ($consulta) use ($request) {
                $consulta->where(
                    'nome_visitante',
                    'LIKE',
                    '%' . $request->input('nome') . '%'
                );
            });
        }

        // Filters by visitor CPF
        if ($request->filled('cpf')) {
            $query->whereHas('visitante', function ($consulta) use ($request) {
                $consulta->where(
                    'cpf_visitante',
                    'LIKE',
                    '%' . $request->input('cpf') . '%'
                );
            });
        }

        $visitas = $query
            ->orderByDesc('entrada')
            ->get()
            ->toArray();

        return $this->responseJson(
            'Histórico de visitas recuperado com sucesso!',
            200,
            $visitas
        );
    }

    // Registers the visitor's entry
    public function allowEntry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fk_porteiro' => 'required|integer|exists:porteiros,pk_id_porteiro',
            'fk_morador' => 'required|integer|exists:moradors,pk_id_morador',
            'fk_visitante' => 'required|integer|exists:visitantes,pk_id_visitante',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                'Os dados informados são inválidos.',
                422,
                $validator->errors()->toArray()
            );
        }

        // Retrieves the authenticated doorman
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

        // Finds the visitor
        $visitante = Visitante::find($request->input('fk_visitante'));

        if (!$visitante) {
            return $this->errorJson(
                'Visitante não encontrado.',
                404
            );
        }

        // Checks whether the visitor already has an open visit
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

        // Creates the visit with all required foreign keys
        $visita = Visita::create([
            'fk_visitante' => $visitante->pk_id_visitante,
            'fk_morador' => $request->input('fk_morador'),
            'fk_porteiro' => $porteiro->pk_id_porteiro,
            'entrada' => now(),
            'saida' => null,
        ]);

        return $this->responseJson(
            'Entrada do visitante registrada com sucesso!',
            201,
            [$visita->load(['visitante', 'morador', 'porteiro'])->toArray()]
        );
    }

    // Registers the visitor's exit
    public function registerExit(string $id)
    {
        $visitante = Visitante::find($id);

        if (!$visitante) {
            return $this->errorJson(
                'Visitante não encontrado.',
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
                'Não existe uma visita em andamento para este visitante.',
                404
            );
        }

        $visita->update([
            'saida' => now(),
        ]);

        return $this->responseJson(
            'Saída do visitante registrada com sucesso!',
            200,
            [$visita->load(['visitante', 'morador', 'porteiro'])->toArray()]
        );
    }
}
