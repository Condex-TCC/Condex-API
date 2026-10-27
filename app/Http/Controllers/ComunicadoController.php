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

    
    // Recupera todos os comunicados, com opção de pesquisa
    public function indexSindico(Request $request)
    {
        // Inicia a consulta
        $query = Comunicado::query();

        // Filtra pela descrição quando o parâmetro for informado
        if ($request->filled('descricao')) {
            $query->where(
                'descricao_comunicado',
                'LIKE',
                '%' . $request->input('descricao') . '%'
            );
        }

        // Recupera os comunicados encontrados
        $comunicados = $query->get();

        // Formata o JSON dos comunicados
        $jsonTratado = ComunicadoResources::collection($comunicados);

        // Retorna a resposta JSON
        return $this->responseJson(
            "Os Comunicados foram recuperados com sucesso!",
            200,
            [
                'comunicados' => $jsonTratado
            ]
        );
    }

}