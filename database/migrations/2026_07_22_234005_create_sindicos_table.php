<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Função que cria as migrations
    public function up(): void
    {
        Schema::create('sindicos', function (Blueprint $table) {
            $table->id("pk_id_sindico"); //Crinado o ID do sindico
            $table->string("email_sindico", 200)->unique(); //Campo de email do sindico
            $table->string("senha_sindico"); //Campo de senha do sindico

            $table->string("nome_sindico", 150)->nullable(); //Campo de nome
            $table->string("telefone_sindico", 20)->nullable(); //Campo de telefone
            $table->string("cpf_sindico")->nullable(); //cpf do sindico

            //Campo que verifica se foi o primeiro acesso do usuário
            $table->boolean("primeiro_acesso_sindico")->default(false);

            //Campo que armazena o código para redefinir a senha do sindico
            $table->string("codigo_senha_sindico")->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sindicos');
    }
};
