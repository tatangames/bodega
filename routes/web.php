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
use App\Http\Controllers\Backend\Aceites\AceitesController;
use App\Http\Controllers\Backend\Reportes\ReportesAceiteController;

// --- RUTA PARA LOGIN ---

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




// ********* RUTAS PARA REPUESTOS ************

// registro de equipos
Route::get('/admin/equipos/index', [EquiposController::class,'indexEquipos'])->name('admin.equipos.index');
Route::get('/admin/equipos/tabla/index', [EquiposController::class,'tablaEquipos']);
Route::post('/admin/equipos/nuevo', [EquiposController::class, 'nuevaEquipos']);
Route::post('/admin/equipos/informacion', [EquiposController::class, 'informacionEquipos']);
Route::post('/admin/equipos/editar', [EquiposController::class, 'editarEquipos']);

// registro unidad de medida
Route::get('/admin/unidadmedida/index', [PrincipalController::class,'indexUnidadMedida'])->name('admin.unidadmedida.index');
Route::get('/admin/unidadmedida/tabla/index', [PrincipalController::class,'tablaUnidadMedida']);
Route::post('/admin/unidadmedida/nuevo', [PrincipalController::class, 'nuevaUnidadMedida']);
Route::post('/admin/unidadmedida/informacion', [PrincipalController::class, 'informacionUnidadMedida']);
Route::post('/admin/unidadmedida/editar', [PrincipalController::class, 'editarUnidadMedida']);

// registro de ubicación de repuestos
Route::get('/admin/ubicacionrepuesto/index', [PrincipalController::class,'indexUbicacionRepuestos'])->name('admin.ubicacion.repuestos.index');
Route::get('/admin/ubicacionrepuesto/tabla/index', [PrincipalController::class,'tablaUbicacionRepuestos']);
Route::post('/admin/ubicacionrepuesto/nuevo', [PrincipalController::class, 'nuevaUbicacionRepuestos']);
Route::post('/admin/ubicacionrepuesto/informacion', [PrincipalController::class, 'informacionUbicacionRepuestos']);
Route::post('/admin/ubicacionrepuesto/editar', [PrincipalController::class, 'editarUbicacionRepuestos']);


// registro de repuestos
Route::get('/admin/materiales/index', [PrincipalController::class,'indexMateriales'])->name('admin.materiales.index');
Route::get('/admin/materiales/tabla/index', [PrincipalController::class,'tablaMateriales']);
Route::post('/admin/materiales/nuevo', [PrincipalController::class, 'nuevoMaterial']);
Route::post('/admin/materiales/informacion', [PrincipalController::class, 'informacionMaterial']);
Route::post('/admin/materiales/editar', [PrincipalController::class, 'editarMaterial']);

// detalle repuestos
Route::get('/admin/detalle/material/cantidad/{id}', [RepuestosController::class,'vistaDetalleMaterial']);
Route::get('/admin/detalle/materialtabla/cantidad/{id}', [RepuestosController::class,'tablaDetalleMaterial']);

// registrar entrada para repuestos
Route::get('/admin/registro/entrada', [PrincipalController::class,'indexRegistroEntrada'])->name('admin.entrada.registro.index');
Route::post('/admin/buscar/material',  [PrincipalController::class,'buscadorMaterial']);
Route::post('/admin/entrada/guardar',  [PrincipalController::class,'guardarEntrada']);

// bloque de cantidades para repuestos
Route::get('/admin/repuesto/cantidad/bloque/{id}', [RepuestosController::class,'bloqueCantidades']);

// registrar salida de repuestos
Route::get('/admin/registro/salida', [PrincipalController::class,'indexRegistroSalida'])->name('admin.salida.registro.index');
Route::post('/admin/salida/guardar',  [PrincipalController::class,'guardarSalida']);

// historial entrada repuestos
Route::get('/admin/entradas/vista', [PrincipalController::class,'indexEntradas'])->name('admin.historial.entrada.repuesto.vista.index');
Route::get('/admin/entradas/tabla', [PrincipalController::class,'indexTablaEntradas']);

// historial entrada para repuestos - opciones
Route::get('/admin/entradas/documento/{id}', [PrincipalController::class,'documentoEntrada']);
Route::post('/admin/entradas/borrar/documento',  [PrincipalController::class,'borrarDocumento']);
Route::post('/admin/entradas/borrar/registro',  [PrincipalController::class,'borrarRegistro']);
Route::post('/admin/entradas/guardar/documento',  [PrincipalController::class,'guardarDocumento']);
Route::post('/admin/historial/entrada/informacion',  [RepuestosController::class,'informacionEntradaHistorial']);
Route::post('/admin/historial/entrada/editar',  [RepuestosController::class,'editarEntradaHistorial']);

// historial salida para repuestos
Route::post('/admin/historial/salida/informacion',  [RepuestosController::class,'informacionSalidaHistorial']);
Route::post('/admin/historial/salida/editar',  [RepuestosController::class,'editarSalidaHistorial']);

// historial detalle para repuestos
Route::get('/admin/entradas/detalle/{id}', [PrincipalController::class,'indexEntradasDetalle']);
Route::get('/admin/entradas/detalle/tabla/{id}', [PrincipalController::class,'indexEntradasDetalleTabla']);

// detalle historial entrada llanta, para cambiar precio
Route::post('/admin/entradas/historial/deta/informacion',  [PrincipalController::class,'informacionEntradaHistorialDetalle']);
Route::post('/admin/entradas/historial/deta/editar',  [PrincipalController::class,'editarEntradaHistorialDetalle']);




// historial detalle para salidas
Route::get('/admin/salidas/vista', [PrincipalController::class,'indexSalidas'])->name('admin.historial.salida.repuesto.vista.index');
Route::get('/admin/salidas/tabla', [PrincipalController::class,'indexTablaSalidas']);


Route::get('/admin/salidas/detalle/{id}', [PrincipalController::class,'indexSalidasDetalle']);
Route::get('/admin/salidas/detalle/tabla/{id}', [PrincipalController::class,'indexSalidasDetalleTabla']);

Route::post('/admin/salidas/borrar/registro',  [PrincipalController::class,'borrarRegistroSalida']);

// Reportes
// entradas y salidas para repuestos
Route::get('/admin/entrada/reporte/vista', [ReportesController::class,'indexEntradaReporte'])->name('admin.entrada.reporte.index');
Route::get('/admin/reporte/registro/{tipo}/{desde}/{hasta}', [ReportesController::class,'reportePdf']);

// reporte por equipos para repuestos
Route::get('/admin/entrada/reporte/equipos/vista', [ReportesController::class,'indexEntradaReporteEquipos'])->name('admin.entrada.reporte.equipos.index');
Route::get('/admin/reporte/porequipo/{desde}/{hasta}/{tipo}/{unidad}', [ReportesController::class, 'reportePorEquipo']);

// reporte cantidad actual para repuestos
Route::get('/admin/reporte/cantidad/vista', [ReportesController::class,'indexEntradaReporteCantidad'])->name('admin.reporte.cantidad.index');
Route::get('/admin/reporte/cantidades', [ReportesController::class,'reportePdfCantidad']);

// reporte catálogo de repuestos
Route::get('/admin/reporte/catalogo/materiales/index', [ReportesController::class,'indexCatalogoMateriales'])->name('admin.reporte.catalogo.repuestos.index');
Route::get('/admin/reporte/catalogo/materiales', [ReportesController::class,'reporteCatalogoMateriales']);






//*************** ACEITES Y LUBRICANTES



// registro de catálogo de aceites y lubricantes
Route::get('/admin/catalogo/aceiteylubricantes/index', [AceitesController::class,'indexCatalogoAceiteLubicantes'])->name('admin.catalogo.aceites.lubricantes.index');
Route::get('/admin/catalogo/aceiteylubricantes/tabla/index', [AceitesController::class,'tablaCatalogoAceiteLubicantes']);
Route::post('/admin/catalogo/aceiteylubricantes/nuevo', [AceitesController::class, 'nuevoCatalogoAceiteLubicantes']);
Route::post('/admin/catalogo/aceiteylubricantes/informacion', [AceitesController::class, 'informacionCatalogoAceiteLubicantes']);
Route::post('/admin/catalogo/aceiteylubricantes/editar', [AceitesController::class, 'editarCatalogoAceiteLubicantes']);

// registrar entrada de aceite y lubricantes
Route::get('/admin/aceiteylubricantes/registro/entrada', [AceitesController::class,'indexRegistroEntrada'])->name('admin.entrada.registro.aceitelubricantes.index');
Route::post('/admin/aceiteylubricantes/buscar/material',  [AceitesController::class,'buscadorAceiteLubricante']);
Route::post('/admin/aceiteylubricantes/entrada/guardar',  [AceitesController::class,'guardarEntradaAceiteLubricante']);

// registrar salida de aceites y lubricantes

Route::get('/admin/aceiteylubricantes/registro/salida', [AceitesController::class,'indexRegistroSalida'])->name('admin.salida.registro.aceitelubricantes.index');

// bloque de cantidades para elegir cual aceite o lubricante
Route::get('/admin/aceiteylubricantes/cantidad/bloque/{id}', [AceitesController::class,'bloqueCantidades']);
// registrar salida para aceites y lubricantes
Route::post('/admin/aceiteylubricantes/salida/guardar',  [AceitesController::class,'guardarSalidaAceiteLubricante']);


// ver salidas de aceites y lubricantes para agregar detalles
Route::get('/admin/aceiteylubricantes/enuso/index', [AceitesController::class,'indexEnUsoAceites'])->name('admin.salida.enuso.aceitelubricantes.index');
Route::get('/admin/aceiteylubricantes/enuso/tabla', [AceitesController::class,'tablaEnUsoAceites']);
Route::post('/admin/aceiteylubricantes/enuso/finalizar', [AceitesController::class,'finalizarUsoAceitesLubricantes']);

// agregar detalle a un registro en uso
Route::get('/admin/aceiteylubricantes/detalle/uso/{id}', [AceitesController::class,'detalleAceiteLubricanteUso']);
Route::get('/admin/aceiteylubricantes/detalle/usotabla/{id}', [AceitesController::class,'tablaAceiteLubricanteUso']);
Route::post('/admin/aceiteylubricantes/guardar/detalles', [AceitesController::class,'guardarDetalleSalida']);

// ver registros finalizados de aceites y lubricantes
Route::get('/admin/aceiteylubricantes/finalizados', [AceitesController::class,'indexAceitesFinalizados'])->name('admin.finalizados.aceitelubricantes.index');
Route::get('/admin/aceiteylubricantes/finalizados/tabla', [AceitesController::class,'tablaAceitesFinalizados']);
Route::post('/admin/aceiteylubricantes/reutilizar', [AceitesController::class,'reutilizarUsoAceitesLubricantes']);

// ver detalle de aceite y lubricantes que estan finalizados
Route::get('/admin/aceiteylubricantes/finali/detalle/index/{id}', [AceitesController::class,'indexFinalizadosAceitesDetalle']);
Route::get('/admin/aceiteylubricantes/finali/detalle/tabla/{id}', [AceitesController::class,'tablaFinalizadosAceitesDetalle']);

Route::post('/admin/aceiteylubricantes/borrar/fila/detalle', [AceitesController::class,'eliminarDetalleAceites']);

// borrar salida de aceites y lubricantes (borra salida y sus detalles)
Route::post('/admin/aceiteylubricantes/borrar/salida', [AceitesController::class,'borrarSalidaAceiteLubricantes']);





// * REPORTES PARA ACEITES Y LUBRICANTES

// entradas y salidas
Route::get('/admin/entrada/aceitelubricantes/reporte/vista', [ReportesAceiteController::class,'indexEntradaReporte'])->name('admin.entrada.reporte.aceitelubricantes.index');
Route::get('/admin/reporte/aceitelubricantes/{tipo}/{desde}/{hasta}', [ReportesAceiteController::class,'reportePdfEntradaAceite']);

// reporte por equipos
Route::get('/admin/entrada/aceitelubricantes/equipos/vista', [ReportesAceiteController::class,'indexEntradaReporteEquiposAceites'])->name('admin.entrada.reporte.aceitelubricantes.equipos.index');
Route::get('/admin/reporte/aceitelubricantes/porequipo/{desde}/{hasta}/{unidad}', [ReportesAceiteController::class, 'reportePorEquipoAceites']);

// reporte cantidad actual
Route::get('/admin/reporte/aceitelubricantes/cantidad/vista', [ReportesAceiteController::class,'indexEntradaReporteCantidadAceites'])->name('admin.reporte.aceitelubricantes.cantidad.index');
Route::get('/admin/reporte/aceitelubricantes/cantidades', [ReportesAceiteController::class,'reportePdfCantidadAceites']);
















//**************** RUTAS PARA LLANTAS *****************

// registro tipo de llanta
Route::get('/admin/rinllanta/index', [PrincipalController::class,'indexRinllanta'])->name('admin.rin.llantas.index');
Route::get('/admin/rinllanta/tabla/index', [PrincipalController::class,'tablaRinllanta']);
Route::post('/admin/rinllanta/nuevo', [PrincipalController::class, 'nuevaRinllanta']);
Route::post('/admin/rinllanta/informacion', [PrincipalController::class, 'informacionRinllanta']);
Route::post('/admin/rinllanta/editar', [PrincipalController::class, 'editarRinllanta']);


// registro de proveedores
Route::get('/admin/proveedor/index', [RepuestosController::class,'indexProveedor'])->name('admin.proveedor.index');
Route::get('/admin/proveedor/tabla/index', [RepuestosController::class,'tablaProveedor']);
Route::post('/admin/proveedor/nuevo', [RepuestosController::class, 'nuevaProveedor']);
Route::post('/admin/proveedor/informacion', [RepuestosController::class, 'informacionProveedor']);
Route::post('/admin/proveedor/editar', [RepuestosController::class, 'editarProveedor']);

// registro marca de llantas
Route::get('/admin/marcallanta/index', [RepuestosController::class,'indexMarca'])->name('admin.marcas.llantas.index');
Route::get('/admin/marcallanta/tabla/index', [RepuestosController::class,'tablaMarca']);
Route::post('/admin/marcallanta/nuevo', [RepuestosController::class, 'nuevaMarca']);
Route::post('/admin/marcallanta/informacion', [RepuestosController::class, 'informacionMarca']);
Route::post('/admin/marcallanta/editar', [RepuestosController::class, 'editarMarca']);

// ajustes de firma para llanta
Route::get('/admin/ajuste/firmallanta/index', [RepuestosController::class,'indexFirmaLlanta'])->name('admin.registro.firmas.llanta.index');
Route::post('/admin/ajuste/firmallanta/editar', [RepuestosController::class, 'editarFirmaLlanta']);

// ubicación para llanta
Route::get('/admin/ubicacion/index', [RepuestosController::class,'indexUbicacion'])->name('admin.ubicacion.index');
Route::get('/admin/ubicacion/tabla/index', [RepuestosController::class,'tablaUbicacion']);
Route::post('/admin/ubicacion/nuevo', [RepuestosController::class, 'nuevaUbicacion']);
Route::post('/admin/ubicacion/informacion', [RepuestosController::class, 'informacionUbicacion']);
Route::post('/admin/ubicacion/editar', [RepuestosController::class, 'editarUbicacion']);


// registro de llantas
Route::get('/admin/llantas/index', [RepuestosController::class,'indexLlantas'])->name('admin.llantas.index');
Route::get('/admin/llantas/tabla/index', [RepuestosController::class,'tablaLlantas']);
Route::post('/admin/llantas/nuevo', [RepuestosController::class, 'nuevoLlantas']);
Route::post('/admin/llantas/informacion', [RepuestosController::class, 'informacionLlantas']);
Route::post('/admin/llantas/editar', [RepuestosController::class, 'editarLlantas']);

// detalle llantas
Route::get('/admin/detalle/llantas/cantidad/{id}', [RepuestosController::class,'vistaDetalleLlanta']);
Route::get('/admin/detalle/llantastabla/cantidad/{id}', [RepuestosController::class,'tablaDetalleLlanta']);

// registrar entrada para llantas
Route::get('/admin/registro/llanta/entrada', [RepuestosController::class,'indexRegistroEntradaLlanta'])->name('admin.entrada.llantas.registro.index');
Route::post('/admin/buscar/llantas',  [RepuestosController::class,'buscadorLlantas']);
Route::post('/admin/entrada/llanta/guardar',  [RepuestosController::class,'guardarEntradaLlantas']);

// registrar salida para llantas
Route::get('/admin/registro/llantas/salida', [RepuestosController::class,'indexRegistroSalidaLlantas'])->name('admin.salida.llantas.registro.index');
Route::post('/admin/salida/llantas/guardar',  [RepuestosController::class,'guardarSalidaLlantas']);

// bloque de cantidades para llantas
Route::get('/admin/llantas/cantidad/bloque/{id}', [RepuestosController::class,'bloqueCantidadesLlantas']);

// historial entrada llantas
Route::get('/admin/historial/llantas/vista', [PrincipalController::class,'indexHistorialEntradasLlanta'])->name('admin.historial.entrada.llanta.vista.index');
Route::get('/admin/historial/llantas/tabla', [PrincipalController::class,'indexTablaHistorialEntradasLlantas']);

// historial entrada detalle para llanta
Route::get('/admin/historial/entrada/llantadetalle/{id}', [PrincipalController::class,'indexEntradasDetalleLlanta']);
Route::get('/admin/historial/entrada/llantadeta/tabla/{id}', [PrincipalController::class,'indexEntradasDetalleTablaLlanta']);

// historial entrada para entradaysalida - opciones
Route::get('/admin/entradas/historialllanta/documento/{id}', [PrincipalController::class,'documentoEntradaLlanta']);
Route::post('/admin/entradas/historialllanta/borrar/documento',  [PrincipalController::class,'borrarDocumentoLlanta']);
Route::post('/admin/entradas/historialllanta/borrar/registro',  [PrincipalController::class,'borrarRegistroLlanta']);
Route::post('/admin/entradas/historialllanta/guardar/documento',  [PrincipalController::class,'guardarDocumentoLlanta']);
Route::post('/admin/entradas/historialllanta/informacion',  [RepuestosController::class,'informacionEntradaHistorialLlanta']);
Route::post('/admin/entradas/historialllanta/editar',  [RepuestosController::class,'editarEntradaHistorialLlanta']);

// detalle historial entrada llanta. para cambiar precio
Route::post('/admin/entradas/historialllanta/deta/informacion',  [RepuestosController::class,'informacionEntradaHistorialLlantaDetalle']);
Route::post('/admin/entradas/historialllanta/deta/editar',  [RepuestosController::class,'editarEntradaHistorialLlantaDetalle']);


// historial salida para llantas
Route::get('/admin/historial/salida/llanta/vista', [PrincipalController::class,'indexSalidasLlantas'])->name('admin.historial.salida.llanta.vista.index');
Route::get('/admin/historial/salida/llanta/tabla', [PrincipalController::class,'indexTablaSalidasLlantas']);

Route::post('/admin/historial/salida/llanta/borrar',  [PrincipalController::class,'borrarRegistroSalidaLlanta']);
Route::post('/admin/historial/salida/llanta/informacion',  [RepuestosController::class,'informacionSalidaHistorialLlanta']);
Route::post('/admin/historial/salida/llanta/editar',  [RepuestosController::class,'editarSalidaHistorialLlanta']);
Route::get('/admin/historialdeta/salida/llanta/{id}', [PrincipalController::class,'indexSalidasDetalleLlanta']);
Route::get('/admin/historialdeta/salida/llanta/tabla/{id}', [PrincipalController::class,'indexSalidasDetalleTablaLlanta']);


// reporte entradas y salidas para llantas
Route::get('/admin/entrada/reporte/llantas/vista', [ReportesController::class,'indexEntradaReporteLlanta'])->name('admin.entrada.reporte.llanta.index');
Route::get('/admin/reporte/registro/llantas/{tipo}/{desde}/{hasta}', [ReportesController::class,'reportePdfLlanta']);

// reporte por marca para llanta
Route::get('/admin/entrada/reporte/llantas/equipos/vista', [ReportesController::class,'indexEntradaReporteEquiposLlantas'])->name('admin.entrada.reporte.llanta.equipos.index');
Route::get('/admin/reporte/porequipo/llantas/{desde}/{hasta}/{tipo}/{unidad}', [ReportesController::class, 'reportePorMarcaLLanta']);

// reporte cantidad actual para llantas
Route::get('/admin/reporte/llantas/cantidad/vista', [ReportesController::class,'indexEntradaReporteCantidadLlanta'])->name('admin.reporte.llanta.cantidad.index');
Route::get('/admin/reporte/llantas/cantidades', [ReportesController::class,'reportePdfCantidadLlanta']);

// reporte catálogo de llantas
Route::get('/admin/reporte/catalogo/llantas/index', [ReportesController::class,'indexCatalogoLLantas'])->name('admin.reporte.catalogo.llantas.index');
Route::get('/admin/reporte/catalogo/llantas', [ReportesController::class,'reporteCatalogoLlantas']);



Route::get('/admin/reporte/por/materiales/index', [ReportesController::class,'indexReportePorMateriales'])->name('admin.reporte.por.material');


Route::get('/admin/reporte/pdf/material/entradas/{desde}/{hasta}/{material}', [ReportesController::class, 'reportePdfPorMaterialEntradas']);
Route::get('/admin/reporte/pdf/material/salidas/{desde}/{hasta}/{material}', [ReportesController::class, 'reportePdfPorMaterialSalidas']);


