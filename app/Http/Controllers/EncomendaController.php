<?php

namespace App\Http\Controllers;

use App\Http\Resources\EncomendaResources;
use App\Models\Encomenda;
use App\HttpResposta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EncomendaController extends Controller
{
    use HttpResposta;

    
    // Função que recupera todas as encomendas para o porteiro
    public function index(Request $request)
    {
        // Busca as encomendas e carrega os relacionamentos
        $query = Encomenda::with(['morador', 'porteiro']);

        // Filtra pelo nome da encomenda
        if ($request->filled('nome')) {
            $query->where(
                'nome_encomenda',
                'LIKE',
                '%' . $request->input('nome') . '%'
            );
        }

        // Filtra pela descrição da encomenda
        if ($request->filled('descricao')) {
            $query->where(
                'descricao_encomenda',
                'LIKE',
                '%' . $request->input('descricao') . '%'
            );
        }

        // Recupera as encomendas encontradas
        $encomendas = $query->get();

        // Retorna a lista no formato definido pelo Resource
        return $this->responseJson(
            'Encomendas recuperadas com sucesso!',
            200,
            [
                'encomendas' => EncomendaResources::collection($encomendas)
            ]
        );
    }


    
    // Função que recupera as encomendas do morador autenticado
    public function indexMorador(Request $request)
    {
        // Recupera o morador autenticado
        $morador = $request->user();

        // Busca somente as encomendas do morador autenticado
        $query = Encomenda::with(['morador', 'porteiro'])
            ->where(
                'fk_id_morador_encomenda',
                $morador->pk_id_morador
            );

        // Filtra pelo nome da encomenda
        if ($request->filled('nome')) {
            $query->where(
                'nome_encomenda',
                'LIKE',
                '%' . $request->input('nome') . '%'
            );
        }

        // Filtra pela descrição da encomenda
        if ($request->filled('descricao')) {
            $query->where(
                'descricao_encomenda',
                'LIKE',
                '%' . $request->input('descricao') . '%'
            );
        }

        // Recupera as encomendas encontradas
        $encomendas = $query->get();

        // Retorna as encomendas encontradas
        return $this->responseJson(
            'Encomendas do morador recuperadas com sucesso!',
            200,
            [
                'encomendas' => EncomendaResources::collection($encomendas)
            ]
        );
    }


    // Função que recupera uma encomenda específica do morador autenticado
    public function showMorador(Request $request, string $id)
    {
        // Recupera o morador autenticado na requisição
        $morador = $request->user();

        // Busca a encomenda pelo ID, garantindo que pertença ao morador autenticado
        $encomenda = Encomenda::with([
            'morador',
            'porteiro'
        ])->where(
            'pk_id_encomenda',
            $id
        )->where(
            'fk_id_morador_encomenda',
            $morador->pk_id_morador
        )->first();

        // Verifica se a encomenda existe e pertence ao morador
        if (!$encomenda) {
            return $this->errorJson(
                'Encomenda não encontrada.',
                404
            );
        }

        // Retorna os dados da encomenda encontrada
        return $this->responseJson(
            'Encomenda recuperada com sucesso!',
            200,
            [
                'encomenda' => new EncomendaResources($encomenda)
            ]
        );
    }

    // Função que cadastra uma nova encomenda para um morador
    public function store(Request $request)
    {
        // Recupera o porteiro autenticado na requisição
        $porteiro = $request->user();

        // Mapeia os dados recebidos pela API para os campos utilizados no banco
        $dadosMapeados = [
            'nome_encomenda' => $request->input('nome'),
            'descricao_encomenda' => $request->input('descricao'),
            'fk_id_morador_encomenda' => $request->input('id_morador'),
            'fk_id_porteiro_encomenda' => $porteiro->pk_id_porteiro,
        ];

        // Valida os dados antes de cadastrar a encomenda
        $validator = Validator::make($dadosMapeados, [
            'nome_encomenda' => 'required|string|max:255',
            'descricao_encomenda' => 'required|string',
            'fk_id_morador_encomenda' => 'required|integer|exists:moradors,pk_id_morador',
            'fk_id_porteiro_encomenda' => 'required|integer|exists:porteiros,pk_id_porteiro',
        ]);

        // Retorna os erros caso os dados informados sejam inválidos
        if ($validator->fails()) {
            return $this->errorJson(
           'Os dados informados são inválidos.',
            422,
            $validator->errors()->toArray()
        );
}

        // Cria a encomenda com os dados validados
        $encomenda = Encomenda::create($dadosMapeados);

        // Carrega os dados relacionados para incluí-los na resposta
        $encomenda->load([
            'morador',
            'porteiro'
        ]);

        // Retorna a encomenda cadastrada
        return $this->responseJson(
            'Encomenda criada com sucesso!',
            201,
            [
                'encomenda' => new EncomendaResources($encomenda)
            ]
        );
    }

    // Função que recupera uma encomenda específica para o porteiro
    public function show(string $id)
    {
        // Busca a encomenda pelo ID e carrega os dados relacionados
        $encomenda = Encomenda::with([
            'morador',
            'porteiro'
        ])->find($id);

        // Verifica se a encomenda foi encontrada
        if (!$encomenda) {
            return $this->errorJson(
                'Encomenda não encontrada.',
                404
            );
        }

        // Retorna os dados da encomenda encontrada
        return $this->responseJson(
            'Encomenda recuperada com sucesso!',
            200,
            [
                'encomenda' => new EncomendaResources($encomenda)
            ]
        );
    }

    // Função que atualiza os dados de uma encomenda
    public function update(Request $request, string $id)
    {
        // Busca a encomenda que será atualizada
        $encomenda = Encomenda::find($id);

        // Verifica se a encomenda existe
        if (!$encomenda) {
            return $this->errorJson(
                'Encomenda não encontrada.',
                404
            );
        }

        // Recupera o porteiro autenticado na requisição
        $porteiro = $request->user();

        // Mapeia os dados recebidos para os campos do banco
        $dadosMapeados = [
            'nome_encomenda' => $request->input('nome'),
            'descricao_encomenda' => $request->input('descricao'),
            'fk_id_porteiro_encomenda' => $porteiro->pk_id_porteiro,
        ];

        // Valida os dados que serão utilizados na atualização
        $validator = Validator::make($dadosMapeados, [
            'nome_encomenda' => 'required|string|max:255',
            'descricao_encomenda' => 'required|string',
            'fk_id_porteiro_encomenda' => 'required|integer|exists:porteiros,pk_id_porteiro',
        ]);

        // Retorna os erros caso os dados informados sejam inválidos
        if ($validator->fails()) {
            return $this->errorJson(
            'Os dados informados são inválidos.',
            422,
            $validator->errors()->toArray()
        );
}

        // Atualiza os dados da encomenda
        $encomenda->update($dadosMapeados);

        // Carrega os dados relacionados para retornar a encomenda atualizada
        $encomenda->load([
            'morador',
            'porteiro'
        ]);

        // Retorna a encomenda atualizada
        return $this->responseJson(
            'Encomenda atualizada com sucesso!',
            200,
            [
                'encomenda' => new EncomendaResources($encomenda)
            ]
        );
    }

    // Função que registra a retirada de uma encomenda
    public function registerWithdrawal(string $id)
    {
        // Busca a encomenda pelo ID
        $encomenda = Encomenda::find($id);

        // Verifica se a encomenda existe
        if (!$encomenda) {
            return $this->errorJson(
                'Encomenda não encontrada.',
                404
            );
        }

        // Impede que uma encomenda já retirada seja registrada novamente
        if ($encomenda->data_retirada !== null) {
            return $this->errorJson(
                'Esta encomenda já foi retirada!',
                400
            );
        }

        // Registra a data e a hora atuais como momento da retirada
        $encomenda->data_retirada = now();

        // Salva a alteração e verifica se a operação foi realizada
        if (!$encomenda->save()) {
            return $this->errorJson(
                'Não foi possível registrar a retirada da encomenda.',
                400
            );
        }

        // Carrega os dados relacionados para retornar a encomenda atualizada
        $encomenda->load([
            'morador',
            'porteiro'
        ]);

        // Retorna a confirmação da retirada
        return $this->responseJson(
            'Retirada da encomenda registrada com sucesso!',
            200,
            [
                'encomenda' => new EncomendaResources($encomenda)
            ]
        );
    }

    // Função que exclui uma encomenda
    public function destroy(string $id)
    {
        // Busca a encomenda que será excluída
        $encomenda = Encomenda::find($id);

        // Verifica se a encomenda existe
        if (!$encomenda) {
            return $this->errorJson(
                'Encomenda não encontrada.',
                404
            );
        }

        // Exclui a encomenda e verifica se a operação foi realizada
        if (!$encomenda->delete()) {
            return $this->errorJson(
                'Não foi possível excluir a encomenda.',
                400
            );
        }

        // Retorna a confirmação da exclusão
        return $this->responseJson(
            'Encomenda excluída com sucesso!',
            200
        );
    }
}
