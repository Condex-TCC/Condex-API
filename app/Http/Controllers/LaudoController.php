<?php

namespace App\Http\Controllers;

use App\Http\Resources\LaudoResources;
use Illuminate\Http\Request;
use App\HttpResposta;
use App\Models\Laudos;
use Illuminate\Support\Facades\Validator;

class LaudoController extends Controller
{
    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    //Função que recupera todos os laudos
    public function index()
    {
        //Pegando todos os laudos do banco de dados
        $laudos = Laudos::all();

        //Filtrando os campos do json do laudo
        $jsonFiltrado = LaudoResources::collection($laudos);

        //Retornando os laudos filtrados
        return $this->responseJson(
            "Laudo cadastrado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o novo laudo criado
                $jsonFiltrado
            ]
        );
    }

    //Função que cadastra o morador
    public function store(Request $request)
    {
        //Pegando o sindico logado
        $sindico = $request->user();

        //Pegando o Id do sindico
        $idSindico = $sindico->pk_id_sindico;

        //Obtendo os nome do documento
        $dadosPassados = json_decode($request->input('dados'), true);
        $nomeLaudo = $dadosPassados['nome']; //Obtendo o nome do array

        //Obtendo o documento da requisição
        $documento = $request->file("documento");

        //Verifica se o documente o nome foram enviados
        if($documento != null && $nomeLaudo != null){

            //Salvando o documento na pasta storage com uma hash no nome para evitar conflito | retorna o nome do arquivo
            $caminhoDocumento = $documento->store('laudos', 'public');

            //Pegando os dados obtidos e mepeando um array
            $dadosMapeados = [
                "nome_laudo" => $nomeLaudo,
                "caminho_laudo" => $caminhoDocumento,
                "fk_id_sindico_laudo" => $idSindico
            ];

            //Salvando o novo laudo no banco de dados
            $novoLaudo = Laudos::create($dadosMapeados);

            //Retornando uma json de sucesso
            return $this->responseJson(
                "Laudo cadastrado com sucesso!", //Menssagem
                200, //Status code
                //Passando os dados
                [
                    //Passando o novo laudo criado
                    new LaudoResources($novoLaudo)
                ]
            );

        }else{

            //Retorna um json de erro
            return $this->errorJson(
                "O nome ou o laudo estão invalidos", //Menssagem
                400, //Status code
            );
        }
    }

    //Função que pega um laudo do banco ade dados
    public function show(string $id)
    {
        //Recupera o laudo no banco de dados
        $laudo = Laudos::findOrFail($id);

        //Retorno o json com as regras atualizadas
        //Retornando um json de sucuesso
        return $this->responseJson(
            "Laudo recupedado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando a regra recuperada
                new LaudoResources($laudo)
            ]
        );
    }

    //Função que atualiza um morador
    public function update(Request $request, string $id)
    {
        //Pegando o sindico logado
        $sindico = $request->user();

        //Pegando o Id do sindico
        $idSindico = $sindico->pk_id_sindico;

        //Obtendo os nome do documento
        $dadosPassados = json_decode($request->input('dados'), true);

        $nomeLaudo = $dadosPassados['nome']; //Obtendo o nome do array

        // Recuperando o laudo atual do banco de dados pelo id
        $laudo = Laudos::findOrFail($id);

        // Mapeando os dados básicos que SEMPRE serão atualizados (como o nome)
        $dadosMapeados = [
            "nome_laudo" => $nomeLaudo,
            "fk_id_sindico_laudo" => $idSindico
        ];

        // Verificando se o usuário enviou um ARQUIVO NOVO de fato
        if($request->hasFile("documento")){
            
            // Obtendo o documento da requisição
            $documento = $request->file("documento");
            
            // Salvando o novo documento na pasta storage
            $caminhoDocumento = $documento->store('laudos', 'public');
            
            // Adicionando o novo caminho ao array que vai para o banco de dados
            $dadosMapeados["caminho_laudo"] = $caminhoDocumento;
        }

        // Atualizando os dados do laudo com os dados passados no array
        $atualizado = $laudo->update($dadosMapeados);

        // Se o update falhar por algum motivo no banco
        if(!$atualizado){
            // Retornando uma resposta Json de erro
            return $this->responseJson(
                "Não foi possivel realizar a atualização do Laudo", // Mensagem
                400, // Status code
            );
        }

        // Pegando o laudo com os novos dados atualizados
        $novoLaudo = Laudos::findOrFail($id);

        // Retornando um json de sucesso com os dados atualizados
        return $this->responseJson(
            "Laudo atualizado com sucesso!", // Mensagem
            200, // Status code
            [
                new LaudoResources($novoLaudo) // Passando os dados
            ]
        );
    }

    //Função que remove o laudo
    public function destroy(string $id)
    {
        //Regando o laudo do banco de dados pelo id
        $laudo = Laudos::findOrFail($id); 

        //Deletando o laudo
        $deletado = $laudo->delete();

        //Verificando se o laudo foi deletado com sucesso
        if(!$deletado){

            //Retorna um Json de erro
            return $this->errorJson(
                "Não foi possivel deletar o Laudo", //Menssagem
                400, //Status code
            );
        }

        //Retornando um json de sucesso
        return $this->responseJson(
            "Laudo apagado com sucesso!", //Menssagem
            200, //Status code
        );
    }
}
