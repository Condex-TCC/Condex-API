<?php

use App\Http\Controllers\LaudoController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MoradorController;
use App\Http\Controllers\PorteiroController;
use App\Http\Controllers\RegrasController;
use App\Http\Controllers\VisitanteController;
use App\Http\Controllers\EncomendaController;
use App\Http\Controllers\AutorizacaoVisitanteController;
use App\Http\Controllers\EspacoController;
use App\Http\Controllers\SindicoController;
use Illuminate\Support\Facades\Route;

//Rota de login
Route::post('/login', [LoginController::class, 'login']);

//Rota para deslogar | Rota protejada de pelo auth:sanctum para deslogar apenas o usuário logado
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');

//Definindo um grupo de rotas para apenas os usuário que estão logados(tem o token) 
Route::middleware("auth:sanctum")->group(function() {

    //Para acessar esse grupo de rotas é necessário ter a habilidade do token de sindico
    Route::middleware("ability:sindico")->prefix("/sindico")->group(function() {

        // Grupo de rotas para realizar o CRUD do síndico
        Route::prefix("/gerenciar")->group(function() {

        // Recuperar todos os síndicos
        Route::get("/get", [SindicoController::class, "index"]);

        // Recuperar um síndico específico
        Route::get("/show/{id}", [SindicoController::class, "show"]);

        // Criar um síndico
        Route::post("/create", [SindicoController::class, "store"]);

        // Atualizar um síndico
        Route::put("/update/{id}", [SindicoController::class, "update"]);

        // Deletar um síndico
        Route::delete("/delete/{id}", [SindicoController::class, "destroy"]);

        });

        //Grupo de rotas para relizar o CRUD do morador
        Route::prefix("/morador")->group(function() {

            //Rota para recuperar todos os moradores
            Route::get("/get", [MoradorController::class, "index"]);

            //Rota que recupera apenas um morador
            Route::get("/show/{id}", [MoradorController::class, 'show']);

            //Rota para criar um morador
            Route::post("/create", [MoradorController::class, 'store']);

            //Rota para atualizar um morador
            Route::put("/update/{id}", [MoradorController::class, 'update']);
            
            //Rota para deletar um morador
            Route::delete("/delete/{id}", [MoradorController::class, 'destroy']);
        });

        //Grupo de rotas para relizar o CRUD do porteiro
        Route::prefix("/porteiro")->group(function() {

            //Rota para recuperar todos os porteiros
            Route::get("/get", [PorteiroController::class, "index"]);

            //Rota que recupera apenas um morador
            Route::get("/show/{id}", [PorteiroController::class, 'show']);

            //Rota para criar um porteiro
            Route::post("/create", [PorteiroController::class, 'store']);

            //Rota para atualizar um porteiro
            Route::put("/update/{id}", [PorteiroController::class, 'update']);
            
            //Rota para deletar um porteiro
            Route::delete("/delete/{id}", [PorteiroController::class, 'destroy']);

        });

        //Grupo de rotas para realizar CRUD nas regras
        Route::prefix("/regras")->group(function() {

            //Rota para recuperar todos os porteiros
            Route::get("/get", [RegrasController::class, "index"]);

            //Rota que recupera apenas uma regra
            Route::get('/show/{id}', [RegrasController::class, 'show']);

            //Rota para criar um porteiro
            Route::post("/create", [RegrasController::class, 'store']);

            //Rota para atualizar um porteiro
            Route::put("/update/{id}", [RegrasController::class, 'update']);
            
            //Rota para deletar um porteiro
            Route::delete("/delete/{id}", [RegrasController::class, 'destroy']);

        });


        // Grupo de rotas para realizar CRUD nos espaços
        Route::prefix("espaco")->group(function() {

           // Rota para recuperar todos os espaços
           Route::get("/get", [EspacoController::class, "index"]);

           // Rota para recuperar um espaço específico
           Route::get("/get/{id}", [EspacoController::class, "show"]);

           // Rota para criar um espaço
           Route::post("/create", [EspacoController::class, "store"]);

           // Rota para atualizar um espaço
           Route::put("/update/{id}", [EspacoController::class, "update"]);

          // Rota para deletar um espaço
          Route::delete("/delete/{id}", [EspacoController::class, "destroy"]);

        });

        //Grupo de rotas para realizar CRUD dos laudos
        Route::prefix("/laudos")->group(function() {

            //Rota para recuperar todos os porteiros
            Route::get("/get", [LaudoController::class, "index"]);

            //Rota para criar um porteiro
            Route::post("/create", [LaudoController::class, 'store']);

            //Rota para atualizar um porteiro
            //Essa rota, por mais que seja de atualização, para trabalhar com arquivos o PHP/Laravel só aceita o POST
            Route::post("/update/{id}", [LaudoController::class, 'update']);
            
            //Rota para deletar um porteiro
            Route::delete("/delete/{id}", [LaudoController::class, 'destroy']);

        });

    });


    // Para acessar esse grupo é necessário ter a habilidade de token de morador
    Route::middleware("ability:morador")->prefix("/morador")->group(function() {

    Route::prefix("/autorizacao")->group(function() {

    // Morador autoriza um visitante
    Route::post("/create", [
        AutorizacaoVisitanteController::class,
        "authorizeVisitor"
    ]);

    });

    // morador pode ver suas encomendas
    Route::prefix("encomenda")->group(function() {

        Route::get("/get", [EncomendaController::class, "index"]);

        Route::get("/get/{id}", [EncomendaController::class, "show"]);

    });

    // morador pode ver as regras
    Route::prefix("regras")->group(function() {

        Route::get("/get", [RegrasController::class, "index"]);

    });

    });


    // Para acessar esse grupo é necessário ter a habilidade do token de porteiro
    Route::middleware("ability:porteiro")->prefix("/porteiro")->group(function() {

    Route::prefix("autorizacao")->group(function() {

    // Consultar visitantes autorizados
    Route::get("/authorized", [AutorizacaoVisitanteController::class,"getAuthorizedVisitors"]);

    // Liberar entrada
    Route::put("/entry/{id}", [AutorizacaoVisitanteController::class,"allowEntry"]);

    // Registrar saída
    Route::put("/exit/{id}", [AutorizacaoVisitanteController::class,"registerExit"]);

    });

    // Moradores
    Route::prefix("morador")->group(function() {

        Route::get("/get", [MoradorController::class, "index"]);

    });

    // Visitantes
    Route::prefix("visitante")->group(function() {

        Route::get("/get", [VisitanteController::class, "index"]);

        Route::get("/get/{id}", [VisitanteController::class, "show"]);

        Route::post("/create", [VisitanteController::class, "store"]);

        Route::put("/update/{id}", [VisitanteController::class, "update"]);

        Route::delete("/delete/{id}", [VisitanteController::class, "destroy"]);

    });


    
    // Encomendas
    Route::prefix("encomenda")->group(function() {

    Route::get("/get", [EncomendaController::class, "index"]);

    Route::post("/create", [EncomendaController::class, "store"]);

    Route::put("/update/{id}", [EncomendaController::class, "update"]);

    Route::delete("/delete/{id}", [EncomendaController::class, 'destroy']);

    // Registrar retirada de uma encomenda
    Route::put("/withdraw/{id}", [
        EncomendaController::class,
        "registerWithdrawal"
    ]);

    });

});
});