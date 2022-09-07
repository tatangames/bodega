<?php

namespace App\Http\Controllers\Backend\Repuestos;

use App\Http\Controllers\Controller;
use App\Models\EntradaDetalle;
use App\Models\Entradas;
use Illuminate\Http\Request;

class RepuestosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // muestra lo disponible de cantidades para descontar
    public function bloqueCantidades($id){

        // obtener todas las entradas y obtener cada fila de
        // cantidad_bloque

        $lista = EntradaDetalle::where('id_material', $id)
            ->where('cantidad_bloque', '>', 0)
            ->get();

        foreach ($lista as $dd){

            $infoEntrada = Entradas::where('id', $dd->id_entrada)->first();
            $dd->fecha = date("d-m-Y", strtotime($infoEntrada->fecha));
            $dd->factura = $infoEntrada->factura;
        }

        $hayCantidad = false;
        if(sizeof($lista) > 0){
            $hayCantidad = true;
        }

        return view('backend.admin.registros.modal.modalentrada', compact('lista', 'hayCantidad'));
    }




}
