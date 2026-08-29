<?php

namespace Database\Seeders;

use App\Models\Comunicado;
use Illuminate\Database\Seeder;

class ComunicadoSeeder extends Seeder
{
    public function run(): void
    {
        Comunicado::factory(10)->create();
    }
}