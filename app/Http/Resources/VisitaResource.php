<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

//Classe que é respnsavel por criar a formatação padão para os elementos JSON
//Essa classe, permite tratar e filtrar os campos que irão aparecer no JSON
class VisitaResource extends JsonResource
{
    //Função responsavel por fazer a formatação
    public function toArray(Request $request): array
    {
        //Esse função retorna um array associativo com os campos filtrados
        return [
            "id" => $this->pk_id_visita,
            "visitante" => new VisitanteResources($this->visitante),
            "morador" => new MoradorResurce($this->morador),
            "porteiro" => new PorteiroResurce($this->porteiro),
            "entrada" => $this->entrada,
            "saida" => $this->saida,
            "criado_em" => $this->created_at,
            "atualizado_em" => $this->updated_at,
        ];
    }
}
