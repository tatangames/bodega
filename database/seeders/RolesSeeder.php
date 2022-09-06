<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Administrador
        $roleAdmin = Role::create(['name' => 'admin']);

        // Inventario
        $roleInventario = Role::create(['name' => 'inventario']);

        // Reportes
        $roleAuditora = Role::create(['name' => 'auditora']);

        // solo para administrador
        Permission::create(['name' => 'sidebar.roles.y.permisos', 'description' => 'sidebar seccion roles y permisos'])->syncRoles($roleAdmin);

        Permission::create(['name' => 'sidebar.registros', 'description' => 'contenedor de registros'])->syncRoles($roleInventario, $roleAuditora);
            Permission::create(['name' => 'registros.repuestos', 'description' => 'contenedor de registros'])->syncRoles($roleInventario, $roleAuditora);

                Permission::create(['name' => 'btn.registros.repuestos.material.nuevo', 'description' => 'boton agregar repuesto'])->syncRoles($roleInventario);
                Permission::create(['name' => 'btn.registros.repuestos.material.editar', 'description' => 'boton editar repuesto'])->syncRoles($roleInventario);

        Permission::create(['name' => 'registros.entradas', 'description' => 'registro de entradas'])->syncRoles($roleInventario);
        Permission::create(['name' => 'registros.salidas', 'description' => 'registro de salidas'])->syncRoles($roleInventario);
        Permission::create(['name' => 'registros.unidadmedida', 'description' => 'registro de unidad de medida'])->syncRoles($roleInventario);
        Permission::create(['name' => 'registros.equipo', 'description' => 'registro de equipos'])->syncRoles($roleInventario);

        Permission::create(['name' => 'sidebar.historial', 'description' => 'contenedor de historial'])->syncRoles($roleInventario, $roleAuditora);
            Permission::create(['name' => 'historial.entrada', 'description' => 'ver historial de entradas'])->syncRoles($roleInventario, $roleAuditora);

                Permission::create(['name' => 'btn.historial.entrada.btn.borrarregistro', 'description' => 'borrar historial de entrada'])->syncRoles($roleInventario);
                Permission::create(['name' => 'btn.historial.entrada.btn.borrardocumento', 'description' => 'borrar documento historial de entrada'])->syncRoles($roleInventario);
                Permission::create(['name' => 'btn.historial.entrada.btn.agregardocumento', 'description' => 'boton agregar documento historial de entrada'])->syncRoles($roleInventario);

        Permission::create(['name' => 'historial.salida', 'description' => 'ver historial de salidas'])->syncRoles($roleInventario, $roleAuditora);

                Permission::create(['name' => 'btn.historial.salida.borrarregistro', 'description' => 'borrar historial de salida'])->syncRoles($roleInventario);

        Permission::create(['name' => 'sidebar.reporte', 'description' => 'contenedor de reporte'])->syncRoles($roleInventario, $roleAuditora);
            Permission::create(['name' => 'reporte.entradas.y.salidas', 'description' => 'reporte de entradas y salidas'])->syncRoles($roleInventario, $roleAuditora);

    }
}
