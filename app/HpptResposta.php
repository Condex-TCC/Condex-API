<?php

namespace App;

//É um tracho de código que pode ser herdado por qualquer outra classe
//Esse trecho de código tem como objetivo criar um retorno padronizado do json
trait HpptResposta
{
    //Função que formata a saída o array em casso de sucesso
    public function responseJson(string $message, int|string $status, array $data = []){

        //Retorna um Json com uma formatação padão para sucesso
        return response()->json([
            'message' => $message,
            'status' => $status,
            'data' => $data
        ], $status);
    }
    
    //Função que formata a saída do array em caso de sucesso
    public function errorJson(string $message, int|string $status, array $errors = [], array $data = []){

        //Retorna um Json com uma formatação padão para fracaço
        return response()->json([
            'message' => $message,
            'status' => $status,
            'errors' => $errors,
            'data' => $data
        ], $status);
    }
}
