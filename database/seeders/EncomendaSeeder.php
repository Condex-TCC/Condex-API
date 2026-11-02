<?php

namespace Database\Seeders;

use App\Models\Encomenda;
use App\Models\Morador;
use Illuminate\Database\Seeder;

class EncomendaSeeder extends Seeder
{
    public function run(): void
    {
        $moradores = Morador::orderBy('pk_id_morador')->get();

        foreach ($moradores as $morador) {
            //Uma encomenda já retirada pelo morador
            $retirada = Encomenda::factory()->retirada()->create([
                'fk_id_morador_encomenda' => $morador->pk_id_morador,
            ]);

            //Ajusta a data de recebimento para ficar coerente com a retirada
            $retirada->forceFill([
                'created_at' => now()->subDays(15),
            ])->saveQuietly();

            //Uma encomenda ainda aguardando retirada na portaria
            $pendente = Encomenda::factory()->aguardandoRetirada()->create([
                'fk_id_morador_encomenda' => $morador->pk_id_morador,
            ]);

            $pendente->forceFill([
                'created_at' => now()->subDays(3),
            ])->saveQuietly();
        }
    }
}
