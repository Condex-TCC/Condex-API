<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

//Classe que é respnsavel por criar a formatação padão para os elementos JSON
//Essa classe, permite tratar e filtrar os campos que irão aparecer no JSON
class EncomendaResources extends JsonResource
{

    //Função responsavel por fazer a formatação
    public function toArray(Request $request): array
    {
        //Retorna um array associativo com os campos filtrados
        return [
            'id' => $this->pk_id_encomenda, //Envia o Id da encomenda
            "nome" => $this->nome_encomenda, //Envia o nome da encomenda
            "descricao" => $this->descricao_encomenda, //Envia a descrição da encomenda
            "porteiro" => $this->fk_id_porteiro_encomenda, //Envia o ID do porteiro associado
        ];
    }
}
