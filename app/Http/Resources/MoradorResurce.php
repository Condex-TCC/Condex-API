<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

//Classe que é respnsavel por criar a formatação padão para os elementos JSON
//Essa classe, permite tratar e filtrar os campos que irão aparecer no JSON
class MoradorResurce extends JsonResource
{
    //Função responsavel por fazer a formatação
    public function toArray(Request $request): array
    {

        //Esse função retorna um array associativo com os campos filtrados
        return [
            "id" => $this->pk_id_morador, //Envia o ID
            "nome" => $this->nome_morador, //Envia o nome
            "cpf" => $this->cpf_morador, //Envia o CPF
            "email" => $this->email_morador, //Envia o email
            "telefone" => $this->telefone_morador, //Envia o email

            //Envia outra array com a unidade
            "unidade" => [
                "bloco" => $this->unidade->bloco_unidade, //Envia o bloco da unidade
                'numero' => $this->unidade->numero_unidade, //Envia o número da unidade
                "descricao" => $this->unidade->categoria->descricao_categoria_unidade, //Envia a descrição da unidade
            ],
        ];
    }
}
