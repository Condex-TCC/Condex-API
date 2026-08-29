<?php

namespace Database\Seeders;

use App\Models\Envio;
use Illuminate\Database\Seeder;

class EnvioSeeder extends Seeder
{
    public function run(): void
    {
        Envio::factory(10)->create();
    }
}