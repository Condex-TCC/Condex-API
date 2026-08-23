<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SindicoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->pk_id_sindico,
            "nome" => $this->nome_sindico,
            "telefone" => $this->telefone_sindico,
            "email" => $this->email_sindico
        ];
    }
}