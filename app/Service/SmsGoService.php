<?php

namespace App\Service;

use Orynlabs\SMSGo\Client;
use Orynlabs\SMSGo\SMSGoError;
use RuntimeException;

//Service que isola o SDK do SMS Go
//Esse é o ÚNICO arquivo do projeto que conhece o SDK (orynlabs/smsgo)
class SmsGoService
{
    //Cliente do SDK
    private Client $client;

    public function __construct()
    {
        //Pegando a chave que está em config/services.php -> .env (SMSGO_KEY)
        $chave = config('services.smsgo.key');

        //Se a chave não foi configurada, avisa logo em vez de quebrar dentro do SDK
        if (empty($chave)) {
            throw new RuntimeException('SMSGO_KEY não configurada no .env');
        }

        //O SDK cuida da autenticação (troca a chave por token e renova sozinho)
        $this->client = new Client($chave);
    }

    //Função que envia um SMS e devolve sempre um array padronizado
    public function enviar(string $telefone, string $mensagem): array
    {
        try {
            //Chamando o SDK
            $resultado = $this->client->send(
                phone: $this->normalizarTelefone($telefone),
                message: $mensagem,
            );

            //Sucesso
            return [
                'sucesso' => true,
                'id' => $resultado->id, //Id do envio na SMSGo
                'status' => $resultado->status, //Ex: "queued"
                'teste' => (bool) $resultado->test, //true quando usada a chave test_
            ];
        } catch (SMSGoError $e) {

            //Falha vinda da API ou de rede (status 0 = rede)
            //Codes comuns: insufficient_balance, validation_error, rate_limited, unauthorized, network_error
            return [
                'sucesso' => false,
                'codigo' => $e->code,
                'status_http' => $e->status,
                'mensagem' => $e->getMessage(),
                'erros' => $e->errors ?? [], //Detalhe por campo em caso de 422
            ];
        }
    }

    //Deixa o telefone no formato +5511999990000 que o SDK espera
    private function normalizarTelefone(string $telefone): string
    {
        //Remove tudo que não é número
        $digitos = preg_replace('/\D/', '', $telefone);

        //Número com DDD, mas sem o 55 (10 ou 11 dígitos) -> adiciona o 55
        if (strlen($digitos) <= 11) {
            $digitos = '55' . $digitos;
        }

        return '+' . $digitos;
    }
}