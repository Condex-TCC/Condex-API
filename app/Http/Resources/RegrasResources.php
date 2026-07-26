<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

//Classe que é respnsavel por criar a formatação padão para os elementos JSON
//Essa classe, permite tratar e filtrar os campos que irão aparecer no JSON
class RegrasResources extends JsonResource
{

    //Função responsavel por fazer a formatação
    public function toArray(Request $request): array
    {
        //Retorna um array associativo com os campos filtrados
        return [
            'id' => $this->pk_id_regra, //Envia o Id da regra
            "regra" => $this->nome_regra, //Envia o nome da regra
            "descricao" => $this->descricao_regra, //Envia a descrição da regra
        ];
    }
}
