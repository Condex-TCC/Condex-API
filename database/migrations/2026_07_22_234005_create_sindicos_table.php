<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Função que cria as migrations
    public function up(): void
    {
        Schema::create('sindicos', function (Blueprint $table) {
            $table->id("pk_id_sindico"); //Crinado o ID do sindico
            $table->string("nome_sindico", 150); //Campo de nome
            $table->string("telefone_sindico", 20); //Campo de telefone
            $table->string("email_sindico", 200)->unique(); //Campo de email do sindico
            $table->string("senha_sindico"); //Campo de senha do sindico
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sindicos');
    }
};
