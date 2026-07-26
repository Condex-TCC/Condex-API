<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\MoradorController;
use App\Http\Controllers\PorteiroController;
use App\Http\Controllers\RegrasController;
use Illuminate\Support\Facades\Route;

//Rota de login
Route::post('/login', [LoginController::class, 'login']);

//Rota para deslogar | Rota protejada de pelo auth:sanctum para deslogar apenas o usuário logado
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

//Definindo um grupo de rotas para apenas os usuário que estão logados(tem o token) 
Route::middleware("auth:sanctum")->group(function() {

    //Para acessar esse grupo de rotas é necessário ter a habilidade do token de sindico
    Route::middleware("ability:sindico")->prefix("/sindico")->group(function() {

        //Grupo de rotas para relizar o CRUD do morador
        Route::prefix("morador")->group(function() {

            //Rota para recuperar todos os moradores
            Route::get("/get", [MoradorController::class, "index"]);

            //Rota para criar um morador
            Route::post("/create", [MoradorController::class, 'store']);

            //Rota para atualizar um morador
            Route::put("/update/{id}", [MoradorController::class, 'update']);
            
            //Rota para deletar um morador
            Route::delete('/delete/{id}', [MoradorController::class, 'destroy']);
        });

        //Grupo de rotas para relizar o CRUD do porteiro
        Route::prefix("porteiro")->group(function() {

            //Rota para recuperar todos os porteiros
            Route::get("/get", [PorteiroController::class, "index"]);

            //Rota para criar um porteiro
            Route::post("/create", [PorteiroController::class, 'store']);

            //Rota para atualizar um porteiro
            Route::put("/update/{id}", [PorteiroController::class, 'update']);
            
            //Rota para deletar um porteiro
            Route::delete('/delete/{id}', [PorteiroController::class, 'destroy']);

        });

        //Grupo de rotas para realizar CRUD nas regras
        Route::prefix("/regras")->group(function() {

            //Rota para recuperar todos os porteiros
            Route::get("/get", [RegrasController::class, "index"]);

            //Rota para criar um porteiro
            Route::post("/create", [RegrasController::class, 'store']);

            //Rota para atualizar um porteiro
            Route::put("/update/{id}", [RegrasController::class, 'update']);
            
            //Rota para deletar um porteiro
            Route::delete('/delete/{id}', [RegrasController::class, 'destroy']);

        });

        //Grupo de rotas para realizar CRUD dos laudos
        Route::prefix("/laudos")->group(function() {

            //Rota para recuperar todos os porteiros
            Route::get("/get", [RegrasController::class, "index"]);

            //Rota para criar um porteiro
            Route::post("/create", [RegrasController::class, 'store']);

            //Rota para atualizar um porteiro
            Route::put("/update/{id}", [RegrasController::class, 'update']);
            
            //Rota para deletar um porteiro
            Route::delete('/delete/{id}', [RegrasController::class, 'destroy']);

        });

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