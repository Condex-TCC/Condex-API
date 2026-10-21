<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reclamacoes', function (Blueprint $table) {
            $table->id("pk_id_reclamacao"); //primary key
            $table->text("reclamacao"); //Campo que armazena a reclamação do morador
            $table->text("resposta_reclamacao")->nullable(); //Campo que armazena a resposta do sindico
            $table->boolean("visualizado_reclamacao")->default(false); //Campo que marca se o sindico visualizou a reclamação
            
            //Campos das chaves estrangeiras
            $table->unsignedBigInteger("fk_id_morador_reclamacao");
            $table->unsignedBigInteger("fk_id_sindico_reclamacao");

            //Fazendo os relacionamentos
            $table->foreign("fk_id_morador_reclamacao")->references("pk_id_morador")->on("moradors");
            $table->foreign("fk_id_sindico_reclamacao")->references("pk_id_sindico")->on("sindicos");


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reclamacoes');
    }
};
