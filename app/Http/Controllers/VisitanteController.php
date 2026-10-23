<?php

namespace App\Http\Controllers;

use App\Http\Resources\VisitanteResources;
use App\Models\Visitante;
use Illuminate\Http\Request;
use App\HttpResposta;
use Illuminate\Support\Facades\Validator;

class VisitanteController extends Controller
{
    use HttpResposta;

    // Função que retorna todos os visitantes para o porteiro
    public function index()
    {
        // Recupera todos os visitantes cadastrados
        $visitantes = Visitante::all();

        // Retorna os visitantes no formato definido pelo Resource
        return $this->responseJson(
            'Visitantes recuperados com sucesso!',
            200,
            [
                'visitantes' => VisitanteResources::collection($visitantes)
            ]
        );
    }

    // Função que permite ao porteiro cadastrar um visitante
    public function store(Request $request)
    {
        // Recupera o porteiro autenticado
        $porteiro = $request->user();

        // Valida os dados enviados para o cadastro
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante',
            'morador' => 'required|integer|exists:moradors,pk_id_morador',
        ]);

        // Retorna os erros caso os dados sejam inválidos
        if ($validator->fails()) {
            return $this->errorJson(
                'Os dados informados são inválidos.',
                422,
                $validator->errors()->toArray()
            );
        }

        // Mapeia os dados recebidos para os campos existentes no banco
        $dadosMapeados = [
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
            'fk_morador' => $request->input('morador'),
            'fk_funcionario' => $porteiro->pk_id_porteiro,
        ];

        // Cadastra o visitante no banco de dados
        $novoVisitante = Visitante::create($dadosMapeados);

        // Retorna os dados do visitante cadastrado
        return $this->responseJson(
            'Visitante criado com sucesso!',
            201,
            [
                'visitante' => new VisitanteResources($novoVisitante)
            ]
        );
    }

    // Função que retorna somente os visitantes do morador autenticado
    public function indexMorador(Request $request)
    {
        // Recupera o morador autenticado na requisição
        $morador = $request->user();

        // Busca somente os visitantes vinculados ao morador autenticado
        $visitantes = Visitante::where(
            'fk_morador',
            $morador->pk_id_morador
        )->get();

        // Retorna a lista de visitantes do morador
        return $this->responseJson(
            'Visitantes do morador recuperados com sucesso!',
            200,
            [
                'visitantes' => VisitanteResources::collection($visitantes)
            ]
        );
    }

    // Função que permite ao morador cadastrar um visitante
    public function storeMorador(Request $request)
    {
        // Recupera o morador autenticado na requisição
        $morador = $request->user();

        // Valida os dados enviados pelo morador
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante',
        ]);

        // Retorna os erros caso os dados sejam inválidos
        if ($validator->fails()) {
            return $this->errorJson(
                'Os dados informados são inválidos.',
                422,
                $validator->errors()->toArray()
            );
        }

        // Mapeia os dados e associa o visitante ao morador autenticado
        $dadosMapeados = [
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
            'fk_morador' => $morador->pk_id_morador,
            'fk_funcionario' => null,
        ];

        // Cadastra o visitante no banco de dados
        $novoVisitante = Visitante::create($dadosMapeados);

        // Retorna os dados do visitante cadastrado
        return $this->responseJson(
            'Visitante cadastrado com sucesso!',
            201,
            [
                'visitante' => new VisitanteResources($novoVisitante)
            ]
        );
    }

    // Função que retorna um visitante específico
    public function show(string $id)
    {
        // Busca o visitante pelo ID
        $visitante = Visitante::findOrFail($id);

        // Retorna os dados do visitante encontrado
        return $this->responseJson(
            'Visitante recuperado com sucesso!',
            200,
            [
                'visitante' => new VisitanteResources($visitante)
            ]
        );
    }

    // Função que atualiza os dados de um visitante
    public function update(Request $request, string $id)
    {
        // Busca o visitante que será atualizado
        $visitante = Visitante::findOrFail($id);

        // Valida os dados enviados para a atualização
        $validator = Validator::make($request->all(), [
            'nome' => 'required|string|max:100',
            'cpf' => 'required|string|max:14|unique:visitantes,cpf_visitante,' . $id . ',pk_id_visitante',
            'morador' => 'required|integer|exists:moradors,pk_id_morador',
        ]);

        // Retorna os erros caso os dados sejam inválidos
        if ($validator->fails()) {
            return $this->errorJson(
                'Os dados informados são inválidos.',
                422,
                $validator->errors()->toArray()
            );
        }

        // Mapeia os dados recebidos para os campos do banco
        $dadosMapeados = [
            'nome_visitante' => $request->input('nome'),
            'cpf_visitante' => $request->input('cpf'),
            'fk_morador' => $request->input('morador'),
        ];

        // Atualiza o visitante
        $atualizado = $visitante->update($dadosMapeados);

        // Verifica se a atualização foi realizada
        if (!$atualizado) {
            return $this->errorJson(
                'Não foi possível atualizar o visitante.',
                400
            );
        }

        // Retorna os dados atualizados do visitante
        return $this->responseJson(
            'Visitante atualizado com sucesso!',
            200,
            [
                'visitante' => new VisitanteResources($visitante->refresh())
            ]
        );
    }

    // Função que remove um visitante
    public function destroy(string $id)
    {
        // Busca o visitante que será removido
        $visitante = Visitante::findOrFail($id);

        // Remove o visitante do banco de dados
        $deletado = $visitante->delete();

        // Verifica se a exclusão foi realizada
        if (!$deletado) {
            return $this->errorJson(
                'Não foi possível excluir o visitante.',
                400
            );
        }

        // Retorna a confirmação da exclusão
        return $this->responseJson(
            'Visitante excluído com sucesso!',
            200
        );
    }
}
