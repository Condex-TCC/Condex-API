<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitanteResources extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->pk_id_visitante,
            "name" => $this->nome_visitante,
            "cpf" => $this->cpf_visitante,
            "resident_id" => $this->fk_morador,
            "employee_id" => $this->fk_funcionario,
        ];
    }
}