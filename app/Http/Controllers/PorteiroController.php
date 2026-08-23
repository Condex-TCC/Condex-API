<?php

namespace App\Http\Controllers;

use App\Http\Resources\PorteiroResurce;
use App\Models\Porteiro;
use Illuminate\Http\Request;
use App\HttpResposta;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class PorteiroController extends Controller
{
    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    //Função que recupera todos os moradores
    public function index()
    {
        //Pegando todos os porterios do banco de dados
        $porteiros = Porteiro::all();

        
        //Aplicando resurce para tratar a o json com os dados dos porteiros
        $jsonTratadado = PorteiroResurce::collection($porteiros);

        //Retorna o json com os moradores filtrados
        return $this->responseJson(
            "Usuarios recuperados com sucesso!", //Menssagem
            200, //Status code
            [$jsonTratadado], //Envia o json tratado nos dados da requisição 
        );
    }

    //Função que cadastra o porteiro
    public function store(Request $request)
    {
        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            "nome_porteiro" => $request->input("nome"),
            "email_porteiro" => $request->input("email"),
            "senha_porteiro" => Hash::make($request->input("password")),
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "nome_porteiro" => 'required|string|max:100',
            "email_porteiro" => 'required|string|max:150',
            "senha_porteiro" => "required",
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

        //Cadastrando o novo porteiro
        $novoPorteiro = Porteiro::create($dadosMapeados);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Porteiro criado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o porteiro criado
                new PorteiroResurce($novoPorteiro)
            ]
        );

    }

    //Função que pega um usuário do banco ade dados
    public function show(string $id)
    {
        //Recupera o usuário no banco de dados
        $porteiro = Porteiro::findOrFail($id);

        //Retorno o json com os dados do porteiro atualizado
        //Retornando um json de sucuesso
        return $this->responseJson(
            "Porteiro recupedado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o morador criado
                new PorteiroResurce($porteiro)
            ]
        );
    }

    //Função que atualiza o porteiro
    public function update(Request $request, string $id)
    {
        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            "nome_porteiro" => $request->input("nome"),
            "email_porteiro" => $request->input("email"),
            "senha_porteiro" => Hash::make($request->input("password")),
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "nome_porteiro" => 'required|string|max:100',
            "email_porteiro" => 'required|string|max:150',
            "senha_porteiro" => "required",
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

        //Pegando o porteiro do bando de dados pelo id
        $porteiro = Porteiro::findOrFail($id);

        //Atualizando os dados do porteiro
        $atualizado = $porteiro->update($dadosMapeados);

         //Se o update não foi atualizado
        if(!$atualizado){

            //Retornando um responsta Json 
            return $this->responseJson(
                "Não foi possivel realizar a atualização do Porteiro", //Menssagem
                400, //Status code
            );
        }

        //Pegando o porteiro com os novos dados
        $novoPorteiro = Porteiro::findOrFail($id); 

        //Retorno o json com os dados do porteiro atualizado
        //Retornando um json de sucuesso
        return $this->responseJson(
            "Porteiro atualizado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o morador criado
                new PorteiroResurce($novoPorteiro)
            ]
        );
    }

    //Função que remove o porterio
    public function destroy(string $id)
    {
        //Pegando o porteiro do banco de dados pelo Id
        $porteiro = Porteiro::findOrFail($id);

        //Deletando o porteiro
        $deletado = $porteiro->delete();

        //Verificando de o porteiro foi deletado com sucesso
        if(!$deletado){

            //Retorna um Json de erro
            return $this->errorJson(
                "Não foi possivel deletar o porteiro", //Menssagem
                400, //Status code
            );
        }

        //Retornando um json de sucesso
        return $this->responseJson(
            "Porteiro apagado com sucesso!", //Menssagem
            200, //Status code
        );
    }
}
