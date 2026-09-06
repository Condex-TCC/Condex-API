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
            'pk_id_reserva' => $this->pk_id_reserva,

            'fk_id_morador' => $this->fk_id_morador,
            'fk_id_espaco' => $this->fk_id_espaco,

            'data_reserva' => $this->data_reserva,
            'hora_inicio' => $this->hora_inicio,
            'hora_fim' => $this->hora_fim,

            'status_reserva' => $this->status_reserva,

            'espaco' => $this->whenLoaded('espaco', function () {
                return [
                    'pk_id_espaco' => $this->espaco->pk_id_espaco,
                    'nome_espaco' => $this->espaco->nome_espaco,
                    'descricao_espaco' => $this->espaco->descricao_espaco,
                ];
            }),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}