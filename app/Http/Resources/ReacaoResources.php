<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReacaoResources extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->pk_id_reacao,

            'tipo_reacao' => $this->tipo_reacao,

            'morador' => $this->morador
                ? [
                    'id' => $this->morador->pk_id_morador,
                    'nome' => $this->morador->nome_morador,
                ]
                : null,

            'comunicado' => $this->comunicado
                ? [
                    'id' => $this->comunicado->pk_id_comunicados,
                    'descricao' => $this->comunicado->descricao_comunicado,
                ]
                : null,
        ];
    }
}