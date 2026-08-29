<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\SindicoController;
use App\Http\Controllers\MoradorController;
use App\Http\Controllers\PorteiroController;
use App\Http\Controllers\VisitanteController;
use App\Http\Controllers\EncomendaController;
use App\Http\Controllers\AutorizacaoVisitanteController;
use App\Http\Controllers\RegrasController;
use App\Http\Controllers\LaudoController;
use App\Http\Controllers\EspacoController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\ReacaoController;


/*
|--------------------------------------------------------------------------
| LOGIN
|--------------------------------------------------------------------------
*/

// Rota de login
Route::post('/login', [LoginController::class, 'login']);

// Rota para deslogar
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth:sanctum');


/*
|--------------------------------------------------------------------------
| ROTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {


    /*
    |--------------------------------------------------------------------------
    | SÍNDICO
    |--------------------------------------------------------------------------
    */

    Route::middleware('ability:sindico')
        ->prefix('sindico')
        ->group(function () {


        // =========================
        // CRUD SÍNDICO
        // =========================

        Route::prefix('gerenciar')->group(function () {

            // Recuperar todos os síndicos
            Route::get('/get', [SindicoController::class, 'index']);

            // Recuperar um síndico específico
            Route::get('/show/{id}', [SindicoController::class, 'show']);

            // Criar um síndico
            Route::post('/create', [SindicoController::class, 'store']);

            // Atualizar um síndico
            Route::put('/update/{id}', [SindicoController::class, 'update']);

            // Deletar um síndico
            Route::delete('/delete/{id}', [SindicoController::class, 'destroy']);
        });


        // =========================
        // MORADORES
        // =========================

        Route::prefix('morador')->group(function () {

            // Recuperar todos os moradores
            Route::get('/get', [MoradorController::class, 'index']);

            // Recuperar um morador específico
            Route::get('/show/{id}', [MoradorController::class, 'show']);

            // Criar um morador
            Route::post('/create', [MoradorController::class, 'store']);

            // Atualizar um morador
            Route::put('/update/{id}', [MoradorController::class, 'update']);

            // Deletar um morador
            Route::delete('/delete/{id}', [MoradorController::class, 'destroy']);
        });


        // =========================
        // PORTEIROS
        // =========================

        Route::prefix('porteiro')->group(function () {

            // Recuperar todos os porteiros
            Route::get('/get', [PorteiroController::class, 'index']);

            // Recuperar um porteiro específico
            Route::get('/show/{id}', [PorteiroController::class, 'show']);

            // Criar um porteiro
            Route::post('/create', [PorteiroController::class, 'store']);

            // Atualizar um porteiro
            Route::put('/update/{id}', [PorteiroController::class, 'update']);

            // Deletar um porteiro
            Route::delete('/delete/{id}', [PorteiroController::class, 'destroy']);
        });


        // =========================
        // REGRAS
        // =========================

        Route::prefix('regras')->group(function () {

            // Recuperar todas as regras
            Route::get('/get', [RegrasController::class, 'index']);

            // Recuperar uma regra específica
            Route::get('/show/{id}', [RegrasController::class, 'show']);

            // Criar uma regra
            Route::post('/create', [RegrasController::class, 'store']);

            // Atualizar uma regra
            Route::put('/update/{id}', [RegrasController::class, 'update']);

            // Deletar uma regra
            Route::delete('/delete/{id}', [RegrasController::class, 'destroy']);
        });


        // =========================
        // ESPAÇOS COMUNS
        // =========================

        Route::prefix('espaco')->group(function () {

            // Recuperar todos os espaços
            Route::get('/get', [EspacoController::class, 'index']);

            // Recuperar um espaço específico
            Route::get('/show/{id}', [EspacoController::class, 'show']);

            // Criar um espaço
            Route::post('/create', [EspacoController::class, 'store']);

            // Atualizar um espaço
            Route::put('/update/{id}', [EspacoController::class, 'update']);

            // Deletar um espaço
            Route::delete('/delete/{id}', [EspacoController::class, 'destroy']);
        });


        // =========================
        // LAUDOS
        // =========================

        Route::prefix('laudos')->group(function () {

            // Recuperar todos os laudos
            Route::get('/get', [LaudoController::class, 'index']);

            // Recuperar um laudo específico
            Route::get('/show/{id}', [LaudoController::class, 'show']);

            // Criar um laudo
            Route::post('/create', [LaudoController::class, 'store']);

            // Atualização com arquivo
            Route::post('/update/{id}', [LaudoController::class, 'update']);

            // Deletar um laudo
            Route::delete('/delete/{id}', [LaudoController::class, 'destroy']);
        });


        // =========================
        // COMUNICADOS
        // =========================

        Route::prefix('comunicado')->group(function () {

            // Síndico cadastra um comunicado
            Route::post('/create', [
                ComunicadoController::class,
                'store'
            ]);
        });


        // =========================
        // RESPOSTAS DOS MORADORES
        // =========================

        Route::prefix('respostas')->group(function () {

            // Síndico visualiza as respostas dos moradores
            Route::get('/get', [
                EnvioController::class,
                'index'
            ]);

            // Síndico cadastra uma contra-resposta
            Route::post('/create/{id}', [
                EnvioController::class,
                'store'
            ]);
        });

    });


    /*
    |--------------------------------------------------------------------------
    | MORADOR
    |--------------------------------------------------------------------------
    */

    Route::middleware('ability:morador')
        ->prefix('morador')
        ->group(function () {

        // =========================
        // REAÇÕES
        // =========================

        Route::prefix('reacao')->group(function () {

            // Morador reage a um comunicado
            Route::post('/create/{id}', [
                ReacaoController::class,
                'store'
            ]);

            // Morador visualiza sua reação em um comunicado
            Route::get('/show/{id}', [
                ReacaoController::class,
                'show'
            ]);
        });
   


        // =========================
        // COMUNICADOS
        // =========================

        Route::prefix('comunicado')->group(function () {

            // Morador visualiza todos os comunicados
            Route::get('/get', [
                ComunicadoController::class,
                'index'
            ]);

            // Morador visualiza um comunicado específico
            Route::get('/show/{id}', [
                ComunicadoController::class,
                'show'
            ]);
        });

        // =========================
        // RESPOSTAS DOS COMUNICADOS
        // =========================

        Route::prefix('resposta')->group(function () {

            // Morador responde um comunicado
            Route::post('/create/{id}', [
                EnvioController::class,
                'respond'
            ]);
        });

        // =========================
        // AUTORIZAÇÃO DE VISITANTES
        // =========================

        Route::prefix('autorizacao')->group(function () {

            // Morador autoriza um visitante
            Route::post('/create', [
                AutorizacaoVisitanteController::class,
                'authorizeVisitor'
            ]);
        });


        // =========================
        // ENCOMENDAS
        // =========================

        Route::prefix('encomenda')->group(function () {

            // Morador pode ver suas encomendas
            Route::get('/get', [
                EncomendaController::class,
                'index'
            ]);

            Route::get('/show/{id}', [
                EncomendaController::class,
                'show'
            ]);
        });


        // =========================
        // REGRAS
        // =========================

        Route::prefix('regras')->group(function () {

            // Morador pode visualizar as regras
            Route::get('/get', [
                RegrasController::class,
                'index'
            ]);

            Route::get('/show/{id}', [
                RegrasController::class,
                'show'
            ]);
        });


        // =========================
        // LAUDOS
        // =========================

        Route::prefix('laudos')->group(function () {

            // Morador pode visualizar os laudos
            Route::get('/get', [
                LaudoController::class,
                'index'
            ]);

            Route::get('/show/{id}', [
                LaudoController::class,
                'show'
            ]);
        });


        // =========================
        // ESPAÇOS
        // =========================

        Route::prefix('espaco')->group(function () {

            // Morador pode visualizar os espaços
            Route::get('/get', [
                EspacoController::class,
                'index'
            ]);

            Route::get('/show/{id}', [
                EspacoController::class,
                'show'
            ]);
        });

    });


    /*
    |--------------------------------------------------------------------------
    | PORTEIRO
    |--------------------------------------------------------------------------
    */

    Route::middleware('ability:porteiro')
        ->prefix('porteiro')
        ->group(function () {


        // =========================
        // AUTORIZAÇÃO DE VISITANTES
        // =========================

        Route::prefix('autorizacao')->group(function () {

            // Consultar visitantes autorizados
            Route::get('/authorized', [
                AutorizacaoVisitanteController::class,
                'getAuthorizedVisitors'
            ]);

            // Liberar entrada
            Route::put('/entry/{id}', [
                AutorizacaoVisitanteController::class,
                'allowEntry'
            ]);

            // Registrar saída
            Route::put('/exit/{id}', [
                AutorizacaoVisitanteController::class,
                'registerExit'
            ]);
        });


        // =========================
        // MORADORES
        // =========================

        Route::prefix('morador')->group(function () {

            // Consultar moradores
            Route::get('/get', [
                MoradorController::class,
                'index'
            ]);

            Route::get('/show/{id}', [
                MoradorController::class,
                'show'
            ]);
        });


        // =========================
        // VISITANTES
        // =========================

        Route::prefix('visitante')->group(function () {

            Route::get('/get', [
                VisitanteController::class,
                'index'
            ]);

            Route::get('/show/{id}', [
                VisitanteController::class,
                'show'
            ]);

            Route::post('/create', [
                VisitanteController::class,
                'store'
            ]);

            Route::put('/update/{id}', [
                VisitanteController::class,
                'update'
            ]);

            Route::delete('/delete/{id}', [
                VisitanteController::class,
                'destroy'
            ]);
        });


        // =========================
        // ENCOMENDAS
        // =========================

        Route::prefix('encomenda')->group(function () {

            Route::get('/get', [
                EncomendaController::class,
                'index'
            ]);

            Route::post('/create', [
                EncomendaController::class,
                'store'
            ]);

            Route::put('/update/{id}', [
                EncomendaController::class,
                'update'
            ]);

            Route::delete('/delete/{id}', [
                EncomendaController::class,
                'destroy'
            ]);

            // Registrar retirada de uma encomenda
            Route::put('/withdraw/{id}', [
                EncomendaController::class,
                'registerWithdrawal'
            ]);
        });


        // =========================
        // REGRAS
        // =========================

        Route::prefix('regras')->group(function () {

            // Porteiro pode consultar as regras
            Route::get('/get', [
                RegrasController::class,
                'index'
            ]);

            Route::get('/show/{id}', [
                RegrasController::class,
                'show'
            ]);
        });


        // =========================
        // LAUDOS
        // =========================

        Route::prefix('laudos')->group(function () {

            // Porteiro pode consultar os laudos
            Route::get('/get', [
                LaudoController::class,
                'index'
            ]);

            Route::get('/show/{id}', [
                LaudoController::class,
                'show'
            ]);
        });

    });

});