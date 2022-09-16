<?php

namespace Database\Seeders;

use App\Models\MedidaRin;
use Illuminate\Database\Seeder;

class MedidaRinSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MedidaRin::create([
            'medida' => '10 R22.5',
        ]);

        MedidaRin::create([
            'medida' => '165 R 13C 8PRT',
        ]);

        MedidaRin::create([
            'medida' => '17.5 25 L3',
        ]);

        MedidaRin::create([
            'medida' => '185 70 R13',
        ]);

        MedidaRin::create([
            'medida' => '205 25 VL2',
        ]);

        MedidaRin::create([
            'medida' => '205 R16 C',
        ]);

        MedidaRin::create([
            'medida' => '215 70 R16',
        ]);

        MedidaRin::create([
            'medida' => '215 75 R15',
        ]);

        MedidaRin::create([
            'medida' => '215 75 R17.5',
        ]);

        MedidaRin::create([
            'medida' => '23.1 25',
        ]);

        MedidaRin::create([
            'medida' => '235 75 R15',
        ]);

        MedidaRin::create([
            'medida' => '245 65 R17',
        ]);

        MedidaRin::create([
            'medida' => '265 70 R17',
        ]);

        MedidaRin::create([
            'medida' => '7.5 R15 LT',
        ]);

        MedidaRin::create([
            'medida' => '8 14.5 MH',
        ]);

        MedidaRin::create([
            'medida' => '900 R20 16 PR',
        ]);
    }
}
