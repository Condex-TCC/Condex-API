<?php

namespace App\Http\Controllers;

use App\HttpResposta;
use App\Models\Visita;
use App\Models\Visitante;

class VisitaController extends Controller
{
    use HttpResposta;

    // Lista o histórico de visitas
    public function index()
    {
        $visitas = Visita::with('visitante')
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
    public function allowEntry(string $id)
    {
        $visitante = Visitante::find($id);

        if (!$visitante) {
            return $this->errorJson(
                "Visitante não encontrado.",
                404
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
                "Este visitante já possui uma visita em andamento.",
                400
            );
        }

        $visita = Visita::create([
            'fk_visitante' => $visitante->pk_id_visitante,
            'entrada' => now(),
            'saida' => null,
        ]);

        return $this->responseJson(
            "Entrada do visitante registrada com sucesso!",
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