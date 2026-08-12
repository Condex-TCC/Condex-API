<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

// Classe responsável por criar a formatação padrão para os elementos JSON
// Essa classe permite tratar e filtrar os campos que irão aparecer no JSON
class EncomendaResources extends JsonResource
{
    // Função responsável por fazer a formatação
    public function toArray(Request $request): array
    {
        // Retorna um array associativo com os campos filtrados
        return [
            "id" => $this->pk_id_encomenda,
            "nome" => $this->nome_encomenda,
            "descricao" => $this->descricao_encomenda,
            "porteiro" => $this->fk_id_porteiro_encomenda,
            "data_retirada" => $this->data_retirada,
        ];
    }
}