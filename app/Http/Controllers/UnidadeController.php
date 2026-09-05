<?php

namespace App\Http\Controllers;

use App\Http\Resources\UnidadeResource;
use Illuminate\Http\Request;
use App\HttpResposta;
use App\Models\Unidade;
use Illuminate\Support\Facades\Validator;

class UnidadeController extends Controller
{
    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    //Função que pega todas as regras
    public function index()
    {
        //Pegando todas as unidades
        $unidades = Unidade::all();

        //Aplicando resurce para tratar a o json com os dados de regras
        $jsonTratadado = UnidadeResource::collection($unidades);

        //Retorna o json com os moradores filtrados
        return $this->responseJson(
            "Regras recuperadas com sucesso!", //Menssagem
            200, //Status code
            [
                $jsonTratadado
            ], //Envia o json tratado nos dados da requisição 
        );

    }

    //Função que cria uma regra
    public function store(Request $request)
    {
        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            'bloco_unidade' => $request->input("bloco"),
            'numero_unidade' => $request->input("numero"),
            'descricao_unidade' => $request->input('descricao'),
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "bloco_unidade" => 'required|string|max:50',
            "numero_unidade" => 'required|numeric',
            "descricao_unidade" => 'nullable|string',
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

        //Crinado nova unidade
        $novaUnidade = Unidade::create($dadosMapeados);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Unidade criada com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o regra criado
                new UnidadeResource($novaUnidade)
            ]
        );
    }

    //Função que pega uma unidade do banco ade dados
    public function show(string $id)
    {
        //Recupera a unidade no banco de dados
        $unidade = Unidade::findOrFail($id);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Unidade recupedado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando a regra recuperada
                new UnidadeResource($unidade)
            ]
        );
    }

    //Função que atuiliza o registro
    public function update(Request $request, string $id)
    {
        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            'bloco_unidade' => $request->input("bloco"),
            'numero_unidade' => $request->input("numero"),
            'descricao_unidade' => $request->input('descricao'),
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "bloco_unidade" => 'required|string|max:50',
            "numero_unidade" => 'required|numeric',
            "descricao_unidade" => 'nullable|string',
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

        //Pegando a unidade salva no banco de dados
        $unidade = Unidade::findOrFail($id);

        //Atualizando os dados da unidade com os dados passados no array
        $unidadeAtualizada = $unidade->update($dadosMapeados);

        //Se o update não foi atualizado
        if(!$unidadeAtualizada){

            //Retornando um responsta Json 
            return $this->responseJson(
                "Não foi possivel realizar a atualização na unidade", //Menssagem
                400, //Status code
            );
        }

        //Recupera a unidade no banco de dados
        $unidade = Unidade::findOrFail($id);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Unidade Atualizada com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando a regra recuperada
                new UnidadeResource($unidade)
            ]
        );
    }

    //Função que apaga as unidades
    public function destroy(string $id)
    {
        //Recupera a unidade no banco de dados
        $unidade = Unidade::findOrFail($id);

        //Deletando a unidade
        $deletado = $unidade->delete();

        //Verificando se a unidade foi deletado com sucesso
        if(!$deletado){

            //Retorna um Json de erro
            return $this->errorJson(
                "Não foi possivel deletar a unidade", //Menssagem
                400, //Status code
            );
        }

        //Retornando um json de sucesso
        return $this->responseJson(
            "Unidade apagada com sucesso!", //Menssagem
            200, //Status code
        );
    }
}
