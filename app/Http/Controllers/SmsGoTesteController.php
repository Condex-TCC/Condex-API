<?php

namespace App\Http\Controllers;

use App\HttpResposta;
use App\EnviaSms;

//Controller TEMPORÁRIO apenas para testar o envio de SMS via SMS Go
//Usa telefone e mensagem estáticos, não consulta o banco
class SmsGoTesteController extends Controller
{
    //Adiciona a trait com as respostas padrões para APIs
    use HttpResposta;

    //Adiciona a trait que oferece o envio de SMS
    use EnviaSms;

    //Telefone estático do teste | TROQUE pelo seu número (com DDD)
    private const TELEFONE_TESTE = '199991234';

    //Mensagem estática do teste
    private const MENSAGEM_TESTE = 'Condex, o TCC que não vai capengar!';

    //Função que dispara o SMS de teste
    public function enviarTeste()
    {
        //Chamando a trait
        $resultado = $this->enviarSms(self::TELEFONE_TESTE, self::MENSAGEM_TESTE);

        //Se o envio falhou
        if (!$resultado['sucesso']) {

            //Retorna um Json com formatação de erro
            return $this->errorJson(
                "Não foi possível enviar o SMS!", //Menssagem
                //Status 0 (falha de rede) vira 502, o resto mantém o status da API
                $resultado['status_http'] ?: 502, //Status code
                [$resultado['codigo'], $resultado['mensagem']], //Erros
            );
        }

        //Retornando um json de sucesso
        return $this->responseJson(
            "SMS enviado com sucesso!", //Menssagem
            200, //Status code
            [
                'id' => $resultado['id'],
                'status' => $resultado['status'],
                'teste' => $resultado['teste'],
            ]
        );
    }
}