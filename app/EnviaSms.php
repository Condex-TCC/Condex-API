<?php

namespace App;

use App\Service\SmsGoService;

/*
|--------------------------------------------------------------------------
| O QUE É ESTE ARQUIVO (TRAIT)
|--------------------------------------------------------------------------
|
| Uma TRAIT é um "pacote de métodos" que pode ser colado dentro de qualquer
| classe com a palavra "use". Não dá para criar um objeto direto de uma trait:
| ela só existe para ser usada por outras classes (aqui, os controllers).
|
| Este é o elo do meio do fluxo:
|
|   Controller  --(use EnviaSms)-->  Trait  --(app())-->  SmsGoService  -->  SDK
|
| Por que ela existe? Para o controller não precisar saber qual service de
| SMS está por trás. O controller só chama enviarSms(). Se um dia trocarmos
| o SMS Go por outro gateway, mexemos APENAS no método servicoSms() daqui
| e nenhum controller precisa ser alterado.
|
*/
 
//Trait que oferece uma interface simples para os controllers enviarem SMS
//Para trocar de gateway no futuro, basta mudar o service retornado em servicoSms()
//
//Como usar em qualquer controller:
// 1) Importar:  use App\EnviaSms;
// 2) Dentro da classe:  use EnviaSms;
// 3) Chamar:  $this->enviarSms('19999999999', 'Texto da mensagem');
trait EnviaSms
{
    //Único ponto que sabe qual service está sendo usado
    //"protected" significa: pode ser usado pela classe que adotou a trait (e suas filhas), mas NÃO pode ser chamado de fora
    //Ou seja, não dá para fazer $controller->servicoSms() em outro lugar do sistema
    //O ": SmsGoService" no final diz o que esta função devolve: um objeto da classe SmsGoService
    protected function servicoSms(): SmsGoService
    {
        //O container do Laravel instancia o service automaticamente
        //app(Classe::class) é um atalho do Laravel que significa: "me entregue um objeto pronto desta classe"
        //Ele cria o SmsGoService (rodando o construtor dele, que lê a chave e monta o Client do SDK) e devolve para nós
        //Assim a trait nunca precisa escrever "new SmsGoService()" nem se preocupar com a chave
        return app(SmsGoService::class);
    }

    //Função que o controller chama para enviar um SMS
    //Retorna um array com a chave 'sucesso' (true/false)
    //Este é o ÚNICO método que o controller precisa conhecer
    //Formato do array devolvido (vem pronto do service):
    // - Deu certo:  ['sucesso' => true,  'id' => ..., 'status' => ..., 'teste' => ...]
    // - Deu errado: ['sucesso' => false, 'codigo' => ..., 'status_http' => ..., 'mensagem' => ..., 'erros' => ...]
    protected function enviarSms(string $telefone, string $mensagem): array
    {
        //A trait não faz nenhuma lógica própria: ela só repassa o pedido ao service e devolve a resposta
        //$this aqui é o próprio controller que está usando a trait
        return $this->servicoSms()->enviar($telefone, $mensagem);
    }
}