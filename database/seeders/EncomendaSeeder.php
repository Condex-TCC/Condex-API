<?php

namespace Database\Seeders;

use App\Models\Encomenda;
use Illuminate\Database\Seeder;

class EncomendaSeeder extends Seeder
{
    public function run(): void
    {
        Encomenda::factory()->count(10)->create();
    }
}