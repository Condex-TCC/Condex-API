<?php

/*
|--------------------------------------------------------------------------
| SmsFacilService
|--------------------------------------------------------------------------
|
| PARA QUE SERVE ESTE ARQUIVO:
| Esta é a camada de SERVICE da integração com o SMS Fácil.
| A única responsabilidade dela é REALIZAR A REQUISIÇÃO HTTP para a API
| do provedor. Ela NÃO valida telefone, NÃO interpreta códigos de erro e
| NÃO monta mensagens para o usuário: isso é trabalho da Trait
| (App\Traits\EnviaSmsTrait). O Service apenas envia e devolve a resposta crua.
|
| Local sugerido no projeto: app/Services/SmsFacilService.php
|
*/

// Declara o "endereço" (namespace) da classe. Corresponde à pasta app/Services
// e permite que o Laravel encontre a classe pelo autoload (PSR-4).
namespace App\Http\Service;

// Importa a classe Response do cliente HTTP do Laravel. É o tipo do objeto
// que representa a resposta devolvida pela API (status, corpo, JSON etc.).
use Illuminate\Http\Client\Response;

// Importa a facade Http, que é o cliente HTTP do Laravel (baseado no Guzzle),
// usado para fazer a requisição POST ao SMS Fácil.
use Illuminate\Support\Facades\Http;

/**
 * Classe responsável exclusivamente por chamar a API do SMS Fácil.
 */
class SmsFacilService
{
    /**
     * URL do endpoint de envio de SMS, conforme a documentação do SMS Fácil.
     * É uma constante porque o valor nunca muda durante a execução.
     * "private" = só esta classe usa; "const" = valor fixo.
     */
    private const URL_ENVIO = 'https://www.smsfacil.com.br/api/v2/EnviaSMS';

    /**
     * Tempo máximo (em segundos) que esperamos pela resposta da API.
     * Evita que a sua API Laravel fique "travada" se o provedor demorar.
     */
    private const TIMEOUT_SEGUNDOS = 15;

    /**
     * Guarda o usuário (login) do SMS Fácil, lido do config/services.php.
     */
    private string $usuario;

    /**
     * Guarda a senha do SMS Fácil, lida do config/services.php.
     */
    private string $senha;

    /**
     * CONSTRUTOR
     * Executado automaticamente quando a classe é instanciada (por exemplo,
     * quando a Trait faz app(SmsFacilService::class)).
     * Serve para carregar as credenciais uma única vez.
     */
    public function __construct()
    {
        // Lê 'smsFacil.user' do arquivo config/services.php, que por sua vez
        // vem da variável SMS_FACIL_USER do .env. O "(string)" garante que,
        // se a variável não existir (null), vira texto vazio e não gera erro de tipo.
        $this->usuario = (string) config('services.smsFacil.user');

        // Mesma ideia para a senha: config/services.php -> SMS_FACIL_PASSWORD no .env.
        $this->senha = (string) config('services.smsFacil.password');
    }

    /**
     * Envia UM SMS para UM único telefone.
     *
     * Esta função só monta e dispara a requisição. Ela espera que o telefone
     * já venha validado/limpo (somente dígitos, DDD + número) pela Trait.
     *
     * @param  string  $telefone  Telefone com DDD, só dígitos (10 ou 11 dígitos). Ex.: 11999999999
     * @param  string  $texto     Conteúdo da mensagem de SMS.
     * @return Response           Resposta crua da API (a Trait é quem interpreta).
     *
     * @throws \Illuminate\Http\Client\ConnectionException Se não conseguir conectar (rede fora, timeout).
     */
    public function enviarSms(string $telefone, string $texto): Response
    {
        // Inicia o retorno: devolve direto o resultado da requisição para quem chamou.
        // Http:: abre o cliente HTTP do Laravel.
        return Http::acceptJson()                // Cabeçalho "Accept: application/json": pedimos resposta em JSON.
            ->asJson()                           // Envia o corpo como JSON e define "Content-Type: application/json" (exigido pela doc).
            ->timeout(self::TIMEOUT_SEGUNDOS)    // Define o tempo limite da requisição (15s).
            ->post(self::URL_ENVIO, [            // Faz um POST HTTPS no endpoint /api/v2/EnviaSMS com o corpo abaixo.
                'usuario' => $this->usuario,     // Credencial de usuário vai NO CORPO, nunca na URL (recomendação da doc).
                'senha' => $this->senha,         // Credencial de senha, também no corpo.
                'mensagens' => [                 // A API espera uma LISTA de mensagens (1 a 1.000 itens).
                    [                            // Abre o único item da lista: aqui sempre enviamos só UMA mensagem.
                        'para' => $telefone,     // Destino: DDD + número, 10 ou 11 dígitos.
                        'texto' => $texto,       // Texto do SMS. Sem "data" e "hora" => envio imediato.
                    ],                           // Fecha o item da mensagem.
                ],                               // Fecha a lista de mensagens.
            ]);                                  // Fecha o corpo e dispara a requisição; o retorno é um objeto Response.
    }
}