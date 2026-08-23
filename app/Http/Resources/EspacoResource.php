<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EspacoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'pk_id_espaco' => $this->pk_id_espaco,
            'descricao_espaco' => $this->descricao_espaco,
            'nome_espaco' => $this->nome_espaco,
            'autorizacao' => $this->autorizacao,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}