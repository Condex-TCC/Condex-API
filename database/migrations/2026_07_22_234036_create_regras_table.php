<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Função que cria as migrations
    public function up(): void
    {
        Schema::create('regras', function (Blueprint $table) {
            $table->id("pk_id_regra"); //Chave primária
            $table->string("nome_regra", 255); //Campo de nome da regra
            $table->text("descricao_regra"); //Campo que descreve a regra
            $table->unsignedBigInteger("fk_id_sindico_regras"); //Campo que irá armazenar o id do sindico

            //Fazendo o relacionanmento com o sindico
            $table->foreign("fk_id_sindico_regras")->references("pk_id_sindico")->on("sindicos");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('regras');
    }
};
