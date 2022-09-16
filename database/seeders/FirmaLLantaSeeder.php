<?php

namespace Database\Seeders;

use App\Models\FirmasLlantas;
use Illuminate\Database\Seeder;

class FirmaLLantaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        FirmasLlantas::create([
            'nombre_1' => 'Elaborado por Encargado de Bodega',
            'nombre_2' => 'Revisado por',
            'nombre_3' => 'Marcos Marcos',
            'nombre_4' => 'Jefe de Plantel Maquinaria y Equipo',
            'nombre_5' => 'Autorizado Por',
            'nombre_6' => 'Gerente de Servicio y Desarrollo Territorial',
            'distancia' => 100
        ]);
    }
}
