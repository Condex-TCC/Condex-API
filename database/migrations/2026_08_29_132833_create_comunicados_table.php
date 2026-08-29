<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comunicados', function (Blueprint $table) {

            $table->id('pk_id_comunicados');

            $table->string('descricao_comunicado', 255);

            $table->unsignedBigInteger('fk_id_sindico_comunicados');

            $table->foreign('fk_id_sindico_comunicados')
                ->references('pk_id_sindico')
                ->on('sindicos')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comunicados');
    }
};