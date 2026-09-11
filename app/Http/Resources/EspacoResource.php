<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EspacoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->pk_id_espaco,
            'descricao' => $this->descricao_espaco,
            'nome' => $this->nome_espaco,
            'disponivel' => $this->autorizacao,
            'criado_em' => $this->created_at,
            'atualizado_em' => $this->updated_at,
        ];
    }
}