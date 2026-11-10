<?php

namespace Database\Seeders;

use App\Models\Visitante;
use Illuminate\Database\Seeder;

class VisitanteSeeder extends Seeder
{
    public function run(): void
    {
        //Dois visitantes por morador, para simular visitas recorrentes e variar o histórico
        Visitante::factory()->count(20)->create();
    }
}
