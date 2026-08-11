<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitantes', function (Blueprint $table) {
            $table->id('pk_id_visitante');

            $table->string('cpf_visitante', 14)->unique();
            $table->string('nome_visitante', 100);

            $table->unsignedBigInteger('fk_morador');
            $table->unsignedBigInteger('fk_funcionario');

            // Relationship with resident
            $table->foreign('fk_morador')
                ->references('pk_id_morador')
                ->on('moradors');

            // Relationship with doorman
            $table->foreign('fk_funcionario')
                ->references('pk_id_porteiro')
                ->on('porteiros');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitantes');
    }
};