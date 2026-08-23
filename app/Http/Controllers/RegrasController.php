<?php

namespace App\Http\Controllers;

use App\Http\Resources\RegrasResources;
use Illuminate\Http\Request;
use App\HttpResposta;
use App\Models\Regras;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class RegrasController extends Controller
{
    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    //Função que pega todas as regras
    public function index()
    {
        //Pegando todas as regras
        $regras = Regras::all();

        //Aplicando resurce para tratar a o json com os dados de regras
        $jsonTratadado = RegrasResources::collection($regras);

        //Retorna o json com os moradores filtrados
        return $this->responseJson(
            "Regras recuperadas com sucesso!", //Menssagem
            200, //Status code
            [$jsonTratadado], //Envia o json tratado nos dados da requisição 
        );
    }

   //Função que cria uma regra
    public function store(Request $request)
    {
        //Pegando o usuário logado | Atravez de auth:sanctun, que ingeja o sindico na requisição
        $sindico = $request->user();

        //Pegando o id do sindico
        $id = $sindico->pk_id_sindico;

        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            "nome_regra" => $request->input("nome"), //Pegando o nome
            "descricao_regra" => $request->input("descricao"), //Pegando a descricao da regra
            "fk_id_sindico_regras" => $id, //Pegando o id
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "nome_regra" => 'required|string|max:200',
            "descricao_regra" => 'required|string|max:150',
            "fk_id_sindico_regras" => 'required|numeric',
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

        //Crinado o nova regra
        $novoRegra = Regras::create($dadosMapeados);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Regra criada com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o regra criado
                new RegrasResources($novoRegra)
            ]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    //Função que atuiliza o registro
    public function update(Request $request, string $id)
    {
        //Pegando o usuário logado | Atravez de auth:sanctun, que ingeja o sindico na requisição
        $sindico = $request->user();

        //Pegando o id do sindico
        $idSindico = $sindico->pk_id_sindico;

        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            "nome_regra" => $request->input("nome"), //Pegando o nome
            "descricao_regra" => $request->input("descricao"), //Pegando a descricao da regra
            "fk_id_sindico_regras" => $idSindico, //Pegando o id
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "nome_regra" => 'required|string|max:200',
            "descricao_regra" => 'required|string|max:150',
            "fk_id_sindico_regras" => 'required|numeric',
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

        //Pegando o registro no banco de dados
        $regra = Regras::findOrFail($id);

        //Atualizando os dados do morador com os dados passados no array
        $regraAtualizado = $regra->update($dadosMapeados);

        //Se o update não foi atualizado
        if(!$regraAtualizado){

            //Retornando um responsta Json 
            return $this->responseJson(
                "Não foi possivel realizar a atualização na regra", //Menssagem
                400, //Status code
            );
        }

        //Pegando o o morador com os novos dados
        $novoRegra = Regras::findOrFail($id); 

        //Retorno o json com os dados do regra atualizado
        //Retornando um json de sucuesso
        return $this->responseJson(
            "Regra atualizado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o morador criado
                new RegrasResources($novoRegra)
            ]
        );

    }

    //Função que apaga as regras
    public function destroy(string $id)
    {
        //Regando a regra do banco de dados pelo id
        $regra = Regras::findOrFail($id); 

        //Deletando a regra
        $deletado = $regra->delete();

        //Verificando se a regra foi deletado com sucesso
        if(!$deletado){

            //Retorna um Json de erro
            return $this->errorJson(
                "Não foi possivel deletar a regra", //Menssagem
                400, //Status code
            );
        }

        //Retornando um json de sucesso
        return $this->responseJson(
            "Regra apagada com sucesso!", //Menssagem
            200, //Status code
        );
    }
}
