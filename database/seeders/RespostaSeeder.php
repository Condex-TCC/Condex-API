<?php

namespace Database\Seeders;

use App\Models\Morador;
use App\Models\Resposta;
use Illuminate\Database\Seeder;

class RespostaSeeder extends Seeder
{
    public function run(): void
    {
        $moradores = Morador::all();

        Resposta::factory(10)->create([
            'fk_id_morador' => $moradores->random()->pk_id_morador,
        ]);
    }
}