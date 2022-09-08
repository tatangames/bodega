<?php

namespace App\Http\Controllers\Backend\Repuestos;

use App\Http\Controllers\Controller;
use App\Models\EntradaDetalle;
use App\Models\Entradas;
use App\Models\Equipos;
use App\Models\Materiales;
use App\Models\SalidaDetalle;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class RepuestosController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    // muestra lo disponible de cantidades para descontar
    public function bloqueCantidades($id){

        // obtener todas las entradas y obtener cada fila de cantidad

        $lista = EntradaDetalle::where('id_material', $id)
            ->where('cantidad', '>', 0)
            ->get();

        foreach ($lista as $dd){

            $infoEntrada = Entradas::where('id', $dd->id_entrada)->first();
            $dd->fecha = date("d-m-Y", strtotime($infoEntrada->fecha));
            $dd->factura = $infoEntrada->factura;

            // buscar la entrada_detalle de cada salida. obtener la suma de salidas
            $salidaDetalle = SalidaDetalle::where('id_entrada_detalle', $dd->id)
                ->where('id_material', $id)
                ->sum('cantidad');

            // total de la cantidad actual
            $dd->cantidadtotal = $dd->cantidad - $salidaDetalle;
        }

        $hayCantidad = false;
        if(sizeof($lista) > 0){
            $hayCantidad = true;
        }

        return view('backend.admin.registros.modal.modalentrada', compact('lista', 'hayCantidad'));
    }

    // id material
    public function vistaDetalleMaterial($id){

        $info = Materiales::where('id', $id)->first();
        $repuesto = $info->nombre;
        $infoMedida = UnidadMedida::where('id', $info->id_medida)->first();
        $medida = $infoMedida->medida;

        return view('backend.admin.materiales.detalle.vistadetalle', compact('id', 'repuesto', 'medida'));
    }

    // id material
    public function tablaDetalleMaterial($id){

        $lista =  EntradaDetalle::where('id_material', $id)->get();

        $valor = 0;
        foreach ($lista as $data){

            // buscar la entrada_detalle de cada salida. obtener la suma de salidas
            $salidaDetalle = SalidaDetalle::where('id_entrada_detalle', $data->id)
                ->where('id_material', $id)
                ->sum('cantidad');

            $infoEntrada = Entradas::where('id', $data->id_entrada)->first();
            $data->factura = $infoEntrada->factura;
            $data->fecha = date("d-m-Y", strtotime($infoEntrada->fecha));

            $infoEquipo = Equipos::where('id', $data->id_equipo)->first();
            $data->equipo = $infoEquipo->nombre;
            $data->inventario = $infoEntrada->inventario;

            // total de la cantidad actual
            $total = $data->cantidad - $salidaDetalle;

            $totalprecio = $total * $data->precio;
            $data->totalprecio = number_format((float)$totalprecio, 2, '.', ',');
            $valor = $valor + $total;
            $data->total = $valor;
            $valor = 0;
        }

        return view('backend.admin.materiales.detalle.tabladetallematerial', compact('lista'));
    }


}
