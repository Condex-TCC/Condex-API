<?php

/*
|--------------------------------------------------------------------------
| EnviaSmsTrait
|--------------------------------------------------------------------------
|
| PARA QUE SERVE ESTE ARQUIVO:
| Trait que fica entre o Service e o Controller. Ela tem uma única função,
| enviarSms(), que faz duas coisas:
|   1) é a interface limpa usada pelo controller;
|   2) trata a requisição: chama o Service e traduz o JSON de retorno
|      do SMS Fácil em um array simples.
|
| Ela NÃO trata o telefone: ele deve chegar pronto (DDD + número, só dígitos).
|
| Local sugerido no projeto: app/Traits/EnviaSmsTrait.php
|
*/

// Namespace da Trait (pasta app/Traits), necessário para o autoload do Laravel.
namespace App\Traits;

// Importa o Service que realiza a requisição ao SMS Fácil.
use App\Http\Service\SmsFacilService;

// Importa a facade Log, para registrar erros em storage/logs/laravel.log.
use Illuminate\Support\Facades\Log;

// Importa Throwable, que representa qualquer erro/exceção do PHP (ex.: falta de conexão).
use Throwable;

/**
 * Trait que oferece o envio de SMS (SMS Fácil) para qualquer controller.
 */
trait EnviaSmsTrait
{
    /**
     * Envia UM SMS para UM telefone e devolve o resultado já traduzido.
     *
     * Retorno (array):
     *   'sucesso'  => bool         true se o SMS entrou na fila de envio (não significa entregue)
     *   'mensagem' => string       texto amigável para exibir
     *   'status'   => int          código HTTP sugerido para o controller responder
     *   'recibo'   => string|null  número do pedido devolvido pelo provedor (só em sucesso)
     *
     * @param  string  $telefone  DDD + número, só dígitos (10 ou 11). Ex.: 11999999999
     * @param  string  $texto     Conteúdo do SMS.
     * @return array
     */
    protected function enviarSms(string $telefone, string $texto): array
    {
        // Tenta chamar a API; qualquer erro de conexão cairá no catch.
        try {

            //Instancia a classe
            $smsFacil = new SmsFacilService();

            // Pede ao Laravel uma instância do Service e envia o SMS; guarda a resposta crua.
            $resposta = $smsFacil->enviarSms($telefone, $texto);

        } catch (Throwable $e) {

            //AQUI TRATA AS FALHAS NO MOMENTO EM QUE SE REALIZA O REQUEST    

            // Registra o erro técnico no log (nunca registre usuário/senha).
            Log::error('SMS Fácil: falha ao chamar a API.', ['erro' => $e->getMessage()]);

            // Devolve 503 (serviço indisponível) com mensagem amigável.
            return ['sucesso' => false, 'mensagem' => 'Não foi possível conectar ao serviço de SMS.', 'status' => 503, 'recibo' => null];
        }

        // Pega o primeiro (e único) item de "resultados" do JSON. Será null se o JSON vier diferente do esperado.
        $item = $resposta->json('resultados.0');

        // Lê o campo "resultado": "OK 1234..." em caso de sucesso, ou um código de erro como "-7".
        $resultado = trim((string) ($item['resultado'] ?? ''));

        // Se o HTTP foi bem-sucedido E o provedor marcou este item como sucesso...
        if ($resposta->successful() && ($item['sucesso'] ?? false) === true) {

            // ...devolve sucesso; o recibo é o texto do resultado sem o prefixo "OK ".
            return ['sucesso' => true, 'mensagem' => 'SMS enviado para a fila de envio.', 'status' => 200, 'recibo' => trim(preg_replace('/^OK\s*/i', '', $resultado))];
        }

        //AQUI SE TRATA OS ERROS NAS VINDOS NA RESPONSE

        // Daqui para baixo houve falha: registra o status HTTP e o código do provedor no log.
        Log::error('SMS Fácil: envio recusado.', ['http' => $resposta->status(), 'codigo' => $resultado]);

        // Traduz o código de erro em [mensagem, status HTTP].
        [$mensagem, $status] = match ($resultado) {
            '-2' => ['Formato do número de celular inválido.', 422],           // Número fora do padrão.
            '-3' => ['O texto da mensagem está vazio ou inválido.', 422],      // Texto rejeitado.
            '-4' => ['Número de celular inválido ou não suportado.', 422],     // Número não atendido.
            default => ['Serviço de SMS indisponível no momento.', 502],       // -5 a -9, HTTP com erro ou resposta inesperada.
        };

        // Devolve a falha no mesmo formato do sucesso.
        return ['sucesso' => false, 'mensagem' => $mensagem, 'status' => $status, 'recibo' => null];
    }
}