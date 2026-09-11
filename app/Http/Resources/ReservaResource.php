<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReservaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->pk_id_reserva,

            'morador' => $this->fk_id_morador,
            'espaco_id' => $this->fk_id_espaco,

            'data' => $this->data_reserva,
            'inicio' => $this->hora_inicio,
            'fim' => $this->hora_fim,

            'status' => $this->status_reserva,

            'espaco' => $this->whenLoaded('espaco', function () {
                return [
                    'id' => $this->espaco->pk_id_espaco,
                    'nome' => $this->espaco->nome_espaco,
                    'descricao' => $this->espaco->descricao_espaco,
                ];
            }),

            'criado_em' => $this->created_at,
            'atualizado_em' => $this->updated_at,
        ];
    }
}