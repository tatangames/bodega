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


// equipos
Route::get('/admin/equipos/index', [EquiposController::class,'indexEquipos'])->name('admin.equipos.index');
Route::get('/admin/equipos/tabla/index', [EquiposController::class,'tablaEquipos']);
Route::post('/admin/equipos/nuevo', [EquiposController::class, 'nuevaEquipos']);
Route::post('/admin/equipos/informacion', [EquiposController::class, 'informacionEquipos']);
Route::post('/admin/equipos/editar', [EquiposController::class, 'editarEquipos']);

// unidad de medida
Route::get('/admin/unidadmedida/index', [PrincipalController::class,'indexUnidadMedida'])->name('admin.unidadmedida.index');
Route::get('/admin/unidadmedida/tabla/index', [PrincipalController::class,'tablaUnidadMedida']);
Route::post('/admin/unidadmedida/nuevo', [PrincipalController::class, 'nuevaUnidadMedida']);
Route::post('/admin/unidadmedida/informacion', [PrincipalController::class, 'informacionUnidadMedida']);
Route::post('/admin/unidadmedida/editar', [PrincipalController::class, 'editarUnidadMedida']);

// # de rin llanta
Route::get('/admin/rinllanta/index', [PrincipalController::class,'indexRinllanta'])->name('admin.rin.llantas.index');
Route::get('/admin/rinllanta/tabla/index', [PrincipalController::class,'tablaRinllanta']);
Route::post('/admin/rinllanta/nuevo', [PrincipalController::class, 'nuevaRinllanta']);
Route::post('/admin/rinllanta/informacion', [PrincipalController::class, 'informacionRinllanta']);
Route::post('/admin/rinllanta/editar', [PrincipalController::class, 'editarRinllanta']);


// proveedores
Route::get('/admin/proveedor/index', [RepuestosController::class,'indexProveedor'])->name('admin.proveedor.index');
Route::get('/admin/proveedor/tabla/index', [RepuestosController::class,'tablaProveedor']);
Route::post('/admin/proveedor/nuevo', [RepuestosController::class, 'nuevaProveedor']);
Route::post('/admin/proveedor/informacion', [RepuestosController::class, 'informacionProveedor']);
Route::post('/admin/proveedor/editar', [RepuestosController::class, 'editarProveedor']);

// marca de llantas
Route::get('/admin/marcallanta/index', [RepuestosController::class,'indexMarca'])->name('admin.marcas.llantas.index');
Route::get('/admin/marcallanta/tabla/index', [RepuestosController::class,'tablaMarca']);
Route::post('/admin/marcallanta/nuevo', [RepuestosController::class, 'nuevaMarca']);
Route::post('/admin/marcallanta/informacion', [RepuestosController::class, 'informacionMarca']);
Route::post('/admin/marcallanta/editar', [RepuestosController::class, 'editarMarca']);

// ubicación llanta
Route::get('/admin/ubicacion/index', [RepuestosController::class,'indexUbicacion'])->name('admin.ubicacion.index');
Route::get('/admin/ubicacion/tabla/index', [RepuestosController::class,'tablaUbicacion']);
Route::post('/admin/ubicacion/nuevo', [RepuestosController::class, 'nuevaUbicacion']);
Route::post('/admin/ubicacion/informacion', [RepuestosController::class, 'informacionUbicacion']);
Route::post('/admin/ubicacion/editar', [RepuestosController::class, 'editarUbicacion']);

// registro de repuestos
Route::get('/admin/materiales/index', [PrincipalController::class,'indexMateriales'])->name('admin.materiales.index');
Route::get('/admin/materiales/tabla/index', [PrincipalController::class,'tablaMateriales']);
Route::post('/admin/materiales/nuevo', [PrincipalController::class, 'nuevoMaterial']);
Route::post('/admin/materiales/informacion', [PrincipalController::class, 'informacionMaterial']);
Route::post('/admin/materiales/editar', [PrincipalController::class, 'editarMaterial']);

// detalle repuestos
Route::get('/admin/detalle/material/cantidad/{id}', [RepuestosController::class,'vistaDetalleMaterial']);
Route::get('/admin/detalle/materialtabla/cantidad/{id}', [RepuestosController::class,'tablaDetalleMaterial']);

// registro de llantas
Route::get('/admin/llantas/index', [RepuestosController::class,'indexLlantas'])->name('admin.llantas.index');
Route::get('/admin/llantas/tabla/index', [RepuestosController::class,'tablaLlantas']);
Route::post('/admin/llantas/nuevo', [RepuestosController::class, 'nuevoLlantas']);
Route::post('/admin/llantas/informacion', [RepuestosController::class, 'informacionLlantas']);
Route::post('/admin/llantas/editar', [RepuestosController::class, 'editarLlantas']);

// detalle llantas
Route::get('/admin/detalle/llantas/cantidad/{id}', [RepuestosController::class,'vistaDetalleLlanta']);
Route::get('/admin/detalle/llantastabla/cantidad/{id}', [RepuestosController::class,'tablaDetalleLlanta']);

// registrar entrada para repuestos
Route::get('/admin/registro/entrada', [PrincipalController::class,'indexRegistroEntrada'])->name('admin.entrada.registro.index');
Route::post('/admin/buscar/material',  [PrincipalController::class,'buscadorMaterial']);
Route::post('/admin/entrada/guardar',  [PrincipalController::class,'guardarEntrada']);

// registrar entrada para llantas
Route::get('/admin/registro/llanta/entrada', [RepuestosController::class,'indexRegistroEntradaLlanta'])->name('admin.entrada.llantas.registro.index');
Route::post('/admin/buscar/llantas',  [RepuestosController::class,'buscadorLlantas']);
Route::post('/admin/entrada/llanta/guardar',  [RepuestosController::class,'guardarEntradaLlantas']);

// registrar salida para llantas
Route::get('/admin/registro/llantas/salida', [RepuestosController::class,'indexRegistroSalidaLlantas'])->name('admin.salida.llantas.registro.index');
Route::post('/admin/salida/llantas/guardar',  [RepuestosController::class,'guardarSalidaLlantas']);

// bloque de cantidades para llantas
Route::get('/admin/llantas/cantidad/bloque/{id}', [RepuestosController::class,'bloqueCantidadesLlantas']);



// bloque de cantidades para repuestos
Route::get('/admin/repuesto/cantidad/bloque/{id}', [RepuestosController::class,'bloqueCantidades']);

// registrar salida de repuestos
Route::get('/admin/registro/salida', [PrincipalController::class,'indexRegistroSalida'])->name('admin.salida.registro.index');
Route::post('/admin/salida/guardar',  [PrincipalController::class,'guardarSalida']);

// historial entrada repuestos
Route::get('/admin/entradas/vista', [PrincipalController::class,'indexEntradas'])->name('admin.historial.entrada.repuesto.vista.index');
Route::get('/admin/entradas/tabla', [PrincipalController::class,'indexTablaEntradas']);

// historial entrada llantas
Route::get('/admin/historial/entradasllanta/vista', [PrincipalController::class,'indexHistorialEntradasLlanta'])->name('admin.historial.entrada.llanta.vista.index');
Route::get('/admin/historial/entradasllanta/tabla', [PrincipalController::class,'indexTablaHistorialEntradasLlantas']);

// historial entrada detalle para llanta
Route::get('/admin/historial/entrada/llantadetalle/{id}', [PrincipalController::class,'indexEntradasDetalleLlanta']);
Route::get('/admin/historial/entrada/llantadeta/tabla/{id}', [PrincipalController::class,'indexEntradasDetalleTablaLlanta']);

// historial entrada para llantas - opciones
Route::get('/admin/entradas/historialllanta/documento/{id}', [PrincipalController::class,'documentoEntradaLlanta']);
Route::post('/admin/entradas/historialllanta/borrar/documento',  [PrincipalController::class,'borrarDocumentoLlanta']);
Route::post('/admin/entradas/historialllanta/borrar/registro',  [PrincipalController::class,'borrarRegistroLlanta']);
Route::post('/admin/entradas/historialllanta/guardar/documento',  [PrincipalController::class,'guardarDocumentoLlanta']);
Route::post('/admin/entradas/historialllanta/informacion',  [RepuestosController::class,'informacionEntradaHistorialLlanta']);
Route::post('/admin/entradas/historialllanta/editar',  [RepuestosController::class,'editarEntradaHistorialLlanta']);

// historial entrada para repuestos - opciones
Route::get('/admin/entradas/documento/{id}', [PrincipalController::class,'documentoEntrada']);
Route::post('/admin/entradas/borrar/documento',  [PrincipalController::class,'borrarDocumento']);
Route::post('/admin/entradas/borrar/registro',  [PrincipalController::class,'borrarRegistro']);
Route::post('/admin/entradas/guardar/documento',  [PrincipalController::class,'guardarDocumento']);
Route::post('/admin/historial/entrada/informacion',  [RepuestosController::class,'informacionEntradaHistorial']);
Route::post('/admin/historial/entrada/editar',  [RepuestosController::class,'editarEntradaHistorial']);

// historial salida para llantas
Route::get('/admin/historial/salida/llanta/vista', [PrincipalController::class,'indexSalidasLlantas'])->name('admin.historial.salida.llanta.vista.index');
Route::get('/admin/historial/salida/llanta/tabla', [PrincipalController::class,'indexTablaSalidasLlantas']);

Route::post('/admin/historial/salida/llanta/borrar',  [PrincipalController::class,'borrarRegistroSalidaLlanta']);
Route::post('/admin/historial/salida/llanta/informacion',  [RepuestosController::class,'informacionSalidaHistorialLlanta']);
Route::post('/admin/historial/salida/llanta/editar',  [RepuestosController::class,'editarSalidaHistorialLlanta']);
Route::get('/admin/historialdeta/salida/llanta/{id}', [PrincipalController::class,'indexSalidasDetalleLlanta']);
Route::get('/admin/historialdeta/salida/llanta/tabla/{id}', [PrincipalController::class,'indexSalidasDetalleTablaLlanta']);

// historial salida para repuestos
Route::post('/admin/historial/salida/informacion',  [RepuestosController::class,'informacionSalidaHistorial']);
Route::post('/admin/historial/salida/editar',  [RepuestosController::class,'editarSalidaHistorial']);

// historial detalle para repuestos
Route::get('/admin/entradas/detalle/{id}', [PrincipalController::class,'indexEntradasDetalle']);
Route::get('/admin/entradas/detalle/tabla/{id}', [PrincipalController::class,'indexEntradasDetalleTabla']);

// historial detalle para salidas
Route::get('/admin/salidas/vista', [PrincipalController::class,'indexSalidas'])->name('admin.historial.salida.repuesto.vista.index');
Route::get('/admin/salidas/tabla', [PrincipalController::class,'indexTablaSalidas']);


Route::get('/admin/salidas/detalle/{id}', [PrincipalController::class,'indexSalidasDetalle']);
Route::get('/admin/salidas/detalle/tabla/{id}', [PrincipalController::class,'indexSalidasDetalleTabla']);

Route::post('/admin/salidas/borrar/registro',  [PrincipalController::class,'borrarRegistroSalida']);


// Reportes
// entradas y salidas
Route::get('/admin/entrada/reporte/vista', [ReportesController::class,'indexEntradaReporte'])->name('admin.entrada.reporte.index');
Route::get('/admin/reporte/registro/{tipo}/{desde}/{hasta}', [ReportesController::class,'reportePdf']);

// llantas
Route::get('/admin/entrada/reporte/llantas/vista', [ReportesController::class,'indexEntradaReporteLlanta'])->name('admin.entrada.reporte.llanta.index');
Route::get('/admin/reporte/registro/llantas/{tipo}/{desde}/{hasta}', [ReportesController::class,'reportePdfLlanta']);

// reporte por equipos
Route::get('/admin/entrada/reporte/equipos/vista', [ReportesController::class,'indexEntradaReporteEquipos'])->name('admin.entrada.reporte.equipos.index');
Route::get('/admin/reporte/porequipo/{desde}/{hasta}/{tipo}/{unidad}', [ReportesController::class, 'reportePorEquipo']);

// llanta
Route::get('/admin/entrada/reporte/llantas/equipos/vista', [ReportesController::class,'indexEntradaReporteEquiposLlantas'])->name('admin.entrada.reporte.llanta.equipos.index');
Route::get('/admin/reporte/porequipo/llantas/{desde}/{hasta}/{tipo}/{unidad}', [ReportesController::class, 'reportePorMarcaLLanta']);

// reporte cantidad actual
Route::get('/admin/reporte/cantidad/vista', [ReportesController::class,'indexEntradaReporteCantidad'])->name('admin.reporte.cantidad.index');
Route::get('/admin/reporte/cantidades', [ReportesController::class,'reportePdfCantidad']);


// llantas
Route::get('/admin/reporte/llantas/cantidad/vista', [ReportesController::class,'indexEntradaReporteCantidadLlanta'])->name('admin.reporte.llanta.cantidad.index');
Route::get('/admin/reporte/llantas/cantidades', [ReportesController::class,'reportePdfCantidadLlanta']);


