
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id('pk_id_visita'); //Campo da chave primária

            //Hora de entrada e saida prevista
            $table->dateTime('hora_entrada_visita');
            $table->dateTime('hora_saida_visita');

            //Campo de autorização
            $table->boolean("autorizacao_visita")->nullable()->default(null);

            //Chaves primárias do vitante e morador que irá recever a visita
            $table->unsignedBigInteger('fk_id_visitante');
            $table->unsignedBigInteger('fk_id_morador');

            //Hora do registro real da entrada do visitante do banco de dados
            $table->dateTime('hora_registro_entrada_visita')->nullable();
            $table->unsignedBigInteger("fk_id_porteiro_entrada")->nullable();

            $table->dateTime('hora_registro_saida_visita')->nullable();
            $table->unsignedBigInteger("fk_id_porteiro_saida")->nullable();

            //Ligando os campos tas chaves primárias
            $table->foreign("fk_id_morador")->references("pk_id_morador")->on('moradors');
            $table->foreign('fk_id_visitante')->references('pk_id_visitante')->on("visitantes");
            $table->foreign("fk_id_porteiro_entrada")->references("pk_id_porteiro")->on("porteiros");
            $table->foreign("fk_id_porteiro_saida")->references("pk_id_porteiro")->on("porteiros");
           

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
