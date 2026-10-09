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

            'comunicado' => new ComunicadoResources($this->comunicado),

            //Puxa a formatação do resource do morador
            'morador' => new MoradorResurce($this->morador),

            'resposta' => $this->resposta,

            'contra_resposta' => $this->contra_resposta,

            'visualizado' => $this->visualizado,
        ];
    }
}