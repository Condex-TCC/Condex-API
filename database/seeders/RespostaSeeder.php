<?php

namespace Database\Seeders;

use App\Models\Resposta;
use Illuminate\Database\Seeder;

class RespostaSeeder extends Seeder
{
    public function run(): void
    {
        Resposta::factory(10)->create();
    }
}