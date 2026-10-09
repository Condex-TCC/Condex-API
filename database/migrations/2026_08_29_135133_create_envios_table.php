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

            $table->unsignedBigInteger('fk_id_morador');

            $table->text('resposta')
                ->nullable();

            $table->text('contra_resposta')
                ->nullable();

            $table->boolean('visualizado')
                ->default(false);

            $table->foreign('fk_id_morador')
                ->references('pk_id_morador')
                ->on('moradors')
                ->onDelete('cascade');

            $table->foreign('fk_id_comunicados')
                ->references('pk_id_comunicados')
                ->on('comunicados')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('envios');
    }
};