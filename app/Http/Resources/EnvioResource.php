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

            'comunicado' => $this->comunicado
                ? [
                    'id' => $this->comunicado->pk_id_comunicados,
                    'descricao' => $this->comunicado->descricao_comunicado,
                ]
                : null,

            'resposta' => $this->resposta
                ? [
                    'id' => $this->resposta->pk_id_resposta,
                    'descricao' => $this->resposta->descricao_resposta,

                    'morador' => $this->resposta->morador
                        ? [
                            'id' => $this->resposta->morador->pk_id_morador,
                            'nome' => $this->resposta->morador->nome_morador,
                        ]
                        : null,
                ]
                : null,

            'contra_resposta' => $this->contraResposta
                ? [
                    'id' => $this->contraResposta->pk_id_contra_resposta,
                    'descricao' => $this->contraResposta->descricao_contra_resposta,
                ]
                : null,
        ];
    }
}