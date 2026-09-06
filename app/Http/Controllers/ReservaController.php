<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservaResource;
use App\Models\Reserva;
use App\Models\Espaco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReservaController extends Controller
{
    // Retorna todas as reservas do morador logado
    public function index(Request $request)
    {
        $morador = $request->user();

        $reservas = Reserva::with('espaco')
            ->where('fk_id_morador', $morador->pk_id_morador)
            ->get();

        return ReservaResource::collection($reservas);
    }

    // Retorna uma reserva específica do morador logado
    public function show(Request $request, $id)
    {
        $morador = $request->user();

        $reserva = Reserva::with('espaco')
            ->where('pk_id_reserva', $id)
            ->where('fk_id_morador', $morador->pk_id_morador)
            ->first();

        if (!$reserva) {
            return response()->json([
                'message' => 'Reserva não encontrada.'
            ], 404);
        }

        return new ReservaResource($reserva);
    }

    // Morador solicita uma nova reserva
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fk_id_espaco' => 'required|exists:espacos,pk_id_espaco',
            'data_reserva' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fim' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors()
            ], 422);
        }

        $espaco = Espaco::find($request->fk_id_espaco);

        if (!$espaco->autorizacao) {
            return response()->json([
                'message' => 'Este espaço não está disponível para reservas.'
            ], 400);
        }

        // Verifica se já existe uma reserva ou solicitação conflitante
        $conflito = Reserva::where('fk_id_espaco', $request->fk_id_espaco)
            ->where('data_reserva', $request->data_reserva)
            ->where('status_reserva', '!=', 'cancelada')
            ->where(function ($query) use ($request) {
                $query->where('hora_inicio', '<', $request->hora_fim)
                    ->where('hora_fim', '>', $request->hora_inicio);
            })
            ->exists();

        if ($conflito) {
            return response()->json([
                'message' => 'Este espaço já possui uma reserva ou solicitação para esse período.'
            ], 409);
        }

        $morador = $request->user();

        $reserva = Reserva::create([
            'fk_id_morador' => $morador->pk_id_morador,
            'fk_id_espaco' => $request->fk_id_espaco,
            'data_reserva' => $request->data_reserva,
            'hora_inicio' => $request->hora_inicio,
            'hora_fim' => $request->hora_fim,
            'status_reserva' => 'pendente',
        ]);

        $reserva->load('espaco');

        return response()->json([
            'message' => 'Solicitação de reserva realizada com sucesso.',
            'data' => new ReservaResource($reserva)
        ], 201);
    }

    // Atualiza uma reserva do morador
    public function update(Request $request, $id)
    {
        $morador = $request->user();

        $reserva = Reserva::where('pk_id_reserva', $id)
            ->where('fk_id_morador', $morador->pk_id_morador)
            ->first();

        if (!$reserva) {
            return response()->json([
                'message' => 'Reserva não encontrada.'
            ], 404);
        }

        if ($reserva->status_reserva === 'cancelada') {
            return response()->json([
                'message' => 'Não é possível alterar uma reserva cancelada.'
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'fk_id_espaco' => 'sometimes|exists:espacos,pk_id_espaco',
            'data_reserva' => 'sometimes|date',
            'hora_inicio' => 'sometimes|date_format:H:i',
            'hora_fim' => 'sometimes|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação.',
                'errors' => $validator->errors()
            ], 422);
        }

        $espaco = $request->fk_id_espaco ?? $reserva->fk_id_espaco;
        $data = $request->data_reserva ?? $reserva->data_reserva;
        $inicio = $request->hora_inicio ?? $reserva->hora_inicio;
        $fim = $request->hora_fim ?? $reserva->hora_fim;

        if ($fim <= $inicio) {
            return response()->json([
                'message' => 'O horário final deve ser posterior ao horário inicial.'
            ], 422);
        }

        $conflito = Reserva::where('fk_id_espaco', $espaco)
            ->where('data_reserva', $data)
            ->where('status_reserva', '!=', 'cancelada')
            ->where('pk_id_reserva', '!=', $reserva->pk_id_reserva)
            ->where(function ($query) use ($inicio, $fim) {
                $query->where('hora_inicio', '<', $fim)
                    ->where('hora_fim', '>', $inicio);
            })
            ->exists();

        if ($conflito) {
            return response()->json([
                'message' => 'Este espaço já possui uma reserva ou solicitação para esse período.'
            ], 409);
        }

        $reserva->update([
            'fk_id_espaco' => $espaco,
            'data_reserva' => $data,
            'hora_inicio' => $inicio,
            'hora_fim' => $fim,
        ]);

        $reserva->load('espaco');

        return new ReservaResource($reserva);
    }

    // Cancela uma reserva
    public function destroy(Request $request, $id)
    {
        $morador = $request->user();

        $reserva = Reserva::where('pk_id_reserva', $id)
            ->where('fk_id_morador', $morador->pk_id_morador)
            ->first();

        if (!$reserva) {
            return response()->json([
                'message' => 'Reserva não encontrada.'
            ], 404);
        }

        $reserva->update([
            'status_reserva' => 'cancelada'
        ]);

        return response()->json([
            'message' => 'Reserva cancelada com sucesso.'
        ]);
    }
}
