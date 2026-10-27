<?php

namespace App\Http\Controllers;

use App\Http\Resources\ReservaResource;
use App\Models\Reserva;
use App\Models\Espaco;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\HttpResposta;
use Carbon\Carbon;

class ReservaController extends Controller
{
    use HttpResposta;

    // Retorna as reservas do morador logado com filtros opcionais
public function indexMorador(Request $request)
{
    $morador = $request->user();

    $query = Reserva::with('espaco')
        ->where('fk_id_morador', $morador->pk_id_morador);

    // Filtra pela data da reserva
    if ($request->filled('data')) {
        $query->where('data_reserva', $request->input('data'));
    }

    // Filtra pelo status da reserva
    if ($request->filled('status')) {
        $query->where(
            'status_reserva',
            'LIKE',
            '%' . $request->input('status') . '%'
        );
    }

    $reservas = $query->get();

    return $this->responseJson(
        'Reservas encontradas com sucesso.',
        200,
        [
            "reservas" => ReservaResource::collection($reservas)
        ]
    );
}

    // Retorna as reservas pendentes para o síndico, com filtros opcionais
public function indexSindico(Request $request)
{
    $query = Reserva::with('espaco')
        ->where('status_reserva', 'pendente');

    // Filtra pela data da reserva
    if ($request->filled('data')) {
        $query->where('data_reserva', $request->input('data'));
    }

    $reservas = $query->get();

    return $this->responseJson(
        'Reservas pendentes encontradas com sucesso.',
        200,
        [
            "reservas" => ReservaResource::collection($reservas)
        ]
    );
}

    // Retorna uma reserva específica do morador logado
    public function showMorador(Request $request, string $id)
    {
        $morador = $request->user();

        $reserva = Reserva::with('espaco')
            ->where('pk_id_reserva', $id)
            ->first();

        if (!$reserva) {
            return $this->errorJson(
                'Reserva não encontrada.',
                404
            );
        }

        return $this->responseJson(
            'Reserva encontrada com sucesso.',
            200,
            (new ReservaResource($reserva))->resolve()
        );
    }

    // Cria uma nova reserva
    public function store(Request $request)
    {
        //Pegando o morador logado
        $morador = $request->user();

        //Pegando o local da reserva
        $fkEspaco = $request->input('id_espaco');

        //Recuperando o espaço
        $espaco = Espaco::find($fkEspaco);

        //Recuperando se o espaço precisa de autorização
        $autorizacao = $espaco->autorizacao;

       //Montando um array com os dados mapeados
       $dadosMapeados = [
        'fk_id_morador' => $morador->pk_id_morador,
        'fk_id_espaco' => $fkEspaco,
        'data_reserva' => Carbon::createFromFormat('d/m/Y', $request->input('data'))->format('Y-m-d'),
        'hora_inicio' => $request->input('inicio'),
        'hora_fim' => $request->input('fim'),
        'status_reserva' => ($autorizacao == true) ? 'pendente' : 'confirmada',
       ];

        //Validando o array mapeado
        $validator = Validator::make($dadosMapeados, [
            'fk_id_morador' => "required",
            'fk_id_espaco' => "required",
            'data_reserva' => "required|date",
            'hora_inicio' => "required|date_format:H:i:s",
            'hora_fim' => "required|date_format:H:i:s",
            'status_reserva' => "required",
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

        //Valida para ver se não há conflito das datas
        $conflito = Reserva::where('fk_id_espaco', $dadosMapeados['fk_id_espaco'])
            ->where('data_reserva', $dadosMapeados['data_reserva'])
            ->where('status_reserva', "!=", 'cancelada')
            ->where(function ($query) use ($dadosMapeados) {
                $query->where('hora_inicio', '<', $dadosMapeados['hora_fim'])
                    ->where('hora_fim', '>', $dadosMapeados['hora_inicio']);
            })
            ->exists();

        //Verifica se não há conflito
        if ($conflito) {

            //Retorna um json com formatação para erro
            return $this->errorJson(
                'Este espaço já está reservado neste horário.', //Menssagem
                400, //Status 
            );
        }

        //Função que realiza o cadastro da reserva no banco de dados
        $reserva = Reserva::create($dadosMapeados);

        //Retorna um json de sucesso
        return $this->responseJson(
            'Reserva solicitada com sucesso.', //menssagem
            201, //status
            (new ReservaResource($reserva->load('espaco')))->resolve() //dados
        );
    }

    //Função que cancela a reserva para o morador
    public function cancelReservation(string $id){

        //Recuperando a reserva do morador
        $reserva = Reserva::find($id);

        //Alterando o estado da reserva
        $reserva->status_reserva = "cancelada";

        //Salvando a alteração 
        $reserva->save();

        //Retorna um json de sucesso
        return $this->responseJson(
            'Reserva cancelada com sucesso.', //menssagem
            201, //status
            (new ReservaResource($reserva->load('espaco')))->resolve() //dados
        );
    }

    //Função que o sindico aprevo ou não, uma reserva pendente
    public function aprovedRervation(Request $request, string $id){

        //Recuperando se a reserva foi aprovada ou não
        $aprovada = $request->input('aprovada');

        //Recuperando a reserva do morador
        $reserva = Reserva::find($id);

        //Mensagem que será envia no json
        $menssagem = "";

        //Varificando se a reseva foi aprovada
        if($aprovada == true){

            //Muda o status da reserva para comfirmada
            $reserva->status_reserva = "confimada";

            //Adicionando a menssagem
            $menssagem = 'Reserva autorizada com sucesso!';

        }else{

            //Muda o status da reserva para comfirmada
            $reserva->status_reserva = "cancelada";

            //Adicionando a menssagem
            $menssagem = 'Reserva cancelada com sucesso!';

        }

        //Salva a alteração
        $reserva->save();

        //Retorna um json de sucesso
        return $this->responseJson(
            $menssagem, //menssagem
            200, //status
            //Dados
            [
                "reserva" => new ReservaResource($reserva)
            ]
        );

    }

    //Função que recupera todas as reservas de um determinado dia
    public function getReservationsDate(Request $request){
        
        //Pegando a data da requisição
        $data = $request->input('data');

        //Filtra as resercas apertir das datas
        $reservasFiltradas = Reserva::where('data_reserva', $data)
            ->where("status_reserva", 'confimada')->get();

        //Filtrando as reservas não pendentes
        $reservasFiltradasPendentes = Reserva::where('data_reserva', $data)
            ->where("status_reserva", 'pendente')->get();

        //Retorna um json de sucesso
        return $this->responseJson(
            'Reserva cancelada com sucesso.', //menssagem
            200, //status
            //Dados
            [
                "reservas" => ReservaResource::collection($reservasFiltradas),
                "reservas_pendentes" => ReservaResource::collection($reservasFiltradasPendentes)
            ]
        );
    }

    //Função que pega uma única reserva para o sindico
    public function showReservationSindico(string $id){

        //Recuperando a reserva selecionada
        $reserva = Reserva::find($id);

        //Retorna um json de sucesso
        return $this->responseJson(
            'Reserva recuperada com sucesso.', //menssagem
            200, //status
            //Dados
            [
                "reserva" => new ReservaResource($reserva)
            ]
        );
    }

   

    
}