<?php

namespace App\Http\Controllers;

use App\Http\Resources\MoradorResurce;
use App\Http\Resources\PorteiroResurce;
use App\Http\Resources\SindicoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\HttpResposta;
use App\Models\Morador;
use App\Models\Porteiro;
use App\Models\Sindico;
use League\CommonMark\Delimiter\Bracket;
use Illuminate\Support\Facades\Validator;

//Classe responsavel por intermediar as requisições do usuário
class LoginController extends Controller
{
    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    //Função que realiza o login
    public function login(Request $request){

        //Variável que irá armazenar o tipo de usuário
        $tipoUsuario = "";

        //Variável que irá guardar o token
        $token = "";

        //Variável que irá garudar se já realizou o primeiro login
        $primerioLogin = false;

        //Pegando os dados do corpo da requisição
        $email = $request->input("email");
        $senha = $request->input("password");

        //Realiza a verificação para sindico
        if(Auth::guard('sindico')->attempt(['email_sindico' => $email, 'password' => $senha])){

            //Defindo o usuário como sindico
            $tipoUsuario = "Sindico";

            //Pegando o sindico logado
            $sindico = $request->user('sindico');

            //Pegando se esse usuário já realizou o primeiro login
            $primerioLogin = $sindico->primeiro_acesso_sindico;

            //Criando um token para o sindico que logou no sistema
            //O nome do token é sindico | As habilidades passadas são de sindico
            $token = $sindico->createToken("sindico", ["sindico"])->plainTextToken;
        }

        //Realiza a verificação para morador
        if(Auth::guard('morador')->attempt(['email_morador' => $email, 'password' => $senha])){

            //Defindo o usuário como morador
            $tipoUsuario = "Morador";

            //Pegando o morador logado
            $morador = $request->user('morador');

            //Pegando se esse usuário já realizou o primeiro login
            $primerioLogin = $morador->primeiro_acesso_morador;

            //Criando um token para o morador que logou no sistema
            //O nome do token é morador | As habilidades passadas são de morador
            $token = $morador->createToken("morador", ["morador"])->plainTextToken;
        }

        //Realiza a verificação para porteiro
        if(Auth::guard('porteiro')->attempt(['email_porteiro' => $email, 'password' => $senha])){

            //Defindo o usuário como porteiro
            $tipoUsuario = "Porteiro";

            //Pegando o porteiro logado
            $porteiro = $request->user('porteiro');

            //Pegando se esse usuário já realizou o primeiro login
            $primerioLogin = $porteiro->primeiro_acesso_porteiro;

            //Criando um token para o porteiro que logou no sistema
            //O nome do token é porteiro | As habilidades passadas são de porteiro
            $token = $porteiro->createToken("porteiro", ["porteiro"])->plainTextToken;
        }

        //Verifica se não foi possivel fazer a autenticação
        if($tipoUsuario == "" && $token == ""){

            //Retorna um json de erro
            return $this->errorJson(
                "Não foi possivel fazer o login", //Menssagem de erro
                400, //Status code
                ["O E-mail ou a senha do estão incorretos!"], //Erros
            );
        }

        //Retorna o token e o tipo de usuario
        return $this->responseJson(
            "Sucesso! Usuário logado com sucesso", //Menssagem
            200, //Status
            //Array com as menssagems
            [
                "tipo_usuario" => $tipoUsuario, //Realiza a passagem do tipo de usuário
                "token" => $token, //Realiza a passagem do token
                "primeiro_acesso" => $primerioLogin === 1 ? true : false, //Realiza a passagen se o usuário já realizou o primeiro login
            ]
        );
    }

    //Função que realiza o logout
    public function logout(Request $request){
        
        //Pega o usuário logado | O usuário fica disponivel por causa do token, que ao passar pelo
        //atun:sanctum ele pega o usuário e ingete na requisição
        $usuario = $request->user();

        //Apagando o token do usuário logado | E deleta o usuário logado
        $usuario->currentAccessToken()->delete();

        //Retorna uma resposta Json
        return $this->responseJson(
            "Usuário deslogado com sucesso", //Menssagem
            200, //Status code
        );
    }
}
