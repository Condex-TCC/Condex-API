<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutorizacaoResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->pk_id_autorizacao,

            "visitante" => [
                "id" => $this->visitante?->pk_id_visitante,
                "nome" => $this->visitante?->nome_visitante,
                "email" => $this->visitante?->email_visitante,
            ],

            "morador" => [
                "id" => $this->morador?->pk_id_morador,
                "nome" => $this->morador?->nome_morador,
            ],

            "status" => $this->status,
            "data" => $this->data_autorizacao,
            "entrada" => $this->entrada_em,
            "saida" => $this->saida_em,

            "criado_em" => $this->created_at,
            "atualizado_em" => $this->updated_at,
        ];
    }
}