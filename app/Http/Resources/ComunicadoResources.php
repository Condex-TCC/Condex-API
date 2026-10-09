<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ComunicadoResources extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->pk_id_comunicados,
            'titulo' => $this->titulo_comunicado,
            'descricao' => $this->descricao_comunicado,
            'criado_em' => $this->created_at,
            'atualizado_em' => $this->updated_at,
        ];
    }
}