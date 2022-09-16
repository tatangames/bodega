<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Seeder;

class MarcaLLantaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Marca::create([
            'nombre' => 'TRIANGLE',
        ]);

        Marca::create([
            'nombre' => 'AUSTONE',
        ]);

        Marca::create([
            'nombre' => 'FIRESTONE',
        ]);

        Marca::create([
            'nombre' => 'BRIDGESTONE',
        ]);

        Marca::create([
            'nombre' => 'COOPERTIRE',
        ]);

        Marca::create([
            'nombre' => 'KAPSEN',
        ]);

        Marca::create([
            'nombre' => 'MAXXIS',
        ]);

        Marca::create([
            'nombre' => 'BKT',
        ]);

        Marca::create([
            'nombre' => 'KAPSEN',
        ]);

    }
}
