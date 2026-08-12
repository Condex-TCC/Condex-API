<?php

namespace Database\Seeders;

use App\Models\Espaco;
use Illuminate\Database\Seeder;

class EspacoSeeder extends Seeder
{
    public function run(): void
    {
        Espaco::factory()->count(10)->create();
    }
}