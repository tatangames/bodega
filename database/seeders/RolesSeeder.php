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

        // Llantas
        $roleLlantas = Role::create(['name' => 'llantas']);

        // solo para administrador
        Permission::create(['name' => 'sidebar.roles.y.permisos', 'description' => 'sidebar seccion roles y permisos'])->syncRoles($roleAdmin);


        Permission::create(['name' => 'sidebar.catalogo', 'description' => 'contenedor de catalogo'])->syncRoles($roleInventario, $roleAuditora, $roleLlantas);
        Permission::create(['name' => 'sidebar.registrar.repuestos', 'description' => 'contenedor de catalogo repuestos'])->syncRoles($roleInventario, $roleAuditora, $roleLlantas);
        Permission::create(['name' => 'sidebar.registrar.llantas', 'description' => 'contenedor de catalogo repuestos'])->syncRoles($roleInventario, $roleAuditora, $roleLlantas);

        Permission::create(['name' => 'sidebar.registros.repuestos', 'description' => 'contenedor de registro repuestos'])->syncRoles($roleInventario);
        Permission::create(['name' => 'registros.entradas.repuestos', 'description' => 'contenedor de registro entradas repuestos'])->syncRoles($roleInventario);
        Permission::create(['name' => 'registros.salidas.repuestos', 'description' => 'contenedor de registro salidas repuestos'])->syncRoles($roleInventario);

        Permission::create(['name' => 'sidebar.registros.llantas', 'description' => 'contenedor de registro llantas'])->syncRoles($roleLlantas);
        Permission::create(['name' => 'registros.entradas.llantas', 'description' => 'contenedor de registro entradas llantas'])->syncRoles($roleLlantas);
        Permission::create(['name' => 'registros.salidas.llantas', 'description' => 'contenedor de registro salidas llantas'])->syncRoles($roleLlantas);

        Permission::create(['name' => 'sidebar.configuracion', 'description' => 'contenedor de configuraciones'])->syncRoles($roleInventario, $roleLlantas);
        Permission::create(['name' => 'registro.proveedores', 'description' => 'contenedor de registro para proveedores'])->syncRoles($roleLlantas);
        Permission::create(['name' => 'registro.ubicacion.llanta', 'description' => 'contenedor de ubicacion para llantas'])->syncRoles($roleLlantas);
        Permission::create(['name' => 'registros.unidadmedida', 'description' => 'contenedor de registro de medidas'])->syncRoles($roleInventario,);
        Permission::create(['name' => 'registros.equipo', 'description' => 'contenedor de registro equipos'])->syncRoles($roleInventario, $roleLlantas);
        Permission::create(['name' => 'registro.marca.llanta', 'description' => 'contenedor registro de marcas de llantas'])->syncRoles($roleLlantas);
        Permission::create(['name' => 'registro.medidarin.llanta', 'description' => 'contenedor registro de medida de rin para llantas'])->syncRoles($roleLlantas);
        Permission::create(['name' => 'registro.firmas.llantas', 'description' => 'contenedor registro de firmas para llantas'])->syncRoles($roleInventario, $roleLlantas, $roleAuditora);

        Permission::create(['name' => 'sidebar.historial.repuestos', 'description' => 'contenedor de historiales'])->syncRoles($roleInventario, $roleLlantas);
        Permission::create(['name' => 'historial.entrada.repuesto', 'description' => 'contenedor de historiales entrada repuesto'])->syncRoles($roleInventario);
        Permission::create(['name' => 'historial.salida.repuesto', 'description' => 'contenedor de historiales salida repuesto'])->syncRoles($roleInventario);
        Permission::create(['name' => 'historial.salida.llanta', 'description' => 'contenedor de historiales salida llanta'])->syncRoles($roleLlantas);
        Permission::create(['name' => 'historial.entrada.llanta', 'description' => 'contenedor de historiales entrada llanta'])->syncRoles($roleLlantas);

        Permission::create(['name' => 'sidebar.reporte.repuesto', 'description' => 'contenedor de reporte repuesto'])->syncRoles($roleInventario, $roleAuditora);
        Permission::create(['name' => 'reporte.repuestos.entradaysalida', 'description' => 'contenedor reporte entrada y salida para repuesto'])->syncRoles($roleInventario, $roleAuditora);
        Permission::create(['name' => 'reporte.repuesto.equipos', 'description' => 'contenedor reporte repuesto por equipos'])->syncRoles($roleInventario, $roleAuditora);
        Permission::create(['name' => 'reporte.repuesto.cantidades', 'description' => 'contenedor reporte cantidades repuesto'])->syncRoles($roleInventario, $roleAuditora);
        Permission::create(['name' => 'sidebar.reporte.catalogo.repuestos', 'description' => 'ver reporte de catalogo de repuestos'])->syncRoles($roleInventario, $roleAuditora);


        Permission::create(['name' => 'sidebar.reporte.llantas', 'description' => 'contenedor de reporte llantas'])->syncRoles($roleLlantas, $roleInventario, $roleAuditora);
        Permission::create(['name' => 'reporte.llantas.entradaysalida', 'description' => 'contenedor reporte entrada y salida para llantas'])->syncRoles($roleLlantas, $roleInventario, $roleAuditora);
        Permission::create(['name' => 'reporte.llantas.equipos', 'description' => 'contenedor reporte llantas por equipos'])->syncRoles($roleLlantas, $roleInventario, $roleAuditora);
        Permission::create(['name' => 'reporte.llantas.cantidades', 'description' => 'contenedor reporte cantidades llantas'])->syncRoles($roleLlantas, $roleInventario, $roleAuditora);
        Permission::create(['name' => 'sidebar.reporte.catalogo.llantas', 'description' => 'ver reporte de catalogo de llantas'])->syncRoles($roleInventario, $roleAuditora);

        //**********************************
        //REPORTES

        Permission::create(['name' => 'btn.registros.repuestos.material.nuevo', 'description' => 'boton agregar repuesto'])->syncRoles($roleInventario);
        Permission::create(['name' => 'btn.registros.llantas.nuevo', 'description' => 'boton agregar llantas'])->syncRoles($roleLlantas);

        Permission::create(['name' => 'btn.registros.repuestos.material.editar', 'description' => 'boton editar repuesto'])->syncRoles($roleInventario);
        Permission::create(['name' => 'btn.registros.llantas.material.editar', 'description' => 'boton editar llanta'])->syncRoles($roleLlantas);

        Permission::create(['name' => 'btn.historial.entrada.btn.borrarregistro', 'description' => 'borrar historial de entrada'])->syncRoles($roleInventario);
        Permission::create(['name' => 'btn.historial.llanta.entrada.btn.borrarregistro', 'description' => 'borrar historial de entrada llanta'])->syncRoles($roleLlantas);

        Permission::create(['name' => 'btn.historial.entrada.btn.borrardocumento', 'description' => 'borrar documento historial de entrada'])->syncRoles($roleInventario);
        Permission::create(['name' => 'btn.historial.entrada.llanta.btn.borrardocumento', 'description' => 'borrar documento historial de entrada llanta'])->syncRoles($roleLlantas);

        Permission::create(['name' => 'btn.historial.entrada.btn.agregardocumento', 'description' => 'boton agregar documento historial de entrada'])->syncRoles($roleInventario);
        Permission::create(['name' => 'btn.historial.entrada.llanta.btn.agregardocumento', 'description' => 'boton agregar documento historial de entrada llanta'])->syncRoles($roleLlantas);

        Permission::create(['name' => 'btn.historial.entrada.btn.editar', 'description' => 'boton editar historial de entrada'])->syncRoles($roleInventario);
        Permission::create(['name' => 'btn.historial.entrada.llanta.btn.editar', 'description' => 'boton editar historial de entrada llanta'])->syncRoles($roleLlantas);

        Permission::create(['name' => 'btn.historial.salida.borrarregistro', 'description' => 'borrar historial de salida'])->syncRoles($roleInventario);
        Permission::create(['name' => 'btn.historial.llanta.salida.borrarregistro', 'description' => 'borrar historial de salida llanta'])->syncRoles($roleLlantas);

        Permission::create(['name' => 'btn.historial.salida.btn.editar', 'description' => 'boton editar historial de salida'])->syncRoles($roleInventario);
        Permission::create(['name' => 'btn.historial.salida.llanta.btn.editar', 'description' => 'boton editar historial de salida llanta'])->syncRoles($roleLlantas);



    }
}
