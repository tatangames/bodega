<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrincipalController;
use App\Http\Controllers\Login\LoginController;
use App\Http\Controllers\Controles\ControlController;
use App\Http\Controllers\Backend\Roles\RolesController;
use App\Http\Controllers\Backend\Roles\PermisoController;
use App\Http\Controllers\Backend\Perfil\PerfilController;
use App\Http\Controllers\Backend\Equipos\EquiposController;
use App\Http\Controllers\Backend\Repuestos\RepuestosController;
use App\Http\Controllers\Backend\Reportes\ReportesController;


Route::get('/', [LoginController::class,'index'])->name('login');

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('admin.logout');

// --- CONTROL WEB ---
Route::get('/panel', [ControlController::class,'indexRedireccionamiento'])->name('admin.panel');

// --- ROLES ---
Route::get('/admin/roles/index', [RolesController::class,'index'])->name('admin.roles.index');
Route::get('/admin/roles/tabla', [RolesController::class,'tablaRoles']);
Route::get('/admin/roles/lista/permisos/{id}', [RolesController::class,'vistaPermisos']);
Route::get('/admin/roles/permisos/tabla/{id}', [RolesController::class,'tablaRolesPermisos']);
Route::post('/admin/roles/permiso/borrar', [RolesController::class, 'borrarPermiso']);
Route::post('/admin/roles/permiso/agregar', [RolesController::class, 'agregarPermiso']);
Route::get('/admin/roles/permisos/lista', [RolesController::class,'listaTodosPermisos']);
Route::get('/admin/roles/permisos-todos/tabla', [RolesController::class,'tablaTodosPermisos']);
Route::post('/admin/roles/borrar-global', [RolesController::class, 'borrarRolGlobal']);

// --- PERMISOS ---
Route::get('/admin/permisos/index', [PermisoController::class,'index'])->name('admin.permisos.index');
Route::get('/admin/permisos/tabla', [PermisoController::class,'tablaUsuarios']);
Route::post('/admin/permisos/nuevo-usuario', [PermisoController::class, 'nuevoUsuario']);
Route::post('/admin/permisos/info-usuario', [PermisoController::class, 'infoUsuario']);
Route::post('/admin/permisos/editar-usuario', [PermisoController::class, 'editarUsuario']);
Route::post('/admin/permisos/nuevo-rol', [PermisoController::class, 'nuevoRol']);
Route::post('/admin/permisos/extra-nuevo', [PermisoController::class, 'nuevoPermisoExtra']);
Route::post('/admin/permisos/extra-borrar', [PermisoController::class, 'borrarPermisoGlobal']);

// --- PERFIL ---
Route::get('/admin/editar-perfil/index', [PerfilController::class,'indexEditarPerfil'])->name('admin.perfil');
Route::post('/admin/editar-perfil/actualizar', [PerfilController::class, 'editarUsuario']);

// --- SIN PERMISOS VISTA 403 ---
Route::get('sin-permisos', [ControlController::class,'indexSinPermiso'])->name('no.permisos.index');


// Equipos
Route::get('/admin/equipos/index', [EquiposController::class,'indexEquipos'])->name('admin.equipos.index');
Route::get('/admin/equipos/tabla/index', [EquiposController::class,'tablaEquipos']);
Route::post('/admin/equipos/nuevo', [EquiposController::class, 'nuevaEquipos']);
Route::post('/admin/equipos/informacion', [EquiposController::class, 'informacionEquipos']);
Route::post('/admin/equipos/editar', [EquiposController::class, 'editarEquipos']);




Route::get('/admin/unidadmedida/index', [PrincipalController::class,'indexUnidadMedida'])->name('admin.unidadmedida.index');
Route::get('/admin/unidadmedida/tabla/index', [PrincipalController::class,'tablaUnidadMedida']);
Route::post('/admin/unidadmedida/nuevo', [PrincipalController::class, 'nuevaUnidadMedida']);
Route::post('/admin/unidadmedida/informacion', [PrincipalController::class, 'informacionUnidadMedida']);
Route::post('/admin/unidadmedida/editar', [PrincipalController::class, 'editarUnidadMedida']);

Route::get('/admin/materiales/index', [PrincipalController::class,'indexMateriales'])->name('admin.materiales.index');
Route::get('/admin/materiales/tabla/index', [PrincipalController::class,'tablaMateriales']);
Route::post('/admin/materiales/nuevo', [PrincipalController::class, 'nuevoMaterial']);
Route::post('/admin/materiales/informacion', [PrincipalController::class, 'informacionMaterial']);
Route::post('/admin/materiales/editar', [PrincipalController::class, 'editarMaterial']);

Route::get('/admin/detalle/material/cantidad/{id}', [RepuestosController::class,'vistaDetalleMaterial']);
Route::get('/admin/detalle/materialtabla/cantidad/{id}', [RepuestosController::class,'tablaDetalleMaterial']);


Route::get('/admin/registro/entrada', [PrincipalController::class,'indexRegistroEntrada'])->name('admin.entrada.registro.index');
Route::post('/admin/buscar/material',  [PrincipalController::class,'buscadorMaterial']);
Route::post('/admin/entrada/guardar',  [PrincipalController::class,'guardarEntrada']);

// bloque de cantidades
Route::get('/admin/repuesto/cantidad/bloque/{id}', [RepuestosController::class,'bloqueCantidades']);


Route::get('/admin/registro/salida', [PrincipalController::class,'indexRegistroSalida'])->name('admin.salida.registro.index');
Route::post('/admin/salida/guardar',  [PrincipalController::class,'guardarSalida']);


Route::get('/admin/entradas/vista', [PrincipalController::class,'indexEntradas'])->name('admin.entrada.vista.index');
Route::get('/admin/entradas/tabla', [PrincipalController::class,'indexTablaEntradas']);

Route::get('/admin/entradas/documento/{id}', [PrincipalController::class,'documentoEntrada']);
Route::post('/admin/entradas/borrar/documento',  [PrincipalController::class,'borrarDocumento']);
Route::post('/admin/entradas/borrar/registro',  [PrincipalController::class,'borrarRegistro']);
Route::post('/admin/entradas/guardar/documento',  [PrincipalController::class,'guardarDocumento']);



Route::get('/admin/entradas/detalle/{id}', [PrincipalController::class,'indexEntradasDetalle']);
Route::get('/admin/entradas/detalle/tabla/{id}', [PrincipalController::class,'indexEntradasDetalleTabla']);


Route::get('/admin/salidas/vista', [PrincipalController::class,'indexSalidas'])->name('admin.salida.vista.index');
Route::get('/admin/salidas/tabla', [PrincipalController::class,'indexTablaSalidas']);

Route::get('/admin/salidas/detalle/{id}', [PrincipalController::class,'indexSalidasDetalle']);
Route::get('/admin/salidas/detalle/tabla/{id}', [PrincipalController::class,'indexSalidasDetalleTabla']);

Route::post('/admin/salidas/borrar/registro',  [PrincipalController::class,'borrarRegistroSalida']);


// Reportes
// entradas y salidas
Route::get('/admin/entrada/reporte/vista', [ReportesController::class,'indexEntradaReporte'])->name('admin.entrada.reporte.index');
Route::get('/admin/reporte/registro/{tipo}/{desde}/{hasta}', [ReportesController::class,'reportePdf']);


// reporte por equipos
Route::get('/admin/entrada/reporte/equipos/vista', [ReportesController::class,'indexEntradaReporteEquipos'])->name('admin.entrada.reporte.equipos.index');
Route::get('/admin/reporte/porequipo/{desde}/{hasta}/{tipo}/{unidad}', [ReportesController::class, 'reportePorEquipo']);






