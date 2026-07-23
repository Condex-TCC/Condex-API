<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Função que cria as migrations
    public function up(): void
    {
        Schema::create('unidades', function (Blueprint $table) {
            $table->id("pk_id_unidades"); //Gera uma chave primária
            $table->string("bloco_unidade", 10); //Campo do bloco da unidade
            $table->string("numero_unidade", 10); //Campo do bloco da unidade
            $table->unsignedBigInteger("fk_id_unidade_categoria"); //Gera um campo para o relacionamente com categoria
            
            //Fazendo o relacionanmento com categoria unidade
            $table->foreign("fk_id_unidade_categoria")->references("pk_id_categoria_unidade")->on("categoria_unidades");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades');
    }
};
