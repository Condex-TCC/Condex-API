<?php

namespace App\Http\Controllers;

use App\Http\Resources\VisitaResource;
use App\HttpResposta;
use App\Models\Visita;
use App\Models\Visitante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

//Classe que intermedia as requisições
class VisitaController extends Controller
{
    //Chama o pacote de funções reutilizadas para o envio padronizado se JSON de retorno
    use HttpResposta;

    //Função que retorna todas as visitas realizadas no consominio
    public function index(Request $request)
    {   
        //Comando que cria um construtor flexivel para montar as consulta
        $query = Visita::query();

        //Carrega os relacionamentos
        $query->with(['visitante', 'morador', 'porteiroEntrda', 'porteiroSaida']);

       if ($request->query("nome") != null) {
        $termo = $request->query("nome");
        
            // Faz a busca na tabela relacionada 'visitante'
            $query->whereHas('visitante', function ($q) use ($termo) {
                $q->where('nome_visitante', 'LIKE', "%" . $termo . "%"); // Ajuste 'nome' se a coluna na tabela visitantes tiver outro nome (ex: 'name')
            });
        }

        //Chama a função que realiza a consulta com as cluasulas adicionadas, além de estabelecer a ordem da consutla
        $visitas = $query->orderByDesc('created_at')->get();

        return $this->responseJson(
            'Histórico de visitas recuperado com sucesso!',
            200,
            VisitaResource::collection($visitas)->resolve()
        );
    }

    //Função que retorna todas as visirtas do morador logado
    public function indexMorador(Request $request){

        //Pega a fk do morador logado
        $pkMorador = $request->user()->pk_id_morador;

        //Comando que cria um construtor flexivel para montar as consulta
        $query = Visita::query();

        //Comando que filtra os dados baseados na pk do morador
        $query->where("fk_id_morador", $pkMorador);

        //Carrega os relacionamentos
        $query->with(['visitante', 'morador', 'porteiroEntrda', 'porteiroSaida']);

        //Verifica se foi enviado o nome do morador
        if($request->query("nome") != null){
            $termo = $request->query("nome");

            //Adiciona na consulta a clausula de conlsulta que filtra pelo nome do morador
            $query->whereHas("visitante", function ($q) use ($termo) {
                $q->where('nome_visitante', 'LIKE', "%" . $termo . "%");
            });
        }
        
        //Realizando a conuslta das visitas pendentes
        $visitasPendentes = $query->clone()->where("autorizacao_visita", null)->get();

        //Realizando a consulta das visitas ativas
        $visitasAtivas = $query->clone()->where("autorizacao_visita", true)
            ->where("hora_registro_entrada_visita", "!=", null)
            ->where("hora_registro_saida_visita", null)->get();
        
        //Realizando a consulta das vistas que vão acontecer
        $visitaFuturas = $query->clone()->where("autorizacao_visita", true)
            ->where("hora_registro_entrada_visita", null)
            ->where("hora_registro_saida_visita", null)->get();

        //Retona os dados com uma formatação padrão
        return $this->responseJson(
            "As visitas do morador forma recuperadas com sucesso!", //Menssagem
            200, //Status code
            [
                "visitas_ativas" => VisitaResource::collection($visitasAtivas),
                "visitas_pendentes" => VisitaResource::collection($visitasPendentes),
                "visitas_futuras" => VisitaResource::collection($visitaFuturas)
            ]
        );
    }

    //Função que retorna o histórico das visitas do morador logado
    public function historicoMorador(Request $request){

        //Pega a fk do morador logado
        $pkMorador = $request->user()->pk_id_morador;

        //Comando que cria um construtor flexivel para montar as consulta
        $query = Visita::query();

        //Comando que filtra os dados baseados na pk do morador
        $query->where("fk_id_morador", $pkMorador);

        //Carrega os relacionamentos
        $query->with(['visitante', 'morador', 'porteiroEntrda', 'porteiroSaida']);

        //Verifica se foi enviado o nome do morador
        if($request->query("nome") != null){
            $termo = $request->query("nome");

            //Adiciona na consulta a clausula de conlsulta que filtra pelo nome do morador
            $query->whereHas("visitante", function ($q) use ($termo) {
                $q->where('nome_visitante', 'LIKE', "%" . $termo . "%");
            });
        }

        //Recupera todos os registros
        $visitas = $query->orderByDesc('created_at')->get();

        //realiza o retorno da com json padronozado
        return $this->responseJson(
            "Histórico de visitas do morador recuperdado com sucesso!",
            200, //Status code
            //Dados
            [
                "visitas" => VisitaResource::collection($visitas)
            ]
        );
    }

    //Função que recupera os dados de uma única visita
    public function show(string $id){

        //Buscando a visita
        $visita = Visita::findOrFail($id);

        //realiza o retorno da com json padronozado
        return $this->responseJson(
            "Dados da pesquisa recuperda com sucesso!",
            200, //Status code
            //Dados
            [
                "visitas" => new VisitaResource($visita)
            ]
        );
    }

    //Função que cadastra um visita
    public function storeMorador(Request $request){

        //Pegando o morador logado
        $pkMorador = $request->user()->pk_id_morador;

        //Monstando um array com os dados da requisição
        $dadosMapeados = [
            'hora_entrada_visita' => $request->input("hora_entrada"),
            'hora_saida_visita' => $request->input("hora_saida"),
            "autorizacao_visita" => 1, //Já deixa a visita autorizada
            'fk_id_visitante' => $request->input("id_visitante"),
            'fk_id_morador' => $pkMorador,
        ];

        //Validando os dados
        $validador = validator($dadosMapeados, [
            'hora_entrada_visita' => "required|date_format:Y-m-d\TH:i",
            'hora_saida_visita' => "required|date_format:Y-m-d\TH:i",
            'fk_id_visitante' => "required",
        ]);

        //Verifica se hove algum erro
        if($validador->fails()){

            //Retorna o json com os erros
            return $this->errorJson(
                "Houve um erro no cadastro da visita",
                400, //Status code
                //Erros
                [
                    $validador->errors(),
                ]
            );
        }

        // Realiza uma consulta para verificar se o morador já está em outra visita no mesmo período
        $outraVisita = Visita::where("fk_id_visitante", $dadosMapeados["fk_id_visitante"])
            // Lógica correta de sobreposição de intervalos de tempo
            ->where("hora_entrada_visita", "<=", $dadosMapeados["hora_saida_visita"])
            ->where("hora_saida_visita", ">=", $dadosMapeados["hora_entrada_visita"])
            // Agrupamento (Closure) para garantir que a checagem de status ocorra apenas para este visitante e período
            ->where(function ($query) {
                $query->where("autorizacao_visita", 1) // Autorizado
                      ->orWhere("autorizacao_visita", null); // Pendente
            })
            ->first();
        
        //Verifica se já existe outra visita nesse horario selecionado
        if($outraVisita != null){

            //Retorna um json de erro
            return $this->errorJson(
                "Esse visitante já está agendado para outra visita! Por favor, agenda para outro horario", //Menssagem
                400, //Status code
                //Dados
                []
            );
        }

        //Salva o visitante no banco de dados
        $visita = Visita::create($dadosMapeados);

        //Retorna um json com formatação padrão
        return $this->responseJson(
            "Visita agendada com sucesso!", //Menssagem
            200, //Status code
            //Dados
            [
                "visita" => new VisitaResource($visita)
            ]
        );
    }

    //Função que autoriza a visita
    public function autoriza(string $id){

        //Recupera a visita
        $visita = Visita::findOrFail($id);

        //Muda a visita para autorizado
        $visita->autorizacao_visita = true;

        //Salva a alterção no banco de dados
        $visita->save();

        //Retorna um json com formatação padrão
        return $this->responseJson(
            "Visitia autorizada!", //Menssagem
            200,
            [
                "visita" => new VisitaResource($visita)
            ]
        );
    }

    //Função que cancela a visita 
    public function cancela(string $id){

        //Recupera a visita
        $visita = Visita::findOrFail($id);

        //Muda a visita para calcelada
        $visita->autorizacao_visita = false;

        //Salva a alterção no banco de dados
        $visita->save();

        //Retorna um json com formatação padrão
        return $this->responseJson(
            "Visita cancela!", //Menssagem
            200,
            [
                "visita" => new VisitaResource($visita)
            ]
        );
    }

    //Função que recupera todas visitas para o porteiro
    public function indexPorteiro(Request $request){

        //Comando que cria um construtor flexivel para montar as consulta
        $query = Visita::query();

        //Carrega os relacionamentos
        $query->with(['visitante', 'morador', 'porteiroEntrda', 'porteiroSaida']);

        //Verifica se foi enviado o nome do morador
        if($request->query("nome") != null){
            $termo = $request->query("nome");

            //Adiciona na consulta a clausula de conlsulta que filtra pelo nome do morador
            $query->whereHas("visitante", function ($q) use ($termo) {
                $q->where('nome_visitante', 'LIKE', "%" . $termo . "%");
            });
        }
        
        //Realizando a conuslta das visitas pendentes
        $visitasPendentes = $query->clone()->where("autorizacao_visita", null)->get();

        //Realizando a consulta das visitas ativas
        $visitasAtivas = $query->clone()->where("autorizacao_visita", true)
            ->where("hora_registro_entrada_visita", "!=", null)
            ->where("hora_registro_saida_visita", null)->get();
        
        //Realizando a consulta das vistas que vão acontecer
        $visitaFuturas = $query->clone()->where("autorizacao_visita", true)
            ->where("hora_registro_entrada_visita", null)
            ->where("hora_registro_saida_visita", null)->get();

        //Retona os dados com uma formatação padrão
        return $this->responseJson(
            "As visitas do morador forma recuperadas com sucesso!", //Menssagem
            200, //Status code
            [
                "visitas_ativas" => VisitaResource::collection($visitasAtivas),
                "visitas_pendentes" => VisitaResource::collection($visitasPendentes),
                "visitas_futuras" => VisitaResource::collection($visitaFuturas)
            ]
        );
    }

    //Função que recupera o histórico de visitas do predio
    public function historicoPorteiro(Request $request){

        //Comando que cria um construtor flexivel para montar as consulta
        $query = Visita::query();

        //Carrega os relacionamentos
        $query->with(['visitante', 'morador', 'porteiroEntrda', 'porteiroSaida']);

        //Verifica se foi enviado o nome do morador
        if($request->query("nome") != null){
            $termo = $request->query("nome");

            //Adiciona na consulta a clausula de conlsulta que filtra pelo nome do morador
            $query->whereHas("visitante", function ($q) use ($termo) {
                $q->where('nome_visitante', 'LIKE', "%" . $termo . "%");
            });
        }

        //Recupera todos os registros
        $visitas = $query->orderByDesc('created_at')->get();

        //realiza o retorno da com json padronozado
        return $this->responseJson(
            "Histórico de visitas recuperdada com sucesso!",
            200, //Status code
            //Dados
            [
                "visitas" => VisitaResource::collection($visitas)
            ]
        );
    }

    //Função que recupera os dados de uma única visita para o porteiro
    public function showPorteiro(string $id){

        //Buscando a visita
        $visita = Visita::findOrFail($id);

        //realiza o retorno da com json padronozado
        return $this->responseJson(
            "Dados da visita recuperda com sucesso!",
            200, //Status code
            //Dados
            [
                "visita" => new VisitaResource($visita)
            ]
        );
    }

    //Função que cadastra um visita para o porteiro
    public function storePorteiro(Request $request){


        //Monstando um array com os dados da requisição
        $dadosMapeados = [
            'hora_entrada_visita' => $request->input("hora_entrada"),
            'hora_saida_visita' => $request->input("hora_saida"),
            'fk_id_visitante' => $request->input("id_visitante"),
            'fk_id_morador' => $request->input("id_morador"),
        ];

        //Validando os dados
        $validador = validator($dadosMapeados, [
            'hora_entrada_visita' => "required|date_format:Y-m-d\TH:i",
            'hora_saida_visita' => "required|date_format:Y-m-d\TH:i",
            'fk_id_visitante' => "required",
            'fk_id_morador' => "required",
        ]);

        //Verifica se hove algum erro
        if($validador->fails()){

            //Retorna o json com os erros
            return $this->errorJson(
                "Houve um erro no cadastro da visita",
                400, //Status code
                //Erros
                [
                    $validador->errors(),
                ]
            );
        }

        // Realiza uma consulta para verificar se o morador já está em outra visita no mesmo período
        $outraVisita = Visita::where("fk_id_visitante", $dadosMapeados["fk_id_visitante"])
            // Lógica correta de sobreposição de intervalos de tempo
            ->where("hora_entrada_visita", "<=", $dadosMapeados["hora_saida_visita"])
            ->where("hora_saida_visita", ">=", $dadosMapeados["hora_entrada_visita"])
            // Agrupamento (Closure) para garantir que a checagem de status ocorra apenas para este visitante e período
            ->where(function ($query) {
                $query->where("autorizacao_visita", 1) // Autorizado
                      ->orWhere("autorizacao_visita", null); // Pendente
            })
            ->first();
        
        //Verifica se já existe outra visita nesse horario selecionado
        if($outraVisita != null){

            //Retorna um json de erro
            return $this->errorJson(
                "Esse visitante já está agendado para outra visita! Por favor, agenda para outro horario", //Menssagem
                400, //Status code
                //Dados
                []
            );
        }

        //Salva o visitante no banco de dados
        $visita = Visita::create($dadosMapeados);

        //Retorna um json com formatação padrão
        return $this->responseJson(
            "Visita agendada com sucesso!", //Menssagem
            200, //Status code
            //Dados
            [
                "visita" => new VisitaResource($visita)
            ]
        );
    }

    //Função que deleta o registro de uma visita não autorizada
    public function delete(string $id){

        //Recuperando o registro
        $visita = Visita::findOrFail($id);

        //Verificando se a visita é nula e pode ser deletada
        if($visita->autorizacao_visita !== null){

            //Montando a menssagem
            $menssagem =  $visita->autorizacao_visita == 1 ? "autorizou" : "resucou" . " visita";

            //Retorna uma menssagen com formatação padrão
            return $this->errorJson(
                "O registro não pode ser apagado, pois morador já " . $menssagem, //Menssgam
                400, //Status code
                //Dados
                [

                ]
            );

        }else{
            
            //Deletando a visita 
            $visita->delete();

            //Retorna uma menssagen com formatação padrão
            return $this->responseJson(
                "O registro foi apagado com sucesso!", //Menssgam
                200, //Status code
                //Dados
                [

                ]
            );
        }
    }

    //Função que registra a entrada do visitante no condominio
    public function entrada(Request $request, string $id){

        //Pegando a pk do porteiro
        $pkPorteiro = $request->user()->pk_id_porteiro;

        //Pegando a visita
        $visita = Visita::findOrFail($id);

        //Verifica se o morador autorizou a entrada
        if($visita->autorizacao_visita === 1){

            //Adicioandan os novos dados
            $visita->fk_id_porteiro_entrada = $pkPorteiro;
            $visita->hora_registro_entrada_visita = now();

            //Atualizando os dados
            $visita->save();

            //Retorna um json com formatação padrão
            return $this->responseJson(
                "Entrada registrada registrada com sucesso!", //Menssagem
                200,
                [
                    "visita" => new VisitaResource(Visita::find($id))
                ]
            );

        }else{

            //Retorna uma menssagem de erro
            return $this->errorJson(
                "Não é possivel registrar a entrada do visitante! Não foi permitido a sua entrada", //Menssage
                400, //Status code
                [

                ]
            );
        }

        
        
    }

    //Função que registra a saida do visitante no condominio
    public function saida(Request $request, string $id){

        //Pegando a pk do porteiro
        $pkPorteiro = $request->user()->pk_id_porteiro;

        //Pegando a visita
        $visita = Visita::findOrFail($id);

        //Verifica se o morador autorizou a entrada
        if($visita->autorizacao_visita === 1 && $visita->hora_registro_entrada_visita !== null){

            //Adicioandan os novos dados
            $visita->fk_id_porteiro_saida = $pkPorteiro;
            $visita->hora_registro_saida_visita = now();

            //Atualizando os dados
            $visita->save();

            //Retorna um json com formatação padrão
            return $this->responseJson(
                "Entrada saida do visitante foi registrada com sucesso!", //Menssagem
                200,
                [
                    "visita" => new VisitaResource(Visita::find($id))
                ]
            );

        }else{

            //Retorna uma menssagem de erro
            return $this->errorJson(
                "Não é possivel registrar a saida do visitante! Não foi registrada a sua entrada", //Menssage
                400, //Status code
                [

                ]
            );
        }

        
        
    }
}
