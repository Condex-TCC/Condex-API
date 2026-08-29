<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reacoes', function (Blueprint $table) {

            $table->id('pk_id_reacao');

            $table->unsignedBigInteger('fk_id_morador');

            $table->unsignedBigInteger('fk_id_comunicado');

            $table->string('tipo_reacao', 50);

            $table->foreign('fk_id_morador')
                ->references('pk_id_morador')
                ->on('moradors')
                ->onDelete('cascade');

            $table->foreign('fk_id_comunicado')
                ->references('pk_id_comunicados')
                ->on('comunicados')
                ->onDelete('cascade');

            // Impede o mesmo morador de reagir duas vezes
            // ao mesmo comunicado
            $table->unique([
                'fk_id_morador',
                'fk_id_comunicado'
            ]);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reacoes');
    }
};