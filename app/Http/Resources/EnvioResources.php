<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnvioResources extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->pk_id_envio,

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

            'resposta' => $this->resposta,

            'contra_resposta' => $this->contra_resposta,

            'visualizado' => $this->visualizado,
        ];
    }
}