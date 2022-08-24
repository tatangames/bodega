<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrincipalController;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [PrincipalController::class,'index']);

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

Route::get('/admin/registro/entrada', [PrincipalController::class,'indexRegistroEntrada'])->name('admin.entrada.registro.index');
Route::post('/admin/buscar/material',  [PrincipalController::class,'buscadorMaterial']);
Route::post('/admin/entrada/guardar',  [PrincipalController::class,'guardarEntrada']);

Route::get('/admin/registro/salida', [PrincipalController::class,'indexRegistroSalida'])->name('admin.salida.registro.index');
Route::post('/admin/salida/guardar',  [PrincipalController::class,'guardarSalida']);



Route::get('/admin/entradas/vista', [PrincipalController::class,'indexEntradas'])->name('admin.entrada.vista.index');
Route::get('/admin/entradas/tabla', [PrincipalController::class,'indexTablaEntradas']);

Route::get('/admin/entradas/detalle/{id}', [PrincipalController::class,'indexEntradasDetalle']);
Route::get('/admin/entradas/detalle/tabla/{id}', [PrincipalController::class,'indexEntradasDetalleTabla']);


Route::get('/admin/salidas/vista', [PrincipalController::class,'indexSalidas'])->name('admin.salida.vista.index');
Route::get('/admin/salidas/tabla', [PrincipalController::class,'indexTablaSalidas']);

Route::get('/admin/salidas/detalle/{id}', [PrincipalController::class,'indexSalidasDetalle']);
Route::get('/admin/salidas/detalle/tabla/{id}', [PrincipalController::class,'indexSalidasDetalleTabla']);

Route::get('/admin/entrada/reporte/vista', [PrincipalController::class,'indexEntradaReporte'])->name('admin.entrada.reporte.index');
Route::get('/admin/reporte/registro/{tipo}/{desde}/{hasta}', [PrincipalController::class,'reportePdf']);







