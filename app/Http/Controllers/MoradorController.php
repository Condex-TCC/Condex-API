<?php

namespace App\Http\Controllers;

use App\Http\Resources\MoradorResurce;
use App\Models\Morador;
use Illuminate\Http\Request;
use App\HttpResposta;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class MoradorController extends Controller
{
    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    //Função que recupera todos os moradores
    public function index()
    {
        //Pegando todos os moradores, com os moradores e carregando o relacionamento com unidade
        $moradores = Morador::with('unidade')->get();

        //Aplicando resurce para tratar a o json com os dados das moradores
        $jsonTratadado = MoradorResurce::collection($moradores);

        //Retorna o json com os moradores filtrados
        return $this->responseJson(
            "Usuarios recuperados com sucesso!", //Menssagem
            200, //Status code
            [$jsonTratadado], //Envia o json tratado nos dados da requisição 
        );
    }

    //Função que cadastra o morador
    public function store(Request $request)
    {
        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            "nome_morador" => $request->input("nome"), //Pegando o nome
            "cpf_morador" => $request->input("cpf"), //Pegando o cpf
            "email_morador" => $request->input("email"), //Pegando o email
            "telefone_morador" => $request->input("telefone"), //Pegando o telefone
            "senha_morador" => Hash::make($request->input("password")), //Pegando a senha e transformando-a em hash
            "fk_id_unidade_morador" => $request->input("id_unidade"), //Pegando o id da unidade
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "nome_morador" => 'required|string|max:100',
            "cpf_morador" => 'required|string|min:14',
            "email_morador" => 'required|string|max:150',
            "telefone_morador" => 'required|string|max:20',
            "senha_morador" => "required",
            "fk_id_unidade_morador" => 'required|numeric',
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

        //Criando um novo Morador
        $novoMorador = Morador::create($dadosMapeados);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Morador criado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o morador criado
                new MoradorResurce($novoMorador->load("unidade"))
            ]
        );

    }

   //Função que recupera o morador
    public function show(string $id)
    {
        //Pegando o morador no banco de dados
        $morador = Morador::findOrFail($id);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Morador recuperado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o morador criado
                new MoradorResurce($morador)
            ]
        );
    }

    //Função que atualiza um morador
    public function update(Request $request, string $id)
    {
        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            "nome_morador" => $request->input("nome"), //Pegando o nome
            "cpf_morador" => $request->input("cpf"), //Pegando o cpf
            "email_morador" => $request->input("email"), //Pegando o email
            "telefone_morador" => $request->input("telefone"), //Pegando o telefone
            "senha_morador" => Hash::make($request->input("password")), //Pegando a senha e transformando-a em hash
            "fk_id_unidade_morador" => $request->input("id_unidade"), //Pegando o id da unidade
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "nome_morador" => 'required|string|max:100',
            "cpf_morador" => 'required|string|min:11',
            "email_morador" => 'required|string|max:150',
            "telefone_morador" => 'required|string|max:20',
            "senha_morador" => "required",
            "fk_id_unidade_morador" => 'required|numeric',
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

        //Regando o morador do banco de dados pelo id
        $morador = Morador::findOrFail($id); 

        //Atualizando os dados do morador com os dados passados no array
        $moradorAtualizado = $morador->update($dadosMapeados);

        //Se o update não foi atualizado
        if(!$moradorAtualizado){

            //Retornando um responsta Json 
            return $this->responseJson(
                "Não foi possivel realizar a atualização do Morador", //Menssagem
                400, //Status code
            );
        }

        //Pegando o o morador com os novos dados
        $novoMorador = Morador::findOrFail($id); 

        //Retorno o json com os dados do morador atualizado
        //Retornando um json de sucuesso
        return $this->responseJson(
            "Morador atualizado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o morador criado
                new MoradorResurce($novoMorador->load("unidade"))
            ]
        );
    }

    //Função que remove o morador
    public function destroy(string $id)
    {
        //Regando o morador do banco de dados pelo id
        $morador = Morador::findOrFail($id); 

        //Deletando o morador
        $deletado = $morador->delete();

        //Verificando de o morador foi deletado com sucesso
        if(!$deletado){

            //Retorna um Json de erro
            return $this->errorJson(
                "Não foi possivel deletar o morador", //Menssagem
                400, //Status code
            );
        }

        //Retornando um json de sucesso
        return $this->responseJson(
            "Morador apagado com sucesso!", //Menssagem
            200, //Status code
        );
    }
}
