<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contra_respostas', function (Blueprint $table) {

            $table->id('pk_id_contra_resposta');

            $table->string('descricao_contra_resposta', 255);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contra_respostas');
    }
};