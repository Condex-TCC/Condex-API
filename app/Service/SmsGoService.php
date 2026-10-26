<?php

namespace App\Service;

use Orynlabs\SMSGo\Client;
use Orynlabs\SMSGo\SMSGoError;
use RuntimeException;

/*
|--------------------------------------------------------------------------
| VISÃO GERAL DO FLUXO DE ENVIO DE SMS
|--------------------------------------------------------------------------
|
| Rota (POST /api/sms-go/teste)
|    |
|    v
| SmsGoTesteController   -> Recebe a requisição e devolve o JSON de resposta.
|    |                      Não sabe NADA sobre SMS Go, só pede para enviar.
|    v
| Trait EnviaSms         -> "Atendente" do controller. Oferece o método
|    |                      enviarSms() e decide qual service será usado.
|    v
| SmsGoService (ESTE)    -> Único arquivo que conhece o SDK do SMS Go.
|    |                      Prepara o telefone, chama o SDK e padroniza o retorno.
|    v
| SDK orynlabs/smsgo     -> Biblioteca oficial (vem pelo Composer, está em
|                           vendor/). Faz a conversa HTTP com a API do SMS Go.
|
| Regra de ouro: cada camada só conversa com a camada logo abaixo dela.
| Assim, se o SDK mudar, só ESTE arquivo precisa ser alterado.
|
*/
 
//Service que isola o SDK do SMS Go
//Esse é o ÚNICO arquivo do projeto que conhece o SDK (orynlabs/smsgo)
//
//Classes externas usadas aqui (todas vêm do SDK ou do PHP, nenhuma é nossa):
// - Client        -> A "ferramenta" principal do SDK. É ela que sabe enviar SMS, consultar saldo, etc.
// - SMSGoError    -> Tipo de erro (exception) que o SDK lança quando a API recusa ou falha uma chamada.
// - RuntimeException -> Erro genérico do próprio PHP. Usamos para avisar de problema de configuração.
class SmsGoService
{
    //Cliente do SDK
    //É um OBJETO da classe Client. Fica guardado aqui dentro para ser reaproveitado em todos os métodos.
    //"private" significa que só esta classe pode mexer nele (nem a trait, nem o controller)
    private Client $client;

    //Construtor: roda automaticamente no momento em que o service é criado
    //Quem cria o service é o Laravel (através do app() na trait), então ninguém chama este método manualmente
    public function __construct()
    {
        //Pegando a chave que está em config/services.php -> .env (SMSGO_KEY)
        //Caminho da chave: .env (SMSGO_KEY) -> config/services.php ('smsgo' => 'key') -> config('services.smsgo.key')
        $chave = config('services.smsgo.key');

        //Se a chave não foi configurada, avisa logo em vez de quebrar dentro do SDK
        //Isso evita um erro confuso lá no meio da biblioteca e deixa a mensagem clara para quem está testando
        if (empty($chave)) {
            throw new RuntimeException('SMSGO_KEY não configurada no .env');
        }

        //O SDK cuida da autenticação (troca a chave por token e renova sozinho)
        //Aqui nasce o objeto Client, já "logado" com a nossa chave
        //Chave que começa com test_ -> modo teste (não envia de verdade). Qualquer outra -> envio real
        $this->client = new Client($chave);
    }

    //Função que envia um SMS e devolve sempre um array padronizado
    //Entrada: telefone (texto) e mensagem (texto)
    //Saída: array com a chave 'sucesso' (true/false), seja qual for o resultado
    //Importante: esta função NÃO lança erro da API para fora, ela captura e transforma em array.
    //Por isso quem chama (trait/controller) só precisa olhar o campo 'sucesso'
    public function enviar(string $telefone, string $mensagem): array
    {
        //"try" tenta executar o bloco. Se o SDK lançar um SMSGoError, o fluxo pula para o "catch" lá embaixo
        try {
            
            //Chamando o SDK
            //Os nomes antes dos dois pontos (phone:, message:) são "parâmetros nomeados" do PHP 8:
            //servem para deixar claro qual valor vai em qual campo
            //O telefone passa antes pelo normalizarTelefone() para ficar no formato que a API exige
            $resultado = $this->client->send(
                phone: $this->normalizarTelefone($telefone),
                message: $mensagem,
            );

            //O $resultado é um OBJETO de retorno do SDK (um "DTO": uma caixinha só de dados, somente leitura)
            //Ele tem propriedades como id, status e test, que lemos abaixo com a seta (->)

            //Sucesso
            //Montamos o NOSSO array a partir do objeto do SDK, assim o resto do projeto não depende do formato do SDK
            return [
                'sucesso' => true,
                'id' => $resultado->id, //Id do envio na SMSGo
                'status' => $resultado->status, //Ex: "queued"
                'teste' => (bool) $resultado->test, //true quando usada a chave test_
            ];
        } catch (SMSGoError $e) {

            //Aqui só cai quem for do tipo SMSGoError
            //A variável $e é o OBJETO do erro e carrega as informações do que deu errado
 
            //Falha vinda da API ou de rede (status 0 = rede)
            //Codes comuns: insufficient_balance, validation_error, rate_limited, unauthorized, network_error
            //Resumo dos campos do objeto de erro:
            // - $e->code      -> texto fixo que identifica o tipo do erro (ex: insufficient_balance = sem saldo)
            // - $e->status    -> código HTTP devolvido pela API (ex: 402, 422). Vale 0 quando nem chegou na API (falha de rede)
            // - $e->getMessage() -> frase explicando o erro
            // - $e->errors    -> lista de erros campo a campo (só existe em erro de validação, o 422)
            return [
                'sucesso' => false,
                'codigo' => $e->code,
                'status_http' => $e->status,
                'mensagem' => $e->getMessage(),
                'erros' => $e->errors ?? [], //Detalhe por campo em caso de 422
                //O "?? []" significa: se $e->errors não existir ou for nulo, usa uma lista vazia no lugar
            ];
        }
    }

    //Deixa o telefone no formato +5511999990000 que o SDK espera
    //É "private": é um detalhe interno do service. Só o método enviar() usa
    //Exemplo: '(19) 99418-4243' vira '+5519994184243'
    private function normalizarTelefone(string $telefone): string
    {
        //Remove tudo que não é número
        //'\D' significa "qualquer caractere que NÃO seja dígito" (parênteses, espaço, traço, etc.)
        $digitos = preg_replace('/\D/', '', $telefone);

        //Número com DDD, mas sem o 55 (10 ou 11 dígitos) -> adiciona o 55
        //55 é o código do Brasil. Se o número já veio com 55 na frente, ele terá 12 ou 13 dígitos e esta linha é ignorada
        if (strlen($digitos) <= 11) {
            $digitos = '55' . $digitos;
        }

        //O '.' no PHP junta textos. Aqui coloca o '+' na frente, como a API exige
        return '+' . $digitos;
    }
}