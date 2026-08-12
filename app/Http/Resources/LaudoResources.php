<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

//Classe que é respnsavel por criar a formatação padão para os elementos JSON
//Essa classe, permite tratar e filtrar os campos que irão aparecer no JSON
class LaudoResources extends JsonResource
{
    //Função responsavel por fazer a formatação
    public function toArray(Request $request): array
    {
        //Esse função retorna um array associativo com os campos filtrados
        return [
            "id" => $this->pk_id_laudos, //Envia o id do laudo
            "laudo" => $this->nome_laudo, //Envia o nome do laudo
            "caminho" => "http://127.0.0.1:8000/storage/" . $this->caminho_laudo, //Envia o caminho do laudo
        ];
    }
}
