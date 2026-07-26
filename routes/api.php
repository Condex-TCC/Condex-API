<?php

use App\Http\Controllers\LoginController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Rota de login
Route::post('/login', [LoginController::class, 'login']);

//Rota para deslogar | Rota protejada de pelo auth:sanctum para deslogar apenas o usuário logado
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');