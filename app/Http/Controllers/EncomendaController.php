<?php

namespace App\Http\Controllers;

use App\Http\Resources\EncomendaResources;
use Illuminate\Http\Request;
use App\HttpResposta;
use App\Models\Encomenda;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class EncomendaController extends Controller
{
    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    //Função que pega todas as encomendas
    public function index()
    {
        //Pegando todas as encomendas
        $encomendas = Encomenda::all();

        //Aplicando resurce para tratar a o json com os dados de encomendas
        $jsonTratadado = EncomendaResources::collection($encomendas);

        //Retorna o json com os moradores filtrados
        return $this->responseJson(
            "Encomendas recuperadas com sucesso!", //Menssagem
            200, //Status code
            [$jsonTratadado], //Envia o json tratado nos dados da requisição 
        );
    }

   //Função que cria uma encomenda
    public function store(Request $request)
    {
        //Pegando o usuário logado | Atravez de auth:sanctun, que ingeja o porteiro na requisição
        $porteiro = $request->user();

        //Pegando o id do porteiro
        $id = $porteiro->pk_id_porteiro;

        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            "nome_encomenda" => $request->input("nome"),
            "descricao_encomenda" => $request->input("descricao"),
            "fk_id_porteiro_encomenda" => $id,
     ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "nome_encomenda" => 'required|string|max:200',
            "descricao_encomenda" => 'required|string|max:150',
            "fk_id_porteiro_encomenda" => 'required|numeric',
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

        //Crinado o nova encomenda
        $novaEncomenda = Encomenda::create($dadosMapeados);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Encomenda criada com sucesso!", //Menssagem
            201, //Status code
            //Passando os dados
            [
                //Passando a encomenda criada
                new EncomendaResources($novaEncomenda)
            ]
        );
    }


    // Função que pega uma encomenda específica
    public function show(string $id)
    {
        // Pegando a encomenda pelo ID
        $encomenda = Encomenda::findOrFail($id);

        // Retornando a encomenda encontrada
        return $this->responseJson(
            "Encomenda recuperada com sucesso!",
            200,
            [
                new EncomendaResources($encomenda)
            ]
        );
    }


    //Função que atuiliza o registro
    public function update(Request $request, string $id)
    {
        //Pegando o usuário logado | Atravez de auth:sanctun, que ingeja o porteiro na requisição
        $porteiro = $request->user();

        //Pegando o id do porteiro
        $idPorteiro = $porteiro->pk_id_porteiro;

        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            "nome_encomenda" => $request->input("nome"), //Pegando o nome
            "descricao_encomenda" => $request->input("descricao"), //Pegando a descricao da encomenda
            "fk_id_porteiro_encomenda" => $idPorteiro, //Pegando o id
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "nome_encomenda" => 'required|string|max:200',
            "descricao_encomenda" => 'required|string|max:150',
            "fk_id_porteiro_encomenda" => 'required|numeric',
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
        $encomenda = Encomenda::findOrFail($id);

        //Atualizando os dados do morador com os dados passados no array
        $encomendaAtualizada = $encomenda->update($dadosMapeados);

        //Se o update não foi atualizado
        if(!$encomendaAtualizada){

            //Retornando um responsta Json 
            return $this->responseJson(
                "Não foi possivel realizar a atualização na encomenda", //Menssagem
                400, //Status code
            );
        }

        //Pegando o o morador com os novos dados
        $novaEncomenda = Encomenda::findOrFail($id); 

        //Retorno o json com os dados do regra atualizado
        //Retornando um json de sucuesso
        return $this->responseJson(
            "Encomenda atualizada com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando a encomenda criada
                new EncomendaResources($novaEncomenda)
            ]
        );

    }

    // Função que registra a retirada de uma encomenda
    public function registerWithdrawal(string $id)
    {
    // Pegando a encomenda pelo ID
    $encomenda = Encomenda::findOrFail($id);

    // Verificando se a encomenda já foi retirada
    if ($encomenda->data_retirada !== null) {

        return $this->errorJson(
            "Esta encomenda já foi retirada!",
            400
        );
    }

    // Registrando a data e hora da retirada
    $encomenda->data_retirada = now();

    // Salvando a alteração
    $retiradaRegistrada = $encomenda->save();

    // Verificando se a retirada foi registrada
    if (!$retiradaRegistrada) {

        return $this->errorJson(
            "Não foi possível registrar a retirada da encomenda!",
            400
        );
    }

    // Buscando novamente a encomenda atualizada
    $encomendaAtualizada = Encomenda::findOrFail($id);

    // Retornando resposta de sucesso
    return $this->responseJson(
        "Retirada da encomenda registrada com sucesso!",
        200,
        [
            new EncomendaResources($encomendaAtualizada)
        ]
    );
    }

    //Função que apaga as encomendas
    public function destroy(string $id)
    {
        //Regando a encomenda do banco de dados pelo id
        $encomenda = Encomenda::findOrFail($id); 

        //Deletando a encomenda
        $deletado = $encomenda->delete();

        //Verificando se a encomenda foi deletada com sucesso
        if(!$deletado){

            //Retorna um Json de erro
            return $this->errorJson(
                "Não foi possivel deletar a encomenda", //Menssagem
                400, //Status code
            );
        }

        //Retornando um json de sucesso
        return $this->responseJson(
            "Encomenda apagada com sucesso!", //Menssagem
            200, //Status code
        );
    }
}
