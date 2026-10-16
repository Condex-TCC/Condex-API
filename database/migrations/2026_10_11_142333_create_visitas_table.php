
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

            $table->foreign('fk_visitante')
                ->references('pk_id_visitante')
                ->on('visitantes');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visitas');
    }
};