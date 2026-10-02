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
use App\Http\Controllers\ReservaController;
use App\Http\Controllers\ComunicadoController;
use App\Http\Controllers\EnvioController;
use App\Http\Controllers\ReacaoController;
use App\Http\Controllers\UnidadeController;
use App\Models\Envio;

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
        ->prefix('/sindico')
        ->group(function () {


        // =========================
        // MORADORES
        // =========================

        Route::prefix('morador')->group(function () {

            // Recuperar todos os moradores
            Route::get('/get', [MoradorController::class, 'index']);

            // Recuperar um morador específico
            Route::get('/show/{id}', [MoradorController::class, 'show']);

            // Verifica se já tem morador cadastrado com apertamento
            Route::get('/verify/{id}', [MoradorController::class, 'verify']);

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
        Route::prefix('/comunicado')->group(function () {

            //Pegando todos os comunicados do sindico
            Route::get('/get', [ComunicadoController::class, 'indexSindico']);

            // Síndico cadastra um comunicado e realiza o envio para os moradores
            Route::post('/create', [EnvioController::class,'store']);

            //O sindico consegue ver os moradores e as suas interações com os envios
            Route::get("/show/{id}", [EnvioController::class, 'showEnvios']);

            //O Sindico recupera os dados do envio espefico do moarador
            Route::get('/show/detalhes/{id}', [EnvioController::class, 'showDetalhesEnvio']);

            //O Sindico recupera as duvidas dos moradores ainda sem resposta
            Route::get('/perguntas', [EnvioController::class, 'showSemResposta']);

            //O Sindico realiza um updade na tabela de envios para cadastrar a contra resposta
            Route::put('/contraResposta/{id}', [EnvioController::class, 'updadeContraResposta']);
        });


        // =========================
        // UNIDADES
        // =========================

        Route::prefix("/unidade")->group(function() {

            // Recuperar todas as unidades
            Route::get('/get', [UnidadeController::class, 'index']);

            // Recuperar uma unidade específico
            Route::get('/show/{id}', [UnidadeController::class, 'show']);

            // Criar uma unidade
            Route::post('/create', [UnidadeController::class, 'store']);

            // Atualizar uma unidade
            Route::put('/update/{id}', [UnidadeController::class, 'update']);

            // Deletar uma unidade
            Route::delete('/delete/{id}', [UnidadeController::class, 'destroy']);
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
        // COMUNICADOS
        // =========================
        Route::prefix('/comunicado')->group(function () {

            //Pegando todos os comunicados do morador | Histórico
            Route::get('/historico/get', [EnvioController::class, 'indexMorador']);

            //Pegando todos os comunicados não visualidos do morador
            Route::get('/get', [EnvioController::class, 'indexMoradorNaoVisualizado']);

            //Pegando os dados do envio | Merca como visualizado
            Route::post("/show/{id}", [EnvioController::class, 'showMorador']);

            //O Sindico realiza um updade na tabela de envios para cadastrar a contra resposta
            Route::put('/resposta/{id}', [EnvioController::class, 'updadeResposta']);
        });


        // =========================
        // ENCOMENDAS
        // =========================
        Route::prefix('encomenda')->group(function () {

            // Morador pode ver apenas suas encomendas
            Route::get('/get', [
                EncomendaController::class,
                'indexMorador'
            ]);

            // Morador pode ver apenas uma de suas encomendas
            Route::get('/show/{id}', [
                EncomendaController::class,
                'showMorador'
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


        // =========================
        // RESERVAS
        // =========================

        Route::prefix('reserva')->group(function () {

            // Morador visualiza suas reservas
            Route::get('/get', [
                ReservaController::class,
                'index'
            ]);

            // Morador visualiza uma reserva específica
            Route::get('/show/{id}', [
                ReservaController::class,
                'show'
            ]);

            // Morador cria uma reserva
            Route::post('/create', [
                ReservaController::class,
                'store'
            ]);

            // Morador atualiza uma reserva
            Route::put('/update/{id}', [
                ReservaController::class,
                'update'
            ]);

            // Morador cancela uma reserva
            Route::delete('/delete/{id}', [
                ReservaController::class,
                'destroy'
            ]);

        });

            // =========================
            // VISITANTES
            // =========================

            Route::prefix('visitante')->group(function () {

            // Morador cadastra um visitante
            Route::post('/create', [
                VisitanteController::class,
                'storeMorador'
            ]);

            Route::get('/visitante/get', [
                VisitanteController::class, 
                'indexMorador'
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


    });

});