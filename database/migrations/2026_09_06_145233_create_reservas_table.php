<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id('pk_id_reserva');

            // Relacionamento com morador
            $table->unsignedBigInteger('fk_id_morador');

            // Relacionamento com espaço
            $table->unsignedBigInteger('fk_id_espaco');

            $table->date('data_reserva');

            $table->time('hora_inicio');

            $table->time('hora_fim');

            $table->string('status_reserva')->default('confirmada');

            // Foreign key do morador
            $table->foreign('fk_id_morador')
                ->references('pk_id_morador')
                ->on('moradors')
                ->onDelete('cascade');

            // Foreign key do espaço
            $table->foreign('fk_id_espaco')
                ->references('pk_id_espaco')
                ->on('espacos')
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};