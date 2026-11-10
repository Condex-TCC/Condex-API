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
            "id" => $this->pk_id_visita, //Envia o ID
            "visitante" => new VisitanteResources($this->visitante), //Envia o json do visitante
            "morador" => new MoradorResurce($this->morador), //Envia o json do morador
            "entrada_prevista" => $this->hora_entrada_visita, //Envia a hora prevista de entrada
            "saida_prevista" => $this->hora_saida_visita, //Envia a hora prevista de saida
            "autorizacao" => $this->autorizacao_visita, //Envia a autorização da visita
            "entrada_registro" => $this->hora_registro_entrada_visita, //Envia a hora exata de entrada
            "porteiro_entrada" => new PorteiroResurce($this->porteiroEntrda), //Envia o porteiro que liberou a entrada
            "saida_registro" => $this->hora_registro_saida_visita, //Envia a hora exata de saida do visitante
            "porteiro_saida" => new PorteiroResurce($this-> porteiroSaida), //Envia o porteiro que liberou a saida 

            "criado_em" => $this->created_at,
            "atualizado_em" => $this->updated_at,
        ];
    }
}
