<?php

namespace App\Http\Controllers;

use App\Http\Resources\VisitaResource;
use App\HttpResposta;
use App\Models\Visita;
use App\Models\Visitante;
use GuzzleHttp\Psr7\Query;
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

    // Registers the visitor's entry
    public function allowEntry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fk_porteiro' => 'required|integer|exists:porteiros,pk_id_porteiro',
            'fk_morador' => 'required|integer|exists:moradors,pk_id_morador',
            'fk_visitante' => 'required|integer|exists:visitantes,pk_id_visitante',
        ]);

        if ($validator->fails()) {
            return $this->errorJson(
                'Os dados informados são inválidos.',
                422,
                $validator->errors()->toArray()
            );
        }

        // Retrieves the authenticated doorman
        $porteiro = $request->user();

        if (
            !$porteiro ||
            (int) $porteiro->pk_id_porteiro !==
            (int) $request->input('fk_porteiro')
        ) {
            return $this->errorJson(
                'O ID informado não corresponde ao porteiro autenticado.',
                403
            );
        }

        // Finds the visitor
        $visitante = Visitante::find($request->input('fk_visitante'));

        if (!$visitante) {
            return $this->errorJson(
                'Visitante não encontrado.',
                404
            );
        }

        // Checks whether the visitor already has an open visit
        $visitaAberta = Visita::where(
            'fk_visitante',
            $visitante->pk_id_visitante
        )
            ->whereNotNull('entrada')
            ->whereNull('saida')
            ->exists();

        if ($visitaAberta) {
            return $this->errorJson(
                'Este visitante já possui uma visita em andamento.',
                400
            );
        }

        // Creates the visit with all required foreign keys
        $visita = Visita::create([
            'fk_visitante' => $visitante->pk_id_visitante,
            'fk_morador' => $request->input('fk_morador'),
            'fk_porteiro' => $porteiro->pk_id_porteiro,
            'entrada' => now(),
            'saida' => null,
        ]);

        return $this->responseJson(
            'Entrada do visitante registrada com sucesso!',
            201,
            [$visita->load(['visitante', 'morador', 'porteiro'])->toArray()]
        );
    }

    // Registers the visitor's exit
    public function registerExit(string $id)
    {
        $visitante = Visitante::find($id);

        if (!$visitante) {
            return $this->errorJson(
                'Visitante não encontrado.',
                404
            );
        }

        $visita = Visita::where(
            'fk_visitante',
            $visitante->pk_id_visitante
        )
            ->whereNotNull('entrada')
            ->whereNull('saida')
            ->orderByDesc('entrada')
            ->first();

        if (!$visita) {
            return $this->errorJson(
                'Não existe uma visita em andamento para este visitante.',
                404
            );
        }

        $visita->update([
            'saida' => now(),
        ]);

        return $this->responseJson(
            'Saída do visitante registrada com sucesso!',
            200,
            [$visita->load(['visitante', 'morador', 'porteiro'])->toArray()]
        );
    }
}
