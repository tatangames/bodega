<?php

namespace Database\Seeders;

use App\Models\Usuario;
use Illuminate\Database\Seeder;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Usuario::create([
            'nombre' => 'Jonathan',
            'usuario' => 'jonathan',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('admin');

        Usuario::create([
            'nombre' => 'Marcos',
            'usuario' => 'marcos',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('inventario');

        Usuario::create([
            'nombre' => 'Auditora',
            'usuario' => 'auditora',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('auditora');

        Usuario::create([
            'nombre' => 'Llantas',
            'usuario' => 'llantas',
            'password' => bcrypt('1234'),
            'activo' => 1,
        ])->assignRole('llantas');
    }
}
