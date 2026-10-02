<?php

namespace App\Http\Controllers;

use App\Http\Resources\ComunicadoResources;
use App\Http\Resources\EnvioResources;
use App\Http\Resources\MoradorResurce;
use App\Models\Comunicado;
use App\Models\Envio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\HttpResposta;
use App\Models\Morador;

class EnvioController extends Controller
{
    use HttpResposta;

    //Função do sindico que cadastra as comunicados e os registros
    public function store(Request $request)
    {
       //Pegando o id do sindico
       $pkSindico = $request->user()->pk_id_sindico;

       //Pegando os dados dos da request
       $dadosMapeadosComunicado = [
            'titulo_comunicado' => $request->input("titulo"),
            'descricao_comunicado' => $request->input('descricao'),
            'fk_id_sindico_comunicados' => $pkSindico
       ];

       //Pegando os moradores selecionados
       $arrayDeMoradoresSelecionados = $request->input("moradores_selecionados");

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeadosComunicado, [
            'titulo_comunicado' => "required|string",
            'descricao_comunicado' => "required|string"
        ]);

        //Caso os dados não passasem na validação
        if($validator->fails()){

            //Retorna um Json com fotmatação de erro
            return $this->errorJson(
                "Os dados passados não estão corretos!", //Menssagem
                400, //Status code
                //Passando os erros
                [
                    //Pegando o array de erros dados pelo validator
                    $validator->errors()
                ]
            );
        }

        //Cadastrando comunicado
        $comunicado = Comunicado::create($dadosMapeadosComunicado);

        //Recuperando o Pk do comunicado
        $pkComunicado = $comunicado->pk_id_comunicados;

        //Verifica se é para moradores especificos ou para todos
        if($arrayDeMoradoresSelecionados == null){

            //Recupera todas as Pk dos moradoes
            $arrayDeTodosMoradores = Morador::all();

            //Fazendo o cadastro dos usuários
            foreach($arrayDeTodosMoradores as $morador){

                //Mapeando os dados para o cadastro
                $dadosMapeadosEnvios = [
                    'fk_id_comunicados' => $pkComunicado,
                    'fk_id_morador' => $morador->pk_id_morador,
                    'resposta' => null,
                    'contra_resposta' => null,
                    'visualizado' => false,
                ];

                //Cadastrando o registro
                $cadastro = Envio::create($dadosMapeadosEnvios);
            }

            //Retona um Json de sucesso com formatação padrão
            return $this->responseJson(
                "O Comunicado foi enviado com sucesso para todos os moradores",
                200,
                [
                    "Comunicado" => new ComunicadoResources($comunicado),
                    'moradores_enviados' => MoradorResurce::collection($arrayDeTodosMoradores)
                ]
            );

        }else{

            //Fazendo o cadastro dos usuários
            foreach($arrayDeMoradoresSelecionados as $idmorador){

                //Mapeando os dados para o cadastro
                $dadosMapeadosEnvios = [
                    'fk_id_comunicados' => $pkComunicado,
                    'fk_id_morador' => $idmorador,
                    'resposta' => null,
                    'contra_resposta' => null,
                    'visualizado' => false,
                ];

                //Cadastrando o registro
                $cadastro = Envio::create($dadosMapeadosEnvios);
            }

            //Retona um Json de sucesso com formatação padrão
            return $this->responseJson(
                "O comunicado foi enviado para os moradores selecionados",
                200,
                 [
                    "Comunicado" => new ComunicadoResources($comunicado),
                    'moradores_enviados' => MoradorResurce::collection(Morador::find($arrayDeMoradoresSelecionados))
                ]
            );

        }
    }

    //Função que recupera todos os envios com o comunicado
    public function showEnvios(string $id){
            
        //Recupera o comunicado
        $comunicado = Comunicado::find($id);

        //Recuperando os envios desse comunicado
        $envios = Envio::where('fk_id_comunicados', $id)->get();

        //Aplicando formatação comum os envios
        $enviosTratados = EnvioResources::collection($envios);

        //Retorando o json com formatação padrão para sucesso
        return $this->responseJson(
            "Envios recuperados com sucesso!", //Menssagem
            200, //Status
            //Dados
            [
                'comunicado' => new ComunicadoResources($comunicado),
                "envios_moradores" => $enviosTratados
            ]
        );
    }

    //TODO: Conferir depois
    //Função que recupera os registros que estão com resposta mas sem contra resposta
    public function showSemResposta(){
        
        //Pegando os envios com resposta e sem contra resposta
        $perguntas = $perguntas = Envio::whereNotNull('resposta')->whereNull('contra_resposta')->get();

        //Retornando formatação com sucesso
        return $this->responseJson(
            "Duvidas recuperadas com sucesso!", //Menssagem
            200, //Status
            //Dados
            [
                "perguntas" => EnvioResources::collection($perguntas)
            ]
        );
    }

    //Função que o sindico cadastra a contra resonsta no registro do envio
    public function updadeContraResposta(Request $request, string $id){

        //Pega da request a contra responta
        $contraResposta = $request->input("contra_resposta");

        //Pegando o envio
        $envio = Envio::find($id);

        //Atualiza a contra resposta
        $envio->contra_resposta = $contraResposta;

        //Realiza a atualização 
        $envio->save();

        //Retorna um json com formatação padrão
        return $this->responseJson(
            "Contra resposta cadastrada com sucesso!", //Menssagem
            200, //Status
            [
                "envio" => new EnvioResources(Envio::find($id))
            ]
        );

    }

    //Função que recupera todos os comunicados do morador
    public function indexMorador(Request $request){

        //Pega a pk do morador
        $pkMorador = $request->User()->pk_id_morador;

        //Pega todos os envios do morador
        $envios = Envio::where('fk_id_morador', $pkMorador)->get();

        //Retorna um JSON com formatação padrão de sucesso
        return $this->responseJson(
            "Comunicados recuperados com sucesso!", //Menssagem
            200,
            [
                'envios' => EnvioResources::collection($envios)
            ]
        );
    }

    //Função que recupera os comunicados do morador não visualizados
    public function indexMoradorNaoVisualizado(Request $request){

        //Pega a pk do morador
        $pkMorador = $request->User()->pk_id_morador;

        //Pega todos os envios do morador
        $envios = Envio::where('fk_id_morador', $pkMorador)
            ->where('visualizado', false);

        //Retorna um JSON com formatação padrão de sucesso
        return $this->responseJson(
            "Comunicados recuperados com sucesso!", //Menssagem
            200,
            [
                'envios' => EnvioResources::collection($envios)
            ]
        );
    }

    //Pegando os detalhes do envio que o morador selecionar
    public function showMorador(string $id){

        //Pega o envio do morador
        $envio = Envio::find($id);

        //Verifica se o envio já foi visualizado
        if($envio->visualizado == false){

            //Atualiza o envio para indicar que o morador visualizou
            $envio->visualizado = true;
            $envio->save();
        }

        //Retorna os dados do JSON com formatação padrão
        return $this->responseJson(
            "Pegando dados do comunicado com sucesso!",
            200,
            [
                'envio' => new EnvioResources($envio)
            ]
        );
    }

     //Função que o morador cadastra a  resonsta no registro do envio
    public function updadeResposta(Request $request, string $id){

        //Pega da request a resposta
        $resposta = $request->input("resposta");

        //Pegando o envio
        $envio = Envio::find($id);

        //Atualiza a resposta
        $envio->resposta = $resposta;

        //Realiza a atualização 
        $envio->save();

        //Retorna um json com formatação padrão
        return $this->responseJson(
            "Pergunta ao sindico cadastrada com sucesso!", //Menssagem
            200, //Status
            [
                "envio" => new EnvioResources(Envio::find($id))
            ]
        );

    }
}