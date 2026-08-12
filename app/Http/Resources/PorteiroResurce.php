<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

//Classe que é respnsavel por criar a formatação padão para os elementos JSON
//Essa classe, permite tratar e filtrar os campos que irão aparecer no JSON
class PorteiroResurce extends JsonResource
{
    //Função responsavel por fazer a formatação
    public function toArray(Request $request): array
    {
        //Esse função retorna um array associativo com os campos filtrados
        return [
            "id" => $this->pk_id_porteiro, //Envia o id
            "nome" => $this->nome_porteito, //Envia o nome
            "email" => $this->email_porteiro, //Envia o email
        ];
    }
}
