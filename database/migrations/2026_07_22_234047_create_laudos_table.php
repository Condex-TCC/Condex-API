<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Função que cria as migrations
    public function up(): void
    {
        Schema::create('laudos', function (Blueprint $table) {
            $table->id("pk_id_laudos"); //Gera um chave primária
            $table->string("nome_laudo"); //Campo de nome do laudo
            $table->text("caminho_laudo"); //Campo de caminho do laudo
            $table->unsignedBigInteger("fk_id_sindico_laudo"); //Campo que irá armazenar o id do sindico

            //Fazendo o relacionanmento com o sindico
            $table->foreign("fk_id_sindico_laudo")->references("pk_id_sindico")->on("sindicos");

            $table->timestamps(); //Campo de data de criação
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laudos');
    }
};
