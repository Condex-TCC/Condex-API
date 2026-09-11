<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservaResource;
use App\Models\Reserva;
use App\Models\Espaco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\HttpResposta;

class ReservaController extends Controller
{
    use HttpResposta;

    // Retorna todas as reservas do morador logado
    public function index(Request $request)
    {
        $morador = $request->user();

        $reservas = Reserva::with('espaco')
            ->where('fk_id_morador', $morador->pk_id_morador)
            ->get();

        return $this->responseJson(
            'Reservas encontradas com sucesso.',
            200,
            ReservaResource::collection($reservas)->resolve()
        );
    }

    // Retorna uma reserva específica do morador logado
    public function show(Request $request, string $id)
    {
        $morador = $request->user();

        $reserva = Reserva::with('espaco')
            ->where('pk_id_reserva', $id)
            ->where('fk_id_morador', $morador->pk_id_morador)
            ->first();

        if (!$reserva) {
            return $this->errorJson(
                'Reserva não encontrada.',
                404
            );
        }

        return $this->responseJson(
            'Reserva encontrada com sucesso.',
            200,
            (new ReservaResource($reserva))->resolve()
        );
    }

    // Cria uma nova reserva
    public function store(Request $request)
    {
        $morador = $request->user();

        $validator = Validator::make($request->all(), [
            'espaco' => 'required|exists:espacos,pk_id_espaco',
            'data' => 'required|date',
            'inicio' => 'required|date_format:H:i',
            'fim' => 'required|date_format:H:i|after:inicio',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                'Erro de validação.',
                422,
                $validator->errors()->toArray()
            );
        }

        $espaco = Espaco::find($request->espaco);

        if (!$espaco || !$espaco->autorizacao) {
            return $this->errorJson(
                'Este espaço não está disponível para reservas.',
                400
            );
        }

        $conflito = Reserva::where('fk_id_espaco', $request->espaco)
            ->where('data_reserva', $request->data)
            ->where(function ($query) use ($request) {
                $query->where('hora_inicio', '<', $request->fim)
                    ->where('hora_fim', '>', $request->inicio);
            })
            ->exists();

        if ($conflito) {
            return $this->errorJson(
                'Este espaço já está reservado neste horário.',
                400
            );
        }

        $reserva = Reserva::create([
            'fk_id_morador' => $morador->pk_id_morador,
            'fk_id_espaco' => $request->espaco,
            'data_reserva' => $request->data,
            'hora_inicio' => $request->inicio,
            'hora_fim' => $request->fim,
            'status_reserva' => 'pendente',
        ]);

        return $this->responseJson(
            'Reserva solicitada com sucesso.',
            201,
            (new ReservaResource($reserva->load('espaco')))->resolve()
        );
    }

    // Atualiza uma reserva
    public function update(Request $request, string $id)
    {
        $morador = $request->user();

        $reserva = Reserva::where('pk_id_reserva', $id)
            ->where('fk_id_morador', $morador->pk_id_morador)
            ->first();

        if (!$reserva) {
            return $this->errorJson(
                'Reserva não encontrada.',
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'espaco' => 'sometimes|exists:espacos,pk_id_espaco',
            'data' => 'sometimes|date',
            'inicio' => 'sometimes|date_format:H:i',
            'fim' => 'sometimes|date_format:H:i',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                'Erro de validação.',
                422,
                $validator->errors()->toArray()
            );
        }

        $espaco = $request->espaco ?? $reserva->fk_id_espaco;
        $data = $request->data ?? $reserva->data_reserva;
        $horaInicio = $request->inicio ?? $reserva->hora_inicio;
        $horaFim = $request->fim ?? $reserva->hora_fim;

        $conflito = Reserva::where('fk_id_espaco', $espaco)
            ->where('data_reserva', $data)
            ->where('pk_id_reserva', '!=', $reserva->pk_id_reserva)
            ->where(function ($query) use ($horaInicio, $horaFim) {
                $query->where('hora_inicio', '<', $horaFim)
                    ->where('hora_fim', '>', $horaInicio);
            })
            ->exists();

        if ($conflito) {
            return $this->errorJson(
                'Este espaço já está reservado neste horário.',
                400
            );
        }

        $reserva->update([
            'fk_id_espaco' => $espaco,
            'data_reserva' => $data,
            'hora_inicio' => $horaInicio,
            'hora_fim' => $horaFim,
        ]);

        return $this->responseJson(
            'Reserva atualizada com sucesso.',
            200,
            (new ReservaResource($reserva->load('espaco')))->resolve()
        );
    }

    // Remove uma reserva
    public function destroy(Request $request, string $id)
    {
        $morador = $request->user();

        $reserva = Reserva::where('pk_id_reserva', $id)
            ->where('fk_id_morador', $morador->pk_id_morador)
            ->first();

        if (!$reserva) {
            return $this->errorJson(
                'Reserva não encontrada.',
                404
            );
        }

        $reserva->delete();

        return $this->responseJson(
            'Reserva excluída com sucesso.',
            200
        );
    }
}