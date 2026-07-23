<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Função que cria as migrations
    public function up(): void
    {
        Schema::create('categoria_unidades', function (Blueprint $table) {
            $table->id("pk_id_categoria_unidade"); //Gera uma chave primária
            $table->text("descricao_categoria_unidade"); //Campo de descrição da unidade
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria_unidades');
    }
};
