<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Função que cria as migrations
    public function up(): void
    {
        Schema::create('porteiros', function (Blueprint $table) {
            $table->id("pk_id_porteiro"); //Chave primaria
            $table->string("email_porteiro", 150)->unique(); //Campo de email
            $table->string("senha_porteiro"); //Campo de senha
            $table->string("nome_porteiro")->nullable(); //Campo de nome
            $table->string("telefone_porteiro")->nullable(); //Campo de telefone
            $table->string("cpf_porteiro")->nullable(); //Campo de cpf
            $table->boolean("primeiro_acesso_porteiro")->default(false);
            $table->boolean("codigo_senha_porterio")->nullable()->default(null);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('porteiros');
    }
};
