<?php

use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Rota de login
Route::post('/login', [LoginController::class, 'login']);

//Rota para deslogar | Rota protejada de pelo auth:sanctum para deslogar apenas o usuário logado
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

//Definindo um grupo de rotas para apenas os usuário que estão logados(tem o token) 
Route::middleware("auth:sanctum")->group(function() {

    //Para acessar esse grupo de rotas é necessário ter a habilidade do token de sindico
    Route::middleware("ability:sindico")->prefix("/sindico")->group(function() {

        //Rotas do sindico

    });

    //Para acessar esse grupo de rotas é necessário ter a habilidade do token de morador
    Route::middleware("ability:morador")->prefix("/morador")->group(function() {

        //Rotas do morador

    });

    //Para acessar esse grupo de rotas é necessário ter a habilidade do token de porteiro
    Route::middleware("ability:porteiro")->prefix("/porteiro")->group(function() {

        //Rotas do porteiro

    });
});