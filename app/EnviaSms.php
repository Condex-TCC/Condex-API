<?php

namespace App;

use App\Service\SmsGoService;

//Trait que oferece uma interface simples para os controllers enviarem SMS
//Para trocar de gateway no futuro, basta mudar o service retornado em servicoSms()
trait EnviaSms
{
    //Único ponto que sabe qual service está sendo usado
    protected function servicoSms(): SmsGoService
    {
        //O container do Laravel instancia o service automaticamente
        return app(SmsGoService::class);
    }

    //Função que o controller chama para enviar um SMS
    //Retorna um array com a chave 'sucesso' (true/false)
    protected function enviarSms(string $telefone, string $mensagem): array
    {
        return $this->servicoSms()->enviar($telefone, $mensagem);
    }
}