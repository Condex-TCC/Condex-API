<?php

namespace App\Http\Controllers;

use App\Http\Resources\VisitanteResources;
use App\Models\Visitante;
use Illuminate\Http\Request;
use App\HttpResposta;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class VisitanteController extends Controller{

    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    //Função que recupera todos os visitantes
    public function index()
    {
        //Pegando todos os visitantes do banco de dados
        $visitantes = Visitante::all();

        
        //Aplicando resurce para tratar a o json com os dados dos visitantes
        $jsonTratadado = VisitanteResources::collection($visitantes);

        //Retorna o json com os visitantes filtrados
        return $this->responseJson(
            "Usuarios recuperados com sucesso!", //Menssagem
            200, //Status code
            [$jsonTratadado], //Envia o json tratado nos dados da requisição 
        );
    }

    //Função que cadastra o visitante
    public function store(Request $request)
    {
        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            "nome_visitante" => $request->input("nome"),
            "email_visitante" => $request->input("email"),
            'senha_visitante' => Hash::make($request->input("password")),
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "nome_visitante" => 'required|string|max:100',
            "email_visitante" => 'required|string|max:150',
            "senha_visitante" => "required",
        ]);

        //Caso os dados não passasem na validação
        if($validator->fails()){

            //Retorna um Json com fotmatação de erro
            return $this->errorJson(
                "Os dados passados não estão corretos!", //Menssagem
                400, //Status code
                //Passando os erros
                [
                    //Pegando o array de erros dados pelo validator
                    $validator->errors()
                ]
            );
        }

        //Cadastrando o novo visitante
        $novoVisitante = Visitante::create($dadosMapeados);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Visitante criado com sucesso!", //Menssagem
            201, //Status code
            //Passando os dados
            [
                //Passando o morador criado
                new VisitanteResources($novoVisitante)
            ]
        );

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
{
    $visitante = Visitante::findOrFail($id);

    return $this->responseJson(
        "Visitante recuperado com sucesso!",
        200,
        [
            new VisitanteResources($visitante)
        ]
    );
}

    //Função que atualiza o visitante
    public function update(Request $request, string $id)
    {
        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            "nome_visitante" => $request->input("nome"),
            "email_visitante" => $request->input("email"),
            'senha_visitante' => Hash::make($request->input("password")),
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "nome_visitante" => 'required|string|max:100',
            "email_visitante" => 'required|string|max:150',
            "senha_visitante" => "required",
        ]);

        //Caso os dados não passasem na validação
        if($validator->fails()){

            //Retorna um Json com fotmatação de erro
            return $this->errorJson(
                "Os dados passados não estão corretos!", //Menssagem
                400, //Status code
                //Passando os erros
                [
                    //Pegando o array de erros dados pelo validator
                    $validator->errors()
                ]
            );
        }

        //Pegando o visitante do bando de dados pelo id
        $visitante = Visitante::findOrFail($id);

        //Atualizando os dados do visitante
        $atualizado = $visitante->update($dadosMapeados);

         //Se o update não foi atualizado
        if(!$atualizado){

            //Retornando um responsta Json 
            return $this->responseJson(
                "Não foi possivel realizar a atualização do Visitante", //Menssagem
                400, //Status code
            );
        }

        //Pegando o visitante com os novos dados
        $novoVisitante = Visitante::findOrFail($id); 

        //Retorno o json com os dados do visitante atualizado
        //Retornando um json de sucuesso
        return $this->responseJson(
            "Visitante atualizado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o morador criado
                new VisitanteResources($novoVisitante)
            ]
        );
    }

    //Função que remove o visitante
    public function destroy(string $id)
    {
        //Pegando o visitante do banco de dados pelo Id
        $visitante = Visitante::findOrFail($id);

        //Deletando o visitante
        $deletado = $visitante->delete();

        //Verificando de o visitante foi deletado com sucesso
        if(!$deletado){

            //Retorna um Json de erro
            return $this->errorJson(
                "Não foi possivel deletar o visitante", //Menssagem
                400, //Status code
            );
        }

        //Retornando um json de sucesso
        return $this->responseJson(
            "Visitante apagado com sucesso!", //Menssagem
            200, //Status code
        );
    }
}