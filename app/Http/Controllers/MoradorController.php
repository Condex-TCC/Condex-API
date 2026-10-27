<?php

namespace App\Http\Controllers;

use App\Http\Resources\MoradorResurce;
use App\Models\Morador;
use Illuminate\Http\Request;
use App\HttpResposta;
use App\Models\Unidade;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;

class MoradorController extends Controller
{
    //Adiciona a trait com as respotas padrões para APIs
    use HttpResposta;

    
    // Função que recupera todos os moradores, com opção de pesquisa
    public function index(Request $request)
    {
        // Inicia a consulta carregando o relacionamento com unidade
        $query = Morador::with('unidade');

        // Filtra pelo email quando o parâmetro for informado
        if ($request->filled('email')) {
            $query->where(
                'email_morador',
                'LIKE',
                '%' . $request->input('email') . '%'
            );
        }

        // Recupera os moradores encontrados
        $moradores = $query->get();

        // Formata os dados usando a Resource
        $jsonTratadado = MoradorResurce::collection($moradores);

        // Retorna a resposta JSON
        return $this->responseJson(
            "Usuarios recuperados com sucesso!",
            200,
            [$jsonTratadado]
        );
    }


    //Função que cadastra o morador
    public function store(Request $request)
    {
        //Pegando os dados do corpo da requisição e montando um array com os campos do banco de dados
        $dadosMapeados = [
            "email_morador" => $request->input("email"), //Pegando o email
            "senha_morador" => Hash::make("password"), //Pegando a senha e transformando-a em hash
            "fk_id_unidade_morador" => $request->input("id_unidade"), //Pegando o id da unidade
        ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            "email_morador" => 'required|string|max:150',
            "senha_morador" => "required",
            "fk_id_unidade_morador" => 'required|numeric',
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

        //Criando um novo Morador
        $novoMorador = Morador::create($dadosMapeados);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Morador criado com sucesso! A senha padrão é password", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o morador criado
                new MoradorResurce($novoMorador->load("unidade"))
            ]
        );

    }

   //Função que recupera o morador
    public function show(string $id)
    {
        //Pegando o morador no banco de dados
        $morador = Morador::findOrFail($id);

        //Retornando um json de sucuesso
        return $this->responseJson(
            "Morador recuperado com sucesso!", //Menssagem
            200, //Status code
            //Passando os dados
            [
                //Passando o morador criado
                new MoradorResurce($morador)
            ]
        );
    }

    //Função que remove o morador
    public function destroy(string $id)
    {
        //Regando o morador do banco de dados pelo id
        $morador = Morador::findOrFail($id); 

        //Deletando o morador
        $deletado = $morador->delete();

        //Verificando de o morador foi deletado com sucesso
        if(!$deletado){

            //Retorna um Json de erro
            return $this->errorJson(
                "Não foi possivel deletar o morador", //Menssagem
                400, //Status code
            );
        }

        //Retornando um json de sucesso
        return $this->responseJson(
            "Morador apagado com sucesso!", //Menssagem
            200, //Status code
        );
    }

    //Função que vai reficiar se tem algum morador já cadastrado com um apertamento expecifico
    public function verify(string $id){

        //Realiza a busca no banco de dados e busca por algum morador que já tenha o apartemento cadastrado
        //retorna true e false
        $exiteMorador = Morador::where('fk_id_unidade_morador', $id)->exists();

        //Verifica o retorno para validar a saida
        if(!$exiteMorador){
            
            //Retornando um json de sucuesso
            return $this->responseJson(
                "Não tem morador cadastrado com esse apertamento!", //Menssagem
                200, //Status code
                //Passando os dados
                [
                    "exite" => $exiteMorador,
                ]
            );

        }else{

            //Retorna um Json de erro
            return $this->errorJson(
                "Já existe morador cadastrado com esse apertamento", //Menssagem
                400, //Status code
                [],
                [
                    "exite" => $exiteMorador,
                ]
            );
        }
    }
}
