<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respostas', function (Blueprint $table) {

            $table->id('pk_id_resposta');

            $table->unsignedBigInteger('fk_id_morador');

            $table->string('descricao_resposta', 255);

            $table->foreign('fk_id_morador')
                ->references('pk_id_morador')
                ->on('moradors')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respostas');
    }
};