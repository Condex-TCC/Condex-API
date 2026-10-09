<?php

namespace App\Http\Controllers;

use App\Http\Resources\ComunicadoResources;
use Illuminate\Http\Request;
use App\HttpResposta;
use App\Models\Comunicado;
use App\Models\Envio;
use App\Models\Morador;
use Illuminate\Support\Facades\Validator;

class ComunicadoController extends Controller
{
    use HttpResposta;

    //Recupera todos os comunicados
    public function indexSindico(){

        //Pegando todos os comunicados
        $comunicados = Comunicado::all();

        //Formata para uma formatação padrão o JSON do comunicado
        $jsonTratado = ComunicadoResources::collection($comunicados);

        //Retorna uma formatação de json de succeso
        return $this->responseJson(
            "Os Comunicados foram recuperados com sucesso!", //Menssagem
            200, //Status code
            [
                'comunicados' => $jsonTratado //Dados
            ]
        );
    }
}