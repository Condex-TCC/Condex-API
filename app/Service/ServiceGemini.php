<?php

namespace App\Service;

use Gemini;
use Gemini\Data\Content;
use Gemini\Data\GenerationConfig;
use Gemini\Enums\ResponseMimeType;
use Illuminate\Support\Facades\Log;

//Classe reposável fazer as requisições para o Gemini
class ServiceGemini
{
    //Atributo que armazena os modelos do gemini que vão ser utilizados
    private const modelos = [
        "gemini-3.5-flash-lite",
        "gemini-3.1-flash-lite"
    ];

    //Atributo que representa a chave do modelo
    private $client;

    //Atributo que representa as configurações do modelo
    private GenerationConfig $config;

    private const systemInstrucao = "
    Você é um assistente de gestão condominial que analisa dados em formato JSON (últimas reclamações,
    últimas reservas e quantidade de perguntas sobre o último comunicado) para gerar um relatório executivo para o síndico.

    REGRAS OBRIGATÓRIAS DE FORMATO E TAMANHO:
    1. IDIOMA: Responda obrigatoriamente em português do Brasil.
    2. ESTRUTURA: O texto DEVE conter exatamente 2 parágrafos separados por uma quebra de linha:
        - Parágrafo 1: Resumo claro das reclamações, reservas e dúvidas sobre o comunicado recebidas nas últimas 24 horas.
        - Parágrafo 2: Ideias práticas e sugestões de melhoria para a gestão do síndico com base nas ocorrências relatadas.
    3. LIMITE: O texto total (soma dos dois parágrafos) DEVE ter no máximo 800 caracteres (incluindo espaços).

    REGRAS DE CONTEÚDO:
        - Público-alvo: Leitura exclusiva do síndico. Mantenha um tom executivo, prático e analítico.
        - Fidelidade: Baseie a síntese estritamente nos dados de entrada fornecidos no JSON. Não invente fatos ou dados.";
    
    //Método construtor da classe
    public function __construct()
    {
        //Cria o cliente autenticado com a chave do .env (via config/services.php)
        $this->client = Gemini::client(config('services.gemini.key'));

        //Configura como o modelo deve se comportar
        $this->config = new GenerationConfig(
            temperature: 0.7, //Define o nivel de criatividade
            topP: 0.9, //Definie a vocabulário de palavras
            responseMimeType: ResponseMimeType::TEXT_PLAIN, //Define o tipo de resposta
        );
    }

    //Metodo que realiza a consulta na IA
    public function generate(string $dados){

        //Faz um loop de repetição para os modelos de IA | Fallback
        foreach(self::modelos as $modelo){

            //Seleciona o modelo e anexa a configuração + instrução de sistema
            $chamada = $this->client->generativeModel($modelo) //Passando o modelo de IA
                ->withGenerationConfig($this->config) //Passando as configurações
                ->withSystemInstruction(Content::parse(self::systemInstrucao)); //Passando a instrução

            //Realiza a tentaiva no modelo
            try{

                //Chamada à API | Passando os dados 
                $resposta = $chamada->generateContent($dados);

                //Retorna o texto gerado pela IA
                return trim($resposta->text());

            }
            catch(\Throwable $e){ //Caso ocorra algum erro

                //Salva no log o erro que retornou
                Log::error("Falha ao usar o modelo {$modelo}", [
                    'modelo'    => $modelo,
                    'mensagem'  => $e->getMessage(),
                    'exception' => $e,
                ]);
            }


        }

        //Retorna um texto em caso de falha do loop
        return "Não foi possível gerar um resumo hoje. Tente mais tarde.";
    }
}