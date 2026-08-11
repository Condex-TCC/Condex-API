<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Função que cria as migrations
    public function up(): void
    {
        Schema::create('porteiros', function (Blueprint $table) {
            $table->id("pk_id_porteiro"); //Chave primaria
            $table->string("nome_porteiro"); //Campo de nome
            $table->string("email_porteiro", 150)->unique(); //Campo de email
            $table->string("senha_porteiro"); //Campo de senha
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('porteiros');
    }
};
