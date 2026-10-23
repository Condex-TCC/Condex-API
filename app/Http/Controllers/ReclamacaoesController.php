<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReclamacaoRecource;
use Illuminate\Http\Request;
use App\HttpResposta;
use App\Models\Reclamacoes;
use Illuminate\Support\Facades\Validator;

//Classe responsavel por intermediar as requisições do usuário
class ReclamacaoesController extends Controller
{
    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    //Função que recupera todos as reclamações do morador logado
    public function indexMorador(Request $request){

        //Pega o usuário logado
        $pkMorador = $request->user()->pk_id_morador;
        
        //Pegando todoas as reclmações do morador logado
        $reclamacoes = Reclamacoes::where("fk_id_morador_reclamacao", $pkMorador)->get();

        //Retona um json com formatação padrão com os comunicados do morador
        return $this->responseJson(
            "Reclamações recuperadas com sucesso!", //Menssagem
            200, //Status
            [
                ReclamacaoRecource::collection($reclamacoes)
            ]
        );
    }

    //Função que cadastra uma reclamação do morador
    public function store(Request $request){
        
        //Pega o usuário logado
        $pkMorador = $request->user()->pk_id_morador;

        //Pegando a pk do sindico
        $pkSindico = 1;

        //Array com os dados mapeados
        $dadosMapeados = [
            "reclamacao" => $request->input("reclamacao"),
            "fk_id_morador_reclamacao" => $pkMorador,
            "fk_id_sindico_reclamacao" => $pkSindico,
        ];

        //Validando os dados da request
        $validator =  Validator::make($dadosMapeados, [
            "reclamacao" => 'required|string',
            "fk_id_morador_reclamacao" => 'required',
            "fk_id_sindico_reclamacao" => 'required',
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

        //Cadastra a nova reclamação
        $novaReclamacao = Reclamacoes::create($dadosMapeados);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Reclamação cadastrada com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o regra criado
                new ReclamacaoRecource($novaReclamacao)
            ]
        );
    }

    //Função que detela uma reclamação do morador
    public function delete(string $id){

        //Recupera o registro da reclamação
        $reclamacao = Reclamacoes::find($id);

        //Deleta a reclamaçaõ
        $reclamacao->delete();

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Reclamação apagada com sucesso!", //Menssagem
            200, //Status code
        );
    }

    //Função que recupera as reclamaçoes não visualidas
    public function indexSindico(){

        //Recuperando todas as reclamçaões não visualidas
        $reclamacoes = Reclamacoes::where("visualizado_reclamacao", false)->get();

        //Retona um json com formatação padrão com os comunicados do morador
        return $this->responseJson(
            "Reclamações não visualizadas recuperadas com sucesso!", //Menssagem
            200, //Status
            [
                ReclamacaoRecource::collection($reclamacoes)
            ]
        );
    }

    //Função que recupera todas as reclamações cadastradas no banco de dados
    public function historicoSindico(){

        //Recuperando todas as reclamçaões
        $reclamacoes = Reclamacoes::all();

        //Retona um json com formatação padrão com os comunicados do morador
        return $this->responseJson(
            "Reclamações recuperadas com sucesso!", //Menssagem
            200, //Status
            [
                ReclamacaoRecource::collection($reclamacoes)
            ]
        );
    }

    //Função que recupa os dados de apenas uma reclamação | Marca como visualida a reclamação
    public function showsindico(string $id){

        //Recupera o resgistro do banco de dados
        $reclamacao = Reclamacoes::find($id);

        //Verifica se o registro existe
        if($reclamacao == null){

            //Retona um json de erro
            return $this->errorJson(
                "O registro foi deletado pelo morado! Recarrega a página", //menssagem
                400, //Status
                []
            );
        }

        //Atualizando o valor da propriedade de visualidado para true
        $reclamacao->visualizado_reclamacao = 1;

        //Atualizando o registro
        $reclamacao->save();

        //Retetorna a reclamação com os dados em uma formatação padrão
        return $this->responseJson(
            "Reclamanção recuperada com sucesso!", //Menssagem
            200, //Status
            [
                "reclamacao" => new ReclamacaoRecource($reclamacao)
            ]
        );
    }

    //Função que atualiza o registro da reclamação adicionando resposta
    public function resposta(Request $request, string $id){

        //Recupera o resgistro do banco de dados
        $reclamacao = Reclamacoes::find($id);

        //Verifica se o registro existe
        if($reclamacao == null){

            //Retona um json de erro
            return $this->errorJson(
                "O registro foi deletado pelo morado! Não é mais possivel responder a essa reclamação", //menssagem
                400, //Status
            );
        }

        //Pegando os dados da requisição
        $resposta = $request->input("resposta");

        //Atualiando o campo de resposta do registro 
        $reclamacao->resposta_reclamacao = $resposta;

        //Atualizando o regsitro no banco de dados
        $reclamacao->save();

        //Retorna um json com formatação padrão
        return $this->responseJson(
            "Resposta para a reclamação do morador foi cadastrada com sucesso", //Menssagem
            200,
            [
                "reclamacao" => new ReclamacaoRecource(Reclamacoes::find($id)),
            ]
        );

    }
}
