
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visitas', function (Blueprint $table) {
            $table->id('pk_id_visita');

            $table->dateTime('entrada')->nullable();
            $table->dateTime('saida')->nullable();

            $table->unsignedBigInteger('fk_visitante');
            $table->unsignedBigInteger('fk_morador');
            $table->unsignedBigInteger('fk_porteiro');

            // Relationship with visitor
            $table->foreign('fk_visitante')
                ->references('pk_id_visitante')
                ->on('visitantes');

            // Relationship with resident
            $table->foreign('fk_morador')
                ->references('pk_id_morador')
                ->on('moradors');

            // Relationship with doorman
            $table->foreign('fk_porteiro')
                ->references('pk_id_porteiro')
                ->on('porteiros');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};
