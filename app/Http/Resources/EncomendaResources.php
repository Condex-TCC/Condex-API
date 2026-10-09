<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EncomendaResources extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->pk_id_encomenda,
            'nome' => $this->nome_encomenda,
            'descricao' => $this->descricao_encomenda,

            'morador' => [
                'id' => $this->morador?->pk_id_morador,
                'nome' => $this->morador?->nome_morador,
            ],

            'porteiro' => [
                'id' => $this->porteiro?->pk_id_porteiro,
                'nome' => $this->porteiro?->nome_porteiro,
            ],

            'data_retirada' => $this->data_retirada,
        ];
    }
}