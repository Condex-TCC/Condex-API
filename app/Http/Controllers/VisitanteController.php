<?php

namespace App\Http\Controllers;

use App\Http\Resources\VisitanteResources;
use App\Models\Visitante;
use Illuminate\Http\Request;
use App\HttpResposta;
use Illuminate\Support\Facades\Validator;

//Classe que intermedia as requisições
class VisitanteController extends Controller
{
    //Chama o pacote de funções reutilizadas para o envio padronizado se JSON de retorno
    use HttpResposta;

    //Retorna todos os visitatnes
    public function index(Request $request)
    {   
        //Comando que cria um construtor flexivel para montar as consulta
        $query = Visitante::query();

        //Verifica se o parametro existe na requisição
        if ($request->query("nome") != null) {

            //Adiciona o filtro na consulta ao banco de dados
            $query->where('nome_visitante', 'LIKE', '%' . $request->input('nome') . '%');
        }

        //Verifica se o parametro existe na requisição
        if ($request->query('cpf') != null) {

            //Adiciona o filtro na consulta ao banco de dados
            $query->where('cpf_visitante', 'LIKE', '%' . $request->input('cpf') . '%');
        }

        //Comando que realiza a consulta, recuperadando todos os registros com as clausulas adicionadas
        $visitantes = $query->get();

        //Retorna um json com formatação padrão
        return $this->responseJson(
            'Visitantes recuperados com sucesso!', //Menssagem
            200, //Status code
            //Dados
            [
                'visitantes' => VisitanteResources::collection($visitantes)
            ]
        );
    }

    //Função que verifica se uma determinado visitan já está cadastrado no banco de dados
    public function consulta(Request $request){

        //Montando um array com os dados vindos da requisição
        $dadosMapeados = [
            "rg_morador" => $request->input("rg"),
            "cpf_visitante" => $request->input("cpf")
        ];

        //Validando os dados vindos da request
        $validador = validator($dadosMapeados, [
            "rg_morador" => "required|string",
            "cpf_visitante" => "required|string",
        ]);

        //Verifica se hove algum erro
        if($validador->fails()){

            //Retorna o json com os erros
            return $this->errorJson(
                "Houve um erro na consulta do visitante",
                400, //Status code
                //Erros
                [
                    $validador->errors(),
                ]
            );
        }

        //Realiza a consulta
        $visitante = Visitante::where("rg_morador", $dadosMapeados["rg_morador"])
            ->where("cpf_visitante", $dadosMapeados['cpf_visitante'])->first();
        
        //Verifica se o visitante existe
        if($visitante != null){

            //Retorna o json
            return $this->responseJson(
                "Visitante econtrado!", //Menssagem
                200, //Status code
                //Dados
                [
                    "encontrado" => true,
                    "visitante" => new VisitanteResources($visitante)
                ]
            );

        }else{

            //Retorna o json
            return $this->responseJson(
                "O visitante não foi encontrado no banco de dados", //Menssagem
                200, //Status code
                //Dados
                [
                    "encontrado" => false,
                    "visitante" => null
                ]
            );
        }
        
    }

    //Função que cadastrar o visitante no banco de dados
    public function store(Request $request){

        //Monstando o array com os dados vindos da requisição
        $dadosMapeados = [
            'nome_visitante' => $request->input("nome"),
            "rg_morador" => $request->input("rg"),
            'cpf_visitante' => $request->input("cpf"),
        ];

        //Validando os dados
        //Validando os dados vindos da request
        $validador = validator($dadosMapeados, [
            "nome_visitante" => "required|string",
            "rg_morador" => "required|string|unique:visitantes,rg_morador",
            "cpf_visitante" => "required|string|unique:visitantes,cpf_visitante",
        ]);

        //Verifica se hove algum erro
        if($validador->fails()){

            //Retorna o json com os erros
            return $this->errorJson(
                "Houve um erro na consulta do visitante",
                400, //Status code
                //Erros
                [
                    $validador->errors(),
                ]
            );
        }

        //Salvando o visitante no banco de dados
        $visitante = Visitante::create($dadosMapeados);

        //Retonra um json com formatação padrão para visitante
        return $this->responseJson(
            "O visitante foi cadastrado com sucesso!",
            200, //Status code
            //Dados
            [
                "Visitante" => new VisitanteResources($visitante)
            ]
        );
    }
}
