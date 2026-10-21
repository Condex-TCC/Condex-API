<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\MoradorResurce;
use App\Http\Resources\PorteiroResurce;
use App\Http\Resources\SindicoResource;
use App\HttpResposta;
use App\Models\Morador;
use App\Models\Porteiro;
use App\Models\Sindico;
use Illuminate\Support\Facades\Validator;

class PerfilUsuarioController extends Controller
{
    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    //Função que atualiza o perfil
    public function updatePerfil(Request $request){

        //Pegando o usuário que realizou a atualização nos dados do perfil
        $tipoUsuario = $request->input("usuario");

        //Pegando o array com os dados do usuário
        $dadosUsuario = $request->input("dados");

        //Array que irá armazenar todos os dados dos usuários
        $dadosMapeados = [];

        //Verifica o nivel de acesso para realizar a atualização dos dados
        switch($tipoUsuario){
            //Sindico
            case "Sindico":
                
                //Pegando a pk do sindico
                $pkSindico = $request->user()->pk_id_sindico;
                
                //Pegando os dados do sindico
                $dadosMapeados = [
                    "nome_sindico" => $dadosUsuario['nome'],
                    "telefone_sindico" => $dadosUsuario['telefone'],
                    "email_sindico" => $dadosUsuario['email'],
                    "cpf_sindico" => $dadosUsuario["cpf"],

                    //Altera para validar o primeiro acesso
                    "primeiro_acesso_sindico" => true,
                ];
                //Validando o array mapeado
                $validator = Validator::make($dadosMapeados, [
                    "nome_sindico" => 'required|string',
                    "email_sindico" => 'required|string',
                    "telefone_sindico" => 'required|numeric',
                    "cpf_sindico" => 'required'
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

                //Recupera o registro do sindico
                $sindico = Sindico::find($pkSindico);

                //Atualizando o registro do sindico
                $sindicoAtualizado = $sindico->update($dadosMapeados);

                //Retorna o registro do sindico em json
                return $this->responseJson(
                    "Perfil do sindico Atualizado com sucesso!", //Menssagem
                    200, //Status code
                    //Dados
                    [
                        "sindico" => new SindicoResource(Sindico::find($pkSindico))
                    ]
                );

                break;
            
            //Morador
            case "Morador":

                //Pegando a pk do morador
                $pkMorador = $request->user()->pk_id_morador;

                //Pegando os dados do sindico
                $dadosMapeados = [
                    "nome_morador" => $dadosUsuario['nome'],
                    "cpf_morador" => $dadosUsuario['cpf'],
                    "email_morador" => $dadosUsuario['email'],
                    "telefone_morador" => $dadosUsuario['telefone'],

                    //Altera para validar o primeiro acesso
                    "primeiro_acesso_morador" => true,
                ];

                //Validando o array mapeado
                $validator = Validator::make($dadosMapeados, [
                    "nome_morador" => "required|string",
                    "cpf_morador" => "required|string",
                    "email_morador" => "required|string",
                    "telefone_morador" => "required|numeric",
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

                //Recupera o registro do morador logado
                $morador = Morador::find($pkMorador);

                //Atualizando o registro do morador
                $moradorAtualizado = $morador->update($dadosMapeados);

                //Retorna o registro do sindico em json
                return $this->responseJson(
                    "Perfil do moraodor Atualizado com sucesso!", //Menssagem
                    200, //Status code
                    //Dados
                    [
                        "morador" => new MoradorResurce(Morador::find($pkMorador))
                    ]
                );

                break;

            //Porteiro
            case  "Porteiro":

                //Pegando a pk do porteiro
                $pkPorteiro = $request->user()->pk_id_porteiro;

                //Pegando os dados do porteiro
                $dadosMapeados = [
                    "nome_porteiro" => $dadosUsuario['nome'],
                    "email_porteiro" => $dadosUsuario['email'],
                    "telefone_porteiro" => $dadosUsuario['telefone'],
                    "cpf_porteiro" => $dadosUsuario['cpf'],

                    //Altera para validar o primeiro acesso
                    "primeiro_acesso_porteiro" => true,
                ];

                //Validando o array mapeado
                $validator = Validator::make($dadosMapeados, [
                    "nome_porteiro" => "required|string",
                    "email_porteiro"  => "required|string",
                    "telefone_porteiro" => "required|string",
                    "cpf_porteiro" => "required|string",
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

                //Recupera o registro do porteiro logado
                $porteiro = Porteiro::find($pkPorteiro);

                //Atualizando o registro do porteiro
                $porteiroAtualizado = $porteiro->update($dadosMapeados);

                //Retorna o registro do porteiro em json
                return $this->responseJson(
                    "Perfil porteiro Atualizado com sucesso!", //Menssagem
                    200, //Status code
                    //Dados
                    [
                        "porteiro" => new PorteiroResurce(Porteiro::find($pkPorteiro))
                    ]
                );

                break;
            
            //Caso todos falhem
            default:

                //Retorna um json com formtação padrão para erro
                return $this->errorJson(
                    "Não foi encontrado o usuário para alterar! Digite Sindico, Morador ou Porteiro",
                    400,
                );

                break;
        }
    }

    //Função que recupera os dados do usuário logado
    public function showUser(Request $request, string $tipoUser){

        //Vericando o tipo se usuário para realizar a busca
        switch($tipoUser){
            //Sindico
            case "Sindico":

                //Pegando a pk
                $pkSindico = $request->user()->pk_id_sindico;
                
                //Buscando os dados do sindico
                $sindico = Sindico::find($pkSindico);

                //Retorna o registro do sindico em json
                return $this->responseJson(
                    "Sindico recuperado com sucesso!", //Menssagem
                    200, //Status code
                    //Dados
                    [
                        "usuario" => new SindicoResource($sindico)
                    ]
                );

                break;

            //Morador
            case "Morador":

                //Pegando a pk
                $pkMorador = $request->user()->pk_id_morador;
                
                //Buscando os dados do morador
                $morador = Morador::find($pkMorador);

                //Retorna o registro do moraodor em json
                return $this->responseJson(
                    "Morador recuperado com sucesso!", //Menssagem
                    200, //Status code
                    //Dados
                    [
                        "usuario" => new MoradorResurce($morador)
                    ]
                );

                break;

            //Porteiro
            case "Porteiro":

                //Pegando a pk
                $pkPorteiro = $request->user()->pk_id_porteiro;
                
                //Buscando os dados do porteiro
                $porteiro = Porteiro::find($pkPorteiro);

                //Retorna o registro do porteiro em json
                return $this->responseJson(
                    "Portiero recuperado com sucesso!", //Menssagem
                    200, //Status code
                    //Dados
                    [
                        "usuario" => new PorteiroResurce($porteiro)
                    ]
                );

                break;

            //Caso todos falhem
            default:

                //Retorna um json com formtação padrão para erro
                return $this->errorJson(
                    "Não foi encontrado o usuário para alterar! Digite Sindico, Morador ou Porteiro",
                    400,
                );

                break;
        }
    }
}
