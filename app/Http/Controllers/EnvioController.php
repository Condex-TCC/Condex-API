<?php

namespace App\Http\Controllers;

use App\Http\Resources\ComunicadoResources;
use App\Http\Resources\EnvioResources;
use App\Models\Comunicado;
use App\Models\Envio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\HttpResposta;
use App\Models\Morador;

class EnvioController extends Controller
{
    use HttpResposta;

    //TODO: Fazer aqui!
    public function index()
    {
        
    }

    //Função que cadastra as comunicados e os registros
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
                    'moradores_enviados' => $arrayDeTodosMoradores
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
                    'moradores_enviados' => $arrayDeMoradoresSelecionados
                ]
            );

        }

    }


    // =========================================================
    // MORADOR RESPONDE UM COMUNICADO
    // =========================================================

    public function respond(Request $request, string $id)
    {
        $morador = $request->user();

        $validator = Validator::make(
            $request->all(),
            [
                'descricao' => 'required|string|max:255',
            ]
        );

        if ($validator->fails()) {

            return $this->errorJson(
                "Os dados passados não estão corretos!",
                400,
                [
                    $validator->errors()
                ]
            );
        }

        $comunicado = Comunicado::find($id);

        if (!$comunicado) {

            return $this->errorJson(
                "Comunicado não encontrado!",
                404
            );
        }

        $envio = Envio::where(
            'fk_id_comunicados',
            $id
        )
        ->where(
            'fk_id_morador',
            $morador->pk_id_morador
        )
        ->first();

        if (!$envio) {

            return $this->errorJson(
                "Esse comunicado não foi enviado para você!",
                404
            );
        }

        if ($envio->resposta != null) {

            return $this->errorJson(
                "Você já respondeu esse comunicado!",
                400
            );
        }

        $envio->update([
            'resposta' => $request->input('descricao')
        ]);

        $envio->load([
            'comunicado',
            'morador'
        ]);

        return $this->responseJson(
            "Resposta cadastrada com sucesso!",
            201,
            [
                new EnvioResources($envio)
            ]
        );
    }


    // =========================================================
    // MORADOR VISUALIZA SEU HISTÓRICO DE RESPOSTAS
    // =========================================================

    public function indexRespostasMorador(Request $request)
    {
        $morador = $request->user();

        $envios = Envio::with([
            'comunicado'
        ])
            ->where(
                'fk_id_morador',
                $morador->pk_id_morador
            )
            ->whereNotNull('resposta')
            ->get();

        $jsonTratado = EnvioResources::collection($envios);

        return $this->responseJson(
            "Histórico de respostas recuperado com sucesso!",
            200,
            [
                $jsonTratado
            ]
        );
    }
}