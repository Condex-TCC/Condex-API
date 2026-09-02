<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

//Classe que é respnsavel por criar a formatação padão para os elementos JSON
//Essa classe, permite tratar e filtrar os campos que irão aparecer no JSON
class UnidadeResource extends JsonResource
{
    //Função responsavel por fazer a formatação
    public function toArray(Request $request): array
    {
        //Esse função retorna um array associativo com os campos filtrados
        return [
            "id" => $this->pk_id_unidades, //Envia o id da unidade
            'bloco' => $this->bloco_unidade, //envia o bloco da unidade
            'numero' => $this->numero_unidade, //envia o número da unidade
            'descricao' => $this->descricao_unidade, //envia a descrição da unidade
        ];
    }
}
