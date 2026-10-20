<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    //Função que cria as migrations
    public function up(): void
    {
        Schema::create('moradors', function (Blueprint $table) {
            $table->id("pk_id_morador"); //Gera uma chave primária
            $table->string("nome_morador", 100); //Campo de nome do morador
            $table->string("cpf_morador", 14); //Campo de cpf do morador
            $table->string("email_morador", 150); //Campo de email do morador
            $table->string("telefone_morador", 20); //Campo do telefone do morador
            $table->string("senha_morador"); //Campo de senha do morador

            //Campo que verifica se foi o primeiro acesso do usuário
            $table->boolean("primeiro_acesso_morador")->default(false);

            //Campo que armazena o código para redefinir a senha do moraodor
            $table->boolean("codigo_senha_morador")->nullable()->default(null);

            $table->unsignedBigInteger("fk_id_unidade_morador"); //Campo que irá armazenar o relacionanemtno com unidade

            //Fazendo o relacionamento com a unidade
            $table->foreign("fk_id_unidade_morador")->references("pk_id_unidades")->on("unidades"); 


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moradors');
    }
};
