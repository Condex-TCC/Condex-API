<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

//Classe que é respnsavel por criar a formatação padão para os elementos JSON
//Essa classe, permite tratar e filtrar os campos que irão aparecer no JSON
class VisitanteResources extends JsonResource
{
    //Função responsavel por fazer a formatação
    public function toArray(Request $request): array
    {
        //Esse função retorna um array associativo com os campos filtrados
        return [
            'id' => $this->pk_id_visitante,
            'name' => $this->nome_visitante,
            'cpf' => $this->cpf_visitante,
            'rg' => $this->rg_morador
        ];
    }
}
