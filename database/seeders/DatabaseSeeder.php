<?php

namespace Database\Seeders;

use App\Models\Unidade;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

//Classe que adicionar os dados falsos aos banco de dados
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    //Função que chamas as outras seeders, para gerar outros falsos
    public function run(): void
    {
        //Chamandos as outras seedars
        $this->call([
            SindicoSeeder::class,
            PorteiroSeeder::class,
            RegrasSeeder::class,
            LaudosSeeder::class,
            UnidadeSeeder::class,
            MoradorSeeder::class,
            VisitanteSeeder::class,
            EspacoSeeder::class,
            EncomendaSeeder::class,
            ComunicadoSeeder::class,
            RespostaSeeder::class,
            EnvioSeeder::class,
        ]);

    }
}
