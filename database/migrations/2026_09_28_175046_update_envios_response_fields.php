<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('envios', function (Blueprint $table) {
            $table->dropForeign(['fk_id_resposta']);
            $table->dropForeign(['fk_id_contra_resposta']);

            $table->dropColumn([
                'fk_id_resposta',
                'fk_id_contra_resposta',
            ]);

            $table->text('resposta')
                ->nullable();

            $table->text('contra_resposta')
                ->nullable();

            $table->boolean('visualizado')
                ->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('envios', function (Blueprint $table) {
            $table->unsignedBigInteger('fk_id_resposta')
                ->nullable();

            $table->unsignedBigInteger('fk_id_contra_resposta')
                ->nullable();

            $table->foreign('fk_id_resposta')
                ->references('pk_id_resposta')
                ->on('respostas')
                ->onDelete('cascade');

            $table->foreign('fk_id_contra_resposta')
                ->references('pk_id_contra_resposta')
                ->on('contra_respostas')
                ->onDelete('set null');

            $table->dropColumn([
                'resposta',
                'contra_resposta',
                'visualizado',
            ]);
        });
    }
};