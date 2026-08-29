<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('envios', function (Blueprint $table) {

            $table->id('pk_id_envio');

            $table->unsignedBigInteger('fk_id_comunicados');

            $table->unsignedBigInteger('fk_id_resposta');

            $table->unsignedBigInteger('fk_id_contra_resposta')
                ->nullable();

            $table->foreign('fk_id_comunicados')
                ->references('pk_id_comunicados')
                ->on('comunicados')
                ->onDelete('cascade');

            $table->foreign('fk_id_resposta')
                ->references('pk_id_resposta')
                ->on('respostas')
                ->onDelete('cascade');

            $table->foreign('fk_id_contra_resposta')
                ->references('pk_id_contra_resposta')
                ->on('contra_respostas')
                ->onDelete('set null');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};