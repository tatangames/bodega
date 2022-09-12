<?php

namespace App\Http\Controllers\Backend\Reportes;

use App\Http\Controllers\Controller;
use App\Models\EntradaDetalle;
use App\Models\Entradas;
use App\Models\Equipos;
use App\Models\Materiales;
use App\Models\SalidaDetalle;
use App\Models\Salidas;
use App\Models\UnidadMedida;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function indexEntradaReporte(){
        return view('backend.admin.reporte.vistaentradareporte');
    }

    public function reportePdf($tipo, $desde, $hasta){

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $resultsBloque = array();
        $index = 0;

        $desdeFormat = date("d-m-Y", strtotime($desde));
        $hastaFormat = date("d-m-Y", strtotime($hasta));

        // entrada
        if($tipo == 1) {

            // lista de entradas
            $listaEntrada = Entradas::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($listaEntrada as $ll){

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                $totaldinero = 0;
                $totalcantidad = 0;
                $multiplicado = 0;
                $totalsumado = 0;

                // 0: el repuesto es nuevo
                // 1: el repuesto ya estaba en bodega
                if($ll->inventario == 0){
                    $ll->tipo = "Repuesto Nuevo";
                }else{
                    $ll->tipo = "Repuesto de Inventario";
                }

                array_push($resultsBloque,$ll);

                // obtener detalle
                $listaDetalle = DB::table('entradas_detalle AS ed')
                    ->join('materiales AS m', 'ed.id_material', '=', 'm.id')
                    ->select('m.nombre', 'ed.cantidad', 'm.id_medida', 'ed.id_equipo', 'ed.precio')
                    ->where('ed.id_entrada', $ll->id)
                    ->orderBy('m.id', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd){
                    if($info = UnidadMedida::where('id', $dd->id_medida)->first()){
                        $dd->medida = $info->medida;
                    }else{
                        $dd->medida = "";
                    }

                    $totaldinero = $totaldinero + $dd->precio;
                    $totalcantidad = $totalcantidad + $dd->cantidad;

                    $multiplicado = $multiplicado + ($dd->precio * $dd->cantidad);
                    $totalsumado = $totalsumado + $multiplicado;
                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = number_format((float)$dd->precio, 2, '.', ',');

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');
                $ll->totalsumado = number_format((float)$totalsumado, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }



            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Entradas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Entradas <br>
            Fecha: $desdeFormat  -  $hastaFormat</p>
            </div>";

            foreach ($listaEntrada as $dd) {

                $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

                $tabla .= "<tr>
                    <td  width='17%'>Fecha</td>
                    <td  width='15%'>Factura u Orden de Compra</td>
                    <td  width='15%'>Tipo Ingreso</td>
                </tr>
                ";

                $tabla .= "<tr>
                    <td width='17%'>$dd->fecha</td>
                    <td  width='15%'>$dd->factura</td>";

                if ($dd->inventario == 0){

                    $tabla .= "<td  width='15%' >$dd->tipo</td>
                    </tr>";
                }else{
                    $tabla .= "<td  width='15%' style='background-color:#e7f512;'>$dd->tipo</td>
                    </tr>";
                }

                if($dd->descripcion != null){
                    $tabla .= "<tr>
                        <td colspan='3'>Descripción</td>
                    </tr>
                    ";

                    $tabla .= "<tr>
                        <td colspan='3' width='30%'>$dd->descripcion</td>
                    </tr>
                    ";
                }

                $tabla .= "</tbody></table>";

                $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
            <tbody>";

                $tabla .= "<tr>
                    <td width='25%'>Repuesto</td>
                    <td width='8%'>Medida</td>
                    <td width='8%'>Equipo</td>
                    <td width='8%'>Cantidad</td>
                    <td width='8%'>Precio</td>
                    <td width='8%'>Total</td>
                </tr>";

                foreach ($dd->detalle as $gg) {
                    $tabla .= "<tr>
                    <td width='25%'>$gg->nombre</td>
                    <td width='8%'>$gg->medida</td>
                    <td width='8%'>$gg->equipo</td>
                    <td width='8%'>$gg->cantidad</td>
                    <td width='8%'>$$gg->precio</td>
                    <td width='8%'>$$gg->multiplicado</td>
                </tr>";
                }

                $tabla .= "<tr>
                    <td width='25%'>Total</td>
                    <td width='8%'></td>
                    <td width='8%'></td>
                    <td width='8%'></td>
                    <td width='8%'></td>
                    <td width='8%'>$$dd->totalsumado</td>
                </tr>";

                $tabla .= "</tbody></table>";
            }

            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet,1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            //$mpdf->WriteHTML($tabla,2);
            $mpdf->WriteHTML($tabla, 2);

            $mpdf->Output();

        }else {
            // salida

            // lista de salidas
            $listaSalida = Salidas::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($listaSalida as $ll){

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                array_push($resultsBloque, $ll);

                $totalcantidad = 0;
                $multiplicado = 0;
                $totaldinero = 0;

                // obtener detalle
                $listaDetalle = DB::table('salidas_detalle AS ed')
                    ->join('materiales AS m', 'ed.id_material', '=', 'm.id')
                    ->select('m.nombre', 'ed.cantidad', 'm.id_medida', 'ed.id_equipo', 'ed.id_entrada_detalle')
                    ->where('ed.id_salida', $ll->id)
                    ->orderBy('m.id', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd){
                    if($info = UnidadMedida::where('id', $dd->id_medida)->first()){
                        $dd->medida = $info->medida;
                    }else{
                        $dd->medida = "";
                    }

                    $infoEntradaDetalle = EntradaDetalle::where('id', $dd->id_entrada_detalle)->first();

                    $totalcantidad = $totalcantidad + $dd->cantidad;

                    $multiplicado = $multiplicado + ($infoEntradaDetalle->precio * $dd->cantidad);

                    $totaldinero = $totaldinero + $multiplicado;

                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = $infoEntradaDetalle->precio;

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }


            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Salidas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Salidas <br>
            Fecha: $desdeFormat  -  $hastaFormat </p>
            </div>";

            foreach ($listaSalida as $dd) {

                $tabla .= "<table width='100%' id='tablaFor'>
                    <tbody>";

                $tabla .= "<tr>
                    <td width='17%'>Fecha</td>
                    <td width='15%'># Talonario</td>
                </tr>";

                $tabla .= "<tr>
                    <td width='17%'>$dd->fecha</td>
                    <td width='15%'>$dd->talonario</td>";

                if ($dd->descripcion != null) {
                    $tabla .= "<tr>
                        <td colspan='2'>Descripción</td>
                            </tr>
                            ";

                    $tabla .= "<tr>
                        <td colspan='2' width='30%'>$dd->descripcion</td>
                    </tr>
                    ";
                }

                $tabla .= "</tbody></table>";

                $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
            <tbody>";

                $tabla .= "<tr>
                    <td width='25%'>Repuesto</td>
                    <td width='8%'>Medida</td>
                    <td width='8%'>Equipo</td>
                    <td width='20px'>Cantidad</td>
                    <td width='8%'>Precio</td>
                    <td width='8%'>Total</td>
                </tr>";

                foreach ($dd->detalle as $gg) {
                    $tabla .= "<tr>
                        <td width='25%'>$gg->nombre</td>
                        <td width='8%'>$gg->medida</td>
                        <td width='8%'>$gg->equipo</td>
                        <td width='20px'>$gg->cantidad</td>
                        <td width='8%'>$$gg->precio</td>
                        <td width='8%'>$$gg->multiplicado</td>
                    </tr>";
                }

                $tabla .= "<tr>
                    <td width='25%'>Total</td>
                    <td width='8%'></td>
                    <td width='8%'></td>
                    <td width='20px'></td>
                    <td width='8%'></td>
                    <td width='8%'>$$dd->totaldinero</td>
                </tr>";

                $tabla .= "</tbody></table>";
            }

            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet,1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla,2);

            $mpdf->Output();
        }
    }


    public function reportePorEquipo($desde, $hasta, $tipo, $unidad){

        $porciones = explode("-", $unidad);

        $desdeFormat = date("d-m-Y", strtotime($desde));
        $hastaFormat = date("d-m-Y", strtotime($hasta));

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $resultsBloque = array();
        $index = 0;

        $listaEquipos = Equipos::whereIn('id', $porciones)->orderBy('nombre')->get();

        // entrada
        if ($tipo == 1) {

            // lista de entradas
            $listaEntrada = Entradas::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($listaEntrada as $ll) {

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));
                $totaldinero = 0;
                $totalcantidad = 0;
                $multiplicado = 0;
                $totalSumado = 0;

                // 0: el repuesto es nuevo
                // 1: el repuesto ya estaba en bodega
                if ($ll->inventario == 0) {
                    $ll->tipo = "Repuesto Nuevo";
                } else {
                    $ll->tipo = "Repuesto de Inventario";
                }

                array_push($resultsBloque, $ll);

                // obtener detalle
                $listaDetalle = DB::table('entradas_detalle AS ed')
                    ->join('materiales AS m', 'ed.id_material', '=', 'm.id')
                    ->select('m.nombre', 'ed.cantidad', 'm.id_medida', 'ed.id_equipo', 'ed.precio')
                    ->where('ed.id_entrada', $ll->id)
                    ->whereIn('ed.id_equipo', $porciones)
                    ->orderBy('m.id', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd) {
                    if ($info = UnidadMedida::where('id', $dd->id_medida)->first()) {
                        $dd->medida = $info->medida;
                    } else {
                        $dd->medida = "";
                    }

                    $totaldinero = $totaldinero + $dd->precio;
                    $totalcantidad = $totalcantidad + $dd->cantidad;

                    $multiplicado = $multiplicado + ($dd->precio * $dd->cantidad);
                    $totalSumado = $totalSumado + $multiplicado;
                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = number_format((float)$dd->precio, 2, '.', ',');

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            $totalSumado = number_format((float)$totalSumado, 2, '.', ',');

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Entradas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Entradas <br>
            Fecha: $desdeFormat  -  $hastaFormat <br>
            </p>
            </div>";

            $tabla .= "
                <p>Equipos Seleccionados</p>";

            foreach ($listaEquipos as $dd) {
                $tabla .= "<label><strong>$dd->nombre, </strong></label>";
            }

            foreach ($listaEntrada as $dd) {

                if(sizeof($dd->detalle) > 0){

                    $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

                    $tabla .= "<tr>
                    <td  width='17%'>Fecha</td>
                    <td  width='15%'>Factura u Orden de Compra</td>
                    <td  width='15%'>Tipo Ingreso</td>
                </tr>
                ";

                    $tabla .= "<tr>
                    <td width='17%'>$dd->fecha</td>
                    <td  width='15%'>$dd->factura</td>";

                    if ($dd->inventario == 0) {

                        $tabla .= "<td  width='15%' >$dd->tipo</td>
                    </tr>";
                    } else {
                        $tabla .= "<td  width='15%' style='background-color:#e7f512;'>$dd->tipo</td>
                    </tr>";
                    }

                    if ($dd->descripcion != null) {
                        $tabla .= "<tr>
                        <td colspan='3'>Descripción</td>
                    </tr>
                    ";

                        $tabla .= "<tr>
                        <td colspan='3' width='30%'>$dd->descripcion</td>
                    </tr>
                    ";
                    }

                    $tabla .= "</tbody></table>";

                    $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
            <tbody>";

                    $tabla .= "<tr>
                    <td width='25%'>Repuesto</td>
                    <td width='8%'>Medida</td>
                    <td width='8%'>Equipo</td>
                    <td width='8%'>Cantidad</td>
                    <td width='8%'>Precio</td>
                    <td width='8%'>Total</td>
                </tr>";

                    foreach ($dd->detalle as $gg) {
                        $tabla .= "<tr>
                    <td width='25%'>$gg->nombre</td>
                    <td width='8%'>$gg->medida</td>
                    <td width='8%'>$gg->equipo</td>
                    <td width='8%'>$gg->cantidad</td>
                    <td width='8%'>$$gg->precio</td>
                    <td width='8%'>$$gg->multiplicado</td>
                </tr>";
                    }

                    $tabla .= "<tr>
                    <td width='25%'>Total</td>
                    <td width='8%'></td>
                    <td width='8%'></td>
                    <td width='8%'></td>
                    <td width='8%'></td>
                    <td width='8%'>$$totalSumado</td>
                </tr>";

                    $tabla .= "</tbody></table>";
                }
            }

            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet, 1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla, 2);

            $mpdf->Output();

        } else {
            // salida

            // lista de salidas
            $listaSalida = Salidas::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($listaSalida as $ll) {

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                $totaldinero = 0;
                $totalcantidad = 0;
                $multiplicado = 0;

                array_push($resultsBloque, $ll);

                // obtener detalle
                $listaDetalle = DB::table('salidas_detalle AS ed')
                    ->join('materiales AS m', 'ed.id_material', '=', 'm.id')
                    ->select('m.nombre', 'ed.cantidad', 'm.id_medida', 'ed.id_equipo', 'ed.id_entrada_detalle')
                    ->where('ed.id_salida', $ll->id)
                    ->whereIn('ed.id_equipo', $porciones)
                    ->orderBy('m.id', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd) {
                    if ($info = UnidadMedida::where('id', $dd->id_medida)->first()) {
                        $dd->medida = $info->medida;
                    } else {
                        $dd->medida = "";
                    }

                    $infoEntradaDetalle = EntradaDetalle::where('id', $dd->id_entrada_detalle)->first();

                    $totaldinero = $totaldinero + $infoEntradaDetalle->precio;
                    $totalcantidad = $totalcantidad + $dd->cantidad;

                    $multiplicado = $multiplicado + ($infoEntradaDetalle->precio * $dd->cantidad);
                    $totaldinero = $totaldinero + $multiplicado;
                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = number_format((float)$infoEntradaDetalle->precio, 2, '.', ',');

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Salidas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Salidas <br>
            Fecha: $desdeFormat  &nbsp;-&nbsp; $hastaFormat <br>
            </p>
            </div>";

            $tabla .= "
                <p>Equipos Seleccionados</p>";

            foreach ($listaEquipos as $dd) {
                $tabla .= "<label><strong>$dd->nombre, </strong></label>";
            }

            foreach ($listaSalida as $dd) {

                if(sizeof($dd->detalle) > 0){

                    $tabla .= "<table width='100%' id='tablaFor'>
                    <tbody>";


                    $tabla .= "<tr>
                    <td  width='17%'>Fecha</td>
                    <td  width='15%'># Talonario</td>
                </tr>";

                    $tabla .= "<tr>
                    <td width='17%'>$dd->fecha</td>
                    <td width='15%'>$dd->talonario</td>";


                    if ($dd->descripcion != null) {
                        $tabla .= "<tr>
                        <td colspan='2'>Descripción</td>
                            </tr>
                            ";

                        $tabla .= "<tr>
                        <td colspan='2' width='30%'>$dd->descripcion</td>
                    </tr>
                    ";
                    }

                    $tabla .= "</tbody></table>";

                    $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
                    <tbody>";


                    $tabla .= "<tr>
                    <td width='25%'>Repuesto</td>
                    <td width='8%'>Medida</td>
                    <td width='8%'>Equipo</td>
                    <td width='8%'>Cantidad</td>
                    <td width='8%'>Precio</td>
                    <td width='8%'>Total</td>
                </tr>";

                    foreach ($dd->detalle as $gg) {
                        $tabla .= "<tr>
                        <td width='25%'>$gg->nombre</td>
                        <td width='8%'>$gg->medida</td>
                        <td width='8%'>$gg->equipo</td>
                        <td width='8%'>$gg->cantidad</td>
                        <td width='8%'>$gg->precio</td>
                        <td width='8%'>$$gg->multiplicado</td>
                    </tr>";
                    }

                    $tabla .= "<tr>
                        <td width='25%'>Total</td>
                        <td width='8%'></td>
                        <td width='8%'></td>
                        <td width='8%'></td>
                        <td width='8%'></td>
                        <td width='8%'>$$dd->totaldinero</td>
                    </tr>";

                    $tabla .= "</tbody></table>";
                }
            }

            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet, 1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla, 2);

            $mpdf->Output();
        }
    }

    public function indexEntradaReporteEquipos(){
        $equipos = Equipos::orderBy('nombre')->get();
        return view('backend.admin.reporte.equipos.vistaequiporeporte', compact('equipos'));
    }

    public function indexEntradaReporteCantidad(){
        return view('backend.admin.reporte.cantidad.vistacantidadactual');
    }

    public function reportePdfCantidad(){

        $lista = Materiales::orderBy('nombre', 'ASC')->get();

        $dt = Carbon::now();
        $fechaFormat = date("d-m-Y", strtotime($dt));

        $totalDinero = 0;
        $totalCantidad = 0;

        foreach ($lista as $item) {
            if($dataUnidad = UnidadMedida::where('id', $item->id_medida)->first()){
                $item->medida = $dataUnidad->medida;
            }

            // obtener todas las entradas detalle de este material

            $entradaDetalle = EntradaDetalle::where('id_material', $item->id)->get();

            $valor = 0;
            $dinero = 0;
            foreach ($entradaDetalle as $data){

                // buscar la entrada_detalle de cada salida. obtener la suma de salidas
                $salidaDetalle = SalidaDetalle::where('id_entrada_detalle', $data->id)
                    ->where('id_material', $item->id)
                    ->sum('cantidad');

                // restar
                $total = $data->cantidad - $salidaDetalle;
                $valor = $valor + $total;
                if($salidaDetalle > 0){
                    $dinero = $dinero + ($data->precio * $total);
                }
            }

            $totalDinero = $totalDinero + $dinero;
            $totalCantidad = $totalCantidad + $valor;

            $item->total = $valor;
            $item->dinero = number_format((float)$dinero, 2, '.', ',');
        }

        $totalDinero = number_format((float)$totalDinero, 2, '.', ',');

        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('Entradas');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo2.png';

        $tabla = "<div class='content'>
        <img id='logo' src='$logoalcaldia'>
        <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
        Reporte de Repuestos Actuales <br>
        Fecha: $fechaFormat
        </div>";

        $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

        $tabla .= "<tr>
                <td width='25%'>Repuesto</td>
                <td width='8%'>Medida</td>
                <td width='8%'>Cantidad</td>
                <td width='8%'>Precio</td>
            </tr>";

        foreach ($lista as $dd) {
            if ($dd->total > 0){
                $tabla .= "<tr>
                <td width='25%'>$dd->nombre</td>
                <td  width='8%'>$dd->medida</td>
                <td  width='8%'>$dd->total</td>
                <td  width='8%'>$$dd->dinero</td>
                ";
            }
        }

        $tabla .= "<tr>
                <td width='25%'>Total</td>
                <td  width='8%'></td>
                <td  width='8%'>$totalCantidad</td>
                <td  width='8%'>$$totalDinero</td>
                ";

        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/cssregistro.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla, 2);

        $mpdf->Output();
    }


}
