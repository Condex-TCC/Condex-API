<?php

namespace Database\Seeders;

use App\Models\Visitante;
use Illuminate\Database\Seeder;

class VisitanteSeeder extends Seeder
{
    public function run(): void
    {
        Visitante::factory()->create();
    }
}