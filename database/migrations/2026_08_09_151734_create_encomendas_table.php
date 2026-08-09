<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Função que cria as migrations
    public function up(): void
    {
        Schema::create('encomenda', function (Blueprint $table) {
            $table->id("pk_id_encomenda"); //Chave primária
            $table->string("nome_encomenda", 255); //Campo de nome da encomenda
            $table->text("descricao_encomenda"); //Campo que descreve a encomenda
            $table->unsignedBigInteger("fk_id_porteiro_encomenda"); //Campo que irá armazenar o id do porteiro

            //Fazendo o relacionanmento com o porteiro
            $table->foreign("fk_id_porteiro_encomenda")->references("pk_id_porteiro")->on("porteiros");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('encomenda');
    }
};
