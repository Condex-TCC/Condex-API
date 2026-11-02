<?php

namespace Database\Seeders;

use App\Models\Visitante;
use Illuminate\Database\Seeder;

class VisitanteSeeder extends Seeder
{
    public function run(): void
    {
        //Visitantes suficientes para variar as visitas registradas
        Visitante::factory()->count(15)->create();
    }
}
