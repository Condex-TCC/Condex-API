<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

//Classe que é respnsavel por criar a formatação padão para os elementos JSON
//Essa classe, permite tratar e filtrar os campos que irão aparecer no JSON
class ReclamacaoRecource extends JsonResource
{
    //Função responsavel por fazer a formatação
    public function toArray(Request $request): array
    {
        //Esse função retorna um array associativo com os campos filtrados
        return [
            "id_reclamacao" => $this->pk_id_reclamacao,
            "reclamacao" => $this->reclamacao,
            "resposta" => $this->resposta_reclamacao,
            "visualizado" => $this->visualizado_reclamacao == 1 ? true : false,
            "morador" => new MoradorResurce($this->morador),
            "sindico" => new SindicoResource($this->sindico),
        ];
    }
}
