<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('autorizacoes_visitantes', function (Blueprint $table) {
            $table->id('pk_id_autorizacao');

            $table->unsignedBigInteger('fk_id_visitante');
            $table->unsignedBigInteger('fk_id_morador');

            $table->string('status')->default('pendente');

            $table->dateTime('data_autorizacao')->nullable();
            $table->dateTime('entrada_em')->nullable();
            $table->dateTime('saida_em')->nullable();

            $table->timestamps();

            $table->foreign('fk_id_visitante')
                ->references('pk_id_visitante')
                ->on('visitantes')
                ->onDelete('cascade');

            $table->foreign('fk_id_morador')
                ->references('pk_id_morador')
                ->on('moradores')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('autorizacoes_visitantes');
    }
};