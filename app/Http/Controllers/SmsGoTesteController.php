<?php

namespace App\Http\Controllers;

use App\HttpResposta;
use App\EnviaSms;

/*
|--------------------------------------------------------------------------
| O QUE É ESTE ARQUIVO (CONTROLLER)
|--------------------------------------------------------------------------
|
| O controller é a PORTA DE ENTRADA da requisição. Ele recebe o pedido que
| chegou pela rota, pede o trabalho para as camadas de baixo e devolve o
| JSON de resposta. Ele não sabe como o SMS é enviado, só pede e trata o
| resultado.
|
| Fluxo completo deste teste:
|
|  1. A rota POST /api/sms-go/teste chama o método enviarTeste() deste controller
|  2. enviarTeste() chama $this->enviarSms()          (método da trait EnviaSms)
|  3. A trait chama o SmsGoService->enviar()          (service que fala com o SDK)
|  4. O service devolve um array com 'sucesso' true/false
|  5. De volta aqui, o controller monta o JSON:
|       - sucesso -> responseJson() (método da trait HttpResposta)
|       - falha   -> errorJson()    (método da trait HttpResposta)
|
| Origem de cada método usado aqui:
|  - enviarSms()    -> vem da trait EnviaSms
|  - responseJson() -> vem da trait HttpResposta
|  - errorJson()    -> vem da trait HttpResposta
|
*/
 
//Controller TEMPORÁRIO apenas para testar o envio de SMS via SMS Go
//Usa telefone e mensagem estáticos, não consulta o banco
//"extends Controller" faz esta classe herdar a classe base de controllers do Laravel
class SmsGoTesteController extends Controller
{
    //Adiciona a trait com as respostas padrões para APIs
    //Dá acesso aos métodos responseJson() e errorJson(), que padronizam o JSON de sucesso e de erro
    use HttpResposta;

    //Adiciona a trait que oferece o envio de SMS
    //Dá acesso ao método enviarSms(). É só isso que o controller precisa saber sobre SMS
    use EnviaSms;

    //Telefone estático do teste | TROQUE pelo seu número (com DDD)
    //"const" é um valor fixo que não muda durante a execução. "private" = só esta classe enxerga
    //Para ler uma constante dentro da própria classe usamos self::NOME
    private const TELEFONE_TESTE = '199991234';

    //Mensagem estática do teste
    private const MENSAGEM_TESTE = 'Condex, o TCC que não vai capengar!';

    //Função que dispara o SMS de teste
    //É o método que a rota chama. Não recebe parâmetros porque o telefone e a mensagem são fixos (constantes acima)
    public function enviarTeste()
    {
        
        //Chamando a trait
        //$resultado recebe o array padronizado do service (ver formato no arquivo EnviaSms)
        $resultado = $this->enviarSms(self::TELEFONE_TESTE, self::MENSAGEM_TESTE);

        //Se o envio falhou
        //O "!" inverte o valor: lê-se "se NÃO deu sucesso"
        if (!$resultado['sucesso']) {

            //Retorna um Json com formatação de erro
            //errorJson() recebe: mensagem, status HTTP, lista de erros (e opcionalmente dados)
            return $this->errorJson(
                "Não foi possível enviar o SMS!", //Menssagem
                //Status 0 (falha de rede) vira 502, o resto mantém o status da API
                //O "?:" é um atalho: se o status_http for 0 (considerado "vazio"), usa 502; senão usa o próprio status_http
                $resultado['status_http'] ?: 502, //Status code
                [$resultado['codigo'], $resultado['mensagem']], //Erros
            );
        }

        //Retornando um json de sucesso
        //Só chega aqui se o "if" de cima não foi verdadeiro, ou seja, o envio foi aceito pela SMS Go
        return $this->responseJson(
            "SMS enviado com sucesso!", //Menssagem
            200, //Status code
            [
                'id' => $resultado['id'], //Id do envio na SMS Go (serve para consultar o status depois)
                'status' => $resultado['status'], //Situação do envio. "queued" = na fila para ser enviado
                'teste' => $resultado['teste'], //true = foi usada a chave de teste (SMS não enviado de verdade)
            ]
        );
    }
}