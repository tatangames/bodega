<?php

namespace App\Http\Controllers\Backend\Reportes;

use App\Http\Controllers\Controller;
use App\Models\EntradaDetalle;
use App\Models\EntradaLLantas;
use App\Models\EntradaLLantasDeta;
use App\Models\Entradas;
use App\Models\Equipos;
use App\Models\FirmasLlantas;
use App\Models\Llantas;
use App\Models\Marca;
use App\Models\Materiales;
use App\Models\SalidaDetalle;
use App\Models\SalidaLLantas;
use App\Models\SalidaLLantasDeta;
use App\Models\Salidas;
use App\Models\UbicacionBodega;
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
        return view('backend.admin.repuestos.reporte.vistaentradareporte');
    }

    public function indexEntradaReporteLlanta(){
        return view('backend.admin.llantas.reportes.vistaentradallantareporte');
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

            $totalFinalEntrada = 0;

            foreach ($listaEntrada as $ll){

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                $totaldinero = 0;
                $totalcantidad = 0;
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

                    $multiplicado = $dd->precio * $dd->cantidad;
                    $totalsumado = $totalsumado + $multiplicado;
                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = number_format((float)$dd->precio, 2, '.', ',');

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

                $totalFinalEntrada = $totalFinalEntrada + $totalsumado;
                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');
                $ll->totalsumado = number_format((float)$totalsumado, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            $totalFinalEntrada = number_format((float)$totalFinalEntrada, 2, '.', ',');

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Entradas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Entradas Repuestos<br>
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


            $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='10%' style='font-weight: bold'>Total Dinero</td>
                   <td  width='10%' style='font-weight: bold'>$$totalFinalEntrada</td>
                    ";

            $tabla .= "</tbody></table>";

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

            $totalFinalSalida = 0;

            foreach ($listaSalida as $ll){

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                array_push($resultsBloque, $ll);

                $totalcantidad = 0;
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

                    $multiplicado = $infoEntradaDetalle->precio * $dd->cantidad;

                    $totaldinero = $totaldinero + $multiplicado;

                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = $infoEntradaDetalle->precio;

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

                $totalFinalSalida = $totalFinalSalida + $totaldinero;
                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            $totalFinalSalida = number_format((float)$totalFinalSalida, 2, '.', ',');

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Salidas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Salidas Repuestos<br>
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


            $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='10%' style='font-weight: bold'>Total Dinero</td>
                   <td  width='10%' style='font-weight: bold'>$$totalFinalSalida</td>
                    ";

            $tabla .= "</tbody></table>";



            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet,1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla,2);

            $mpdf->Output();
        }
    }


    public function reportePdfLlanta($tipo, $desde, $hasta){

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $resultsBloque = array();
        $index = 0;

        $desdeFormat = date("d-m-Y", strtotime($desde));
        $hastaFormat = date("d-m-Y", strtotime($hasta));

        $firmas = FirmasLlantas::where('id', 1)->first();

        // entrada
        if($tipo == 1) {

            // lista de entradas
            $listaEntrada = EntradaLLantas::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            $totalFinalEntrada = 0; // dinero final
            $totalLLantaEntrada = 0; // cantidad llanta final
            foreach ($listaEntrada as $ll){

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                $totaldinero = 0;
                $totalcantidad = 0;
                $totalsumado = 0;

                // 0: el repuesto es nuevo
                // 1: el repuesto ya estaba en bodega
                if($ll->inventario == 0){
                    $ll->tipo = "Llanta Nuevo";
                }else{
                    $ll->tipo = "Llanta de Inventario";
                }

                array_push($resultsBloque,$ll);

                // obtener detalle
                $listaDetalle = DB::table('entrada_llanta_deta AS ed')
                    ->join('llantas AS m', 'ed.id_llanta', '=', 'm.id')
                    ->join('marca_llanta AS marca', 'm.id_marca', '=', 'marca.id')
                    ->join('medida_rin AS medi', 'm.id_medida', '=', 'medi.id')
                    ->select('marca.nombre', 'medi.medida', 'ed.cantidad', 'm.id_medida', 'ed.id_ubicacion', 'ed.precio')
                    ->where('ed.id_entrada_llanta', $ll->id)
                    ->orderBy('m.id', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd){

                    $totaldinero = $totaldinero + $dd->precio;
                    $totalcantidad = $totalcantidad + $dd->cantidad;

                    // por fila
                    $multiplicado = $dd->precio * $dd->cantidad;
                    // poor bloque
                    $totalsumado = $totalsumado + $multiplicado;
                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = number_format((float)$dd->precio, 2, '.', ',');

                    $infoBodega = UbicacionBodega::where('id', $dd->id_ubicacion)->first();
                    $dd->ubicacion = $infoBodega->nombre;
                }

                $totalFinalEntrada = $totalFinalEntrada + $totalsumado;
                $totalLLantaEntrada += $totalcantidad;
                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');
                $ll->totalsumado = number_format((float)$totalsumado, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            $totalFinalEntrada = number_format((float)$totalFinalEntrada, 2, '.', ',');

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Entradas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Entradas Llantas<br>
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
                    <td width='25%'>Marca</td>
                    <td width='8%'>Tipo Llanta</td>
                    <td width='8%'>Ubicación</td>
                    <td width='8%'>Cantidad</td>
                    <td width='8%'>Precio</td>
                    <td width='8%'>Total</td>
                </tr>";

                foreach ($dd->detalle as $gg) {
                    $tabla .= "<tr>
                    <td width='25%'>$gg->nombre</td>
                    <td width='8%'>$gg->medida</td>
                    <td width='8%'>$gg->ubicacion</td>
                    <td width='8%'>$gg->cantidad</td>
                    <td width='8%'>$$gg->precio</td>
                    <td width='8%'>$$gg->multiplicado</td>
                </tr>";
                }

                $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>Total</td>
                    <td width='8%'></td>
                    <td width='8%'></td>
                    <td width='8%' style='font-weight: bold'>$dd->totalcantidad</td>
                    <td width='8%'></td>
                    <td width='8%' style='font-weight: bold'>$$dd->totalsumado</td>
                </tr>";

                $tabla .= "</tbody></table>";
            }

            $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>Total Entradas</td>
                    <td width='8%' style='font-weight: bold'>Entrada Dinero</td>
                </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$totalLLantaEntrada</td>
                    <td width='8%' style='font-weight: bold'>$$totalFinalEntrada</td>
                </tr>";

            $tabla .= "</tbody></table>";


            if($firmas->saltopagina == 1) {
                $tabla .= "<pagebreak />";
                $tabla .= "<div style='padding-top: 1px'></div>";
            }


            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: $firmas->distancia'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>F. __________________________________________</td>
                    <td width='25%' style='font-weight: bold'>F. __________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_1</td>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_3</td>
                    </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-size: 16px'>$firmas->nombre_2</td>
                    <td width='25%' style='font-size: 16px'>$firmas->nombre_4</td>
                    </tr>";

            $tabla .= "</tbody></table>";


            $tabla .= "<table width='100%'  id='tablaForTranspa' style='margin-top: $firmas->distancia2'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>F.__________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    </tr>";


            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_5</td>
                    </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_6</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet,1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            //$mpdf->WriteHTML($tabla,2);
            $mpdf->WriteHTML($tabla, 2);

            $mpdf->Output();

        }else {
            // salida

            // lista de salidas
            $listaSalida = SalidaLLantas::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            $totalFinalSalida = 0; // dinero
            $totalLLantaSalida = 0; // cantidad llantas salieron

            foreach ($listaSalida as $ll){

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                array_push($resultsBloque, $ll);

                $totalcantidad = 0;
                $totaldinero = 0;

                // obtener detalle
                $listaDetalle = DB::table('salida_llanta_deta AS ed')
                    ->join('llantas AS m', 'ed.id_llanta', '=', 'm.id')
                    ->join('marca_llanta AS marca', 'm.id_marca', '=', 'marca.id')
                    ->join('medida_rin AS medi', 'm.id_medida', '=', 'medi.id')
                    ->select('marca.nombre', 'medi.medida', 'ed.cantidad', 'm.id_medida', 'ed.id_equipo', 'ed.id_l_entrada_detalle')
                    ->where('ed.id_salida_llanta', $ll->id)
                    ->orderBy('marca.nombre', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd){

                    $infoEntradaDetalle = EntradaLLantasDeta::where('id', $dd->id_l_entrada_detalle)->first();

                    $totalcantidad = $totalcantidad + $dd->cantidad;

                    // por fila
                    $multiplicado = $infoEntradaDetalle->precio * $dd->cantidad;

                    $totaldinero = $totaldinero + $multiplicado;

                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = $infoEntradaDetalle->precio;

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

                $totalFinalSalida = $totalFinalSalida + $totaldinero;
                $totalLLantaSalida += $totalcantidad;
                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            $totalFinalSalida = number_format((float)$totalFinalSalida, 2, '.', ',');

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Salidas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Salidas Llantas<br>
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
                    <td width='25%'>Marca</td>
                    <td width='8%'>Tipo de Llanta</td>
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
                    <td width='25%' style='font-weight: bold'>Total</td>
                    <td width='8%'></td>
                    <td width='8%'></td>
                    <td width='20px' style='font-weight: bold'>$dd->totalcantidad</td>
                    <td width='8%'></td>
                    <td width='8%' style='font-weight: bold'>$$dd->totaldinero</td>
                </tr>";

                $tabla .= "</tbody></table>";
            }

            $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>Total Salidas</td>
                    <td width='8%' style='font-weight: bold'>Salida Dinero</td>
                </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$totalLLantaSalida</td>
                    <td width='8%' style='font-weight: bold'>$$totalFinalSalida</td>
                </tr>";

            $tabla .= "</tbody></table>";


            if($firmas->saltopagina == 1) {
                $tabla .= "<pagebreak />";
                $tabla .= "<div style='padding-top: 1px'></div>";
            }

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: $firmas->distancia'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>F. __________________________________________</td>
                    <td width='25%' style='font-weight: bold'>F. __________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_1</td>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_3</td>
                    </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-size: 16px'>$firmas->nombre_2</td>
                    <td width='25%' style='font-size: 16px'>$firmas->nombre_4</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%'  id='tablaForTranspa' style='margin-top: $firmas->distancia2'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>F.__________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    </tr>";


            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_5</td>
                    </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_6</td>
                    </tr>";

            $tabla .= "</tbody></table>";


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

            $totalFinalEntrada = 0;

            foreach ($listaEntrada as $ll) {

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));
                $totaldinero = 0;
                $totalcantidad = 0;
                $totalsumado = 0;

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

                    $multiplicado = $dd->precio * $dd->cantidad;
                    $totalsumado = $totalsumado + $multiplicado;
                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = number_format((float)$dd->precio, 2, '.', ',');

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

                $totalFinalEntrada = $totalFinalEntrada + $totalsumado;
                $ll->totalsumado = number_format((float)$totalsumado, 2, '.', ',');

                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            $totalFinalEntrada = number_format((float)$totalFinalEntrada, 2, '.', ',');

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Entradas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Entradas Repuestos<br>
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
                    <td width='8%'>$$dd->totalsumado</td>
                </tr>";

                    $tabla .= "</tbody></table>";
                }
            }

            $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='10%' style='font-weight: bold'>Total Dinero</td>
                   <td  width='10%' style='font-weight: bold'>$$totalFinalEntrada</td>
                    ";

            $tabla .= "</tbody></table>";

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

            $totalFinalSalida = 0;

            foreach ($listaSalida as $ll) {

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                $totaldinero = 0;
                $totalcantidad = 0;

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

                    $totalcantidad = $totalcantidad + $dd->cantidad;

                    $multiplicado = $infoEntradaDetalle->precio * $dd->cantidad;
                    $totaldinero = $totaldinero + $multiplicado;
                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = number_format((float)$infoEntradaDetalle->precio, 2, '.', ',');

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

                $totalFinalSalida = $totalFinalSalida + $totaldinero;
                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            $totalFinalSalida = number_format((float)$totalFinalSalida, 2, '.', ',');

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Salidas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Salidas Repuestos<br>
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
                        <td width='8%'>$$dd->totaldinero</td>
                    </tr>";

                    $tabla .= "</tbody></table>";
                }
            }

            $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='10%' style='font-weight: bold'>Total Dinero</td>
                   <td  width='10%' style='font-weight: bold'>$$totalFinalSalida</td>
                    ";

            $tabla .= "</tbody></table>";

            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet, 1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla, 2);

            $mpdf->Output();
        }
    }

    public function reportePorMarcaLLanta($desde, $hasta, $tipo, $unidad){

        $porciones = explode("-", $unidad);

        $desdeFormat = date("d-m-Y", strtotime($desde));
        $hastaFormat = date("d-m-Y", strtotime($hasta));

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $firmas = FirmasLlantas::where('id', 1)->first();

        $resultsBloque = array();
        $index = 0;

        $listaMarcas = Marca::whereIn('id', $porciones)->orderBy('nombre')->get();

        // entrada
        if ($tipo == 1) {

            // lista de entradas
            $listaEntrada = EntradaLLantas::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            $totalFinalEntrada = 0; // total de dinero
            $totalLLantaEntrada = 0; // total de llantas finales

            foreach ($listaEntrada as $ll) {

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));
                $totaldinero = 0;
                $totalcantidad = 0;
                $totalsumado = 0;

                // 0: el repuesto es nuevo
                // 1: el repuesto ya estaba en bodega
                if ($ll->inventario == 0) {
                    $ll->tipo = "Llanta Nueva";
                } else {
                    $ll->tipo = "Llanta de Inventario";
                }

                array_push($resultsBloque, $ll);

                // obtener detalle
                $listaDetalle = DB::table('entrada_llanta_deta AS ed')
                    ->join('llantas AS m', 'ed.id_llanta', '=', 'm.id')
                    ->join('marca_llanta AS marca', 'm.id_marca', '=', 'marca.id')
                    ->join('medida_rin AS medi', 'm.id_medida', '=', 'medi.id')
                    ->select('marca.nombre', 'medi.medida', 'ed.cantidad', 'm.id_medida', 'ed.id_ubicacion', 'ed.precio')
                    ->where('ed.id_entrada_llanta', $ll->id)
                    ->whereIn('marca.id', $porciones)
                    ->orderBy('marca.nombre', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd) {

                    $totaldinero = $totaldinero + $dd->precio;
                    $totalcantidad = $totalcantidad + $dd->cantidad;

                    $multiplicado = $dd->precio * $dd->cantidad;
                    $totalsumado = $totalsumado + $multiplicado;
                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = number_format((float)$dd->precio, 2, '.', ',');

                    $infoBodega = UbicacionBodega::where('id', $dd->id_ubicacion)->first();
                    $dd->ubicacion = $infoBodega->nombre;
                }

                $totalFinalEntrada = $totalFinalEntrada + $totalsumado;
                $totalLLantaEntrada += $totalcantidad;
                $ll->totalsumado = number_format((float)$totalsumado, 2, '.', ',');

                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            $totalFinalEntrada = number_format((float)$totalFinalEntrada, 2, '.', ',');

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Entradas');


            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Entradas Llantas<br>
            Fecha: $desdeFormat  -  $hastaFormat <br>
            </p>
            </div>";

            $tabla .= "
                <p>Marcas Seleccionadas</p>";

            foreach ($listaMarcas as $dd) {
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
                    <td width='25%'>Marca</td>
                    <td width='8%'>Tipo de Llanta</td>
                    <td width='8%'>Ubicación</td>
                    <td width='8%'>Cantidad</td>
                    <td width='8%'>Precio</td>
                    <td width='8%'>Total</td>
                </tr>";

                    foreach ($dd->detalle as $gg) {
                        $tabla .= "<tr>
                    <td width='25%'>$gg->nombre</td>
                    <td width='8%'>$gg->medida</td>
                    <td width='8%'>$gg->ubicacion</td>
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
            }

            $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>Total Entradas</td>
                    <td width='8%' style='font-weight: bold'>Entrada Dinero</td>
                </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$totalLLantaEntrada</td>
                    <td width='8%' style='font-weight: bold'>$$totalFinalEntrada</td>
                </tr>";



            $tabla .= "</tbody></table>";


            if($firmas->saltopagina == 1) {
                $tabla .= "<pagebreak />";
                $tabla .= "<div style='padding-top: 1px'></div>";
            }

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: $firmas->distancia'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>F. __________________________________________</td>
                    <td width='25%' style='font-weight: bold'>F. __________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_1</td>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_3</td>
                    </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-size: 16px'>$firmas->nombre_2</td>
                    <td width='25%' style='font-size: 16px'>$firmas->nombre_4</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: $firmas->distancia2'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>F.__________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    </tr>";


            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_5</td>
                    </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_6</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet, 1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla, 2);

            $mpdf->Output();

        } else {
            // salida

            // lista de salidas
            $listaSalida = SalidaLLantas::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            $totalFinalSalida = 0;
            $totalLLantaSalida = 0;
            foreach ($listaSalida as $ll) {

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                $totaldinero = 0;
                $totalcantidad = 0;

                array_push($resultsBloque, $ll);

                // obtener detalle
                $listaDetalle = DB::table('salida_llanta_deta AS ed')
                    ->join('llantas AS m', 'ed.id_llanta', '=', 'm.id')
                    ->join('marca_llanta AS marca', 'm.id_marca', '=', 'marca.id')
                    ->join('medida_rin AS medi', 'm.id_medida', '=', 'medi.id')
                    ->select('marca.nombre', 'ed.cantidad', 'medi.medida', 'ed.id_equipo', 'ed.id_l_entrada_detalle')
                    ->where('ed.id_salida_llanta', $ll->id)
                    ->whereIn('m.id_marca', $porciones)
                    ->orderBy('marca.nombre', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd) {
                    $infoEntradaDetalle = EntradaLLantasDeta::where('id', $dd->id_l_entrada_detalle)->first();

                    $totalcantidad = $totalcantidad + $dd->cantidad;

                    $multiplicado = $infoEntradaDetalle->precio * $dd->cantidad;
                    $totaldinero = $totaldinero + $multiplicado;
                    $dd->multiplicado = number_format((float)$multiplicado, 2, '.', ',');

                    $dd->precio = number_format((float)$infoEntradaDetalle->precio, 2, '.', ',');

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }
                $totalLLantaSalida += $totalcantidad;
                $totalFinalSalida = $totalFinalSalida + $totaldinero;
                $ll->totalcantidad = $totalcantidad;
                $ll->totaldinero = number_format((float)$totaldinero, 2, '.', ',');

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            $totalFinalSalida = number_format((float)$totalFinalSalida, 2, '.', ',');

            //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Salidas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Reporte de Salidas Llantas<br>
            Fecha: $desdeFormat  &nbsp;-&nbsp; $hastaFormat <br>
            </p>
            </div>";

            $tabla .= "
                <p>Marcas Seleccionados</p>";

            foreach ($listaMarcas as $dd) {
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

            $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
                    <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>Total Salidas</td>
                    <td width='8%' style='font-weight: bold'>Salida Dinero</td>
                </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$totalLLantaSalida</td>
                    <td width='8%' style='font-weight: bold'>$$totalFinalSalida</td>
                </tr>";

            $tabla .= "</tbody></table>";

            if($firmas->saltopagina == 1) {
                $tabla .= "<pagebreak />";
                $tabla .= "<div style='padding-top: 1px'></div>";
            }

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: $firmas->distancia'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>F. __________________________________________</td>
                    <td width='25%' style='font-weight: bold'>F. __________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_1</td>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_3</td>
                    </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-size: 16px'>$firmas->nombre_2</td>
                    <td width='25%' style='font-size: 16px'>$firmas->nombre_4</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: $firmas->distancia2'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>F.__________________________________________</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    </tr>";


            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_5</td>
                    </tr>";

            $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_6</td>
                    </tr>";

            $tabla .= "</tbody></table>";


            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet, 1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla, 2);

            $mpdf->Output();
        }
    }


    public function indexEntradaReporteEquipos(){
        $equipos = Equipos::orderBy('nombre')->get();
        return view('backend.admin.repuestos.reporte.equipos.vistaequiporeporte', compact('equipos'));
    }

    public function indexEntradaReporteEquiposLlantas(){
        $marcas = Marca::orderBy('nombre')->get();
        return view('backend.admin.llantas.reportes.equipos.vistareportellantaequipo', compact('marcas'));
    }

    public function indexEntradaReporteCantidad(){
        return view('backend.admin.repuestos.reporte.cantidad.vistacantidadactual');
    }

    public function indexEntradaReporteCantidadLlanta(){
        return view('backend.admin.llantas.reportes.cantidad.vistareportellantacantidad');
    }

    public function reportePdfCantidad(){

        $lista = Materiales::orderBy('nombre', 'ASC')->get();

        $dt = Carbon::now();
        $fechaFormat = date("d-m-Y", strtotime($dt));

        $totalDinero = 0;
        $totalCantidad = 0;
        $totalarticulos = 0;

        $resultsBloque = array();
        $index = 0;

        foreach ($lista as $item) {
            array_push($resultsBloque, $item);

            $sumadinerobloque = 0;
            $medida = '';
            if($dataUnidad = UnidadMedida::where('id', $item->id_medida)->first()){
                $medida = $dataUnidad->medida;
            }

            $haybloque = false;

            // obtener todas las entradas detalle de este material

            $entradaDetalle = EntradaDetalle::where('id_material', $item->id)->get();
            $valor = 0;

            foreach ($entradaDetalle as $data){
                $dinerobloque = 0;
                // buscar la entrada_detalle de cada salida. obtener la suma de salidas
                $salidaDetalle = SalidaDetalle::where('id_entrada_detalle', $data->id)
                    ->where('id_material', $item->id)
                    ->sum('cantidad');

                // restar
                $totalActual = $data->cantidad - $salidaDetalle;
                $valor = $valor + $totalActual;

                if($totalActual > 0){
                    $dinerobloque = $dinerobloque + ($data->precio * $totalActual);
                    $sumadinerobloque = $sumadinerobloque + $dinerobloque;
                    $data->dinerobloque = number_format((float)$dinerobloque, 2, '.', ',');
                    $totalarticulos = $totalarticulos + $totalActual;
                }else{
                    $data->dinerobloque = 0;
                }
                if($totalActual > 0){
                    $haybloque = true;
                }

                $data->totalactual = $totalActual;
            }

            $totalCantidad = $totalCantidad + $valor;
            $item->medida = $medida;
            $item->total = $valor;

            $item->haybloque = $haybloque;

            $totalDinero = $totalDinero + $sumadinerobloque;
            $item->sumadinerobloque = number_format((float)$sumadinerobloque, 2, '.', ',');

            $resultsBloque[$index]->detalle = $entradaDetalle;
            $index++;
        }

        $totalDinero = number_format((float)$totalDinero, 2, '.', ',');

        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('Actuales');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo2.png';

        $tabla = "<div class='content'>
        <img id='logo' src='$logoalcaldia'>
        <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
        Reporte de Repuestos Actuales <br>
        Fecha: $fechaFormat
        </div>";

        foreach ($lista as $ll) {

            if($ll->haybloque){

                $tabla .= "<table width='100%' id='tablaFor'>
                <tbody>";

                $tabla .= "<tr>
                    <td width='25%'>Repuesto</td>
                    <td width='8%'>Código</td>
                    <td width='8%'>Medida</td>
                </tr>";

                $tabla .= "<tr>
                    <td width='25%'>$ll->nombre</td>
                    <td width='8%'>$ll->codigo</td>
                    <td width='8%'>$ll->medida</td>
                </tr>";

                $tabla .= "</tbody></table>";

                $tabla .= "<table width='100%' id='tablaFor'>
                <tbody>";

                $unavuelta = true;

                foreach ($ll->detalle as $dd) {

                    if ($dd->totalactual > 0) {

                        if ($unavuelta) {
                            $unavuelta = false;
                            $tabla .= "<tr>
                            <td width='10%'>Cantidad</td>
                            <td width='10%'>Precio</td>
                            <td width='10%'>Total</td>
                            </tr>";
                        }

                                $tabla .= "<tr>
                        <td width='10%'>$dd->totalactual</td>
                        <td width='10%'>$$dd->precio</td>
                        <td width='10%'>$$dd->dinerobloque</td>
                        </tr>";
                    }
                }

                $tabla .= "<tr>
                        <td width='10%'></td>
                        <td width='10%'></td>
                        <td width='10%'>$$ll->sumadinerobloque</td>
                        </tr>";

                $tabla .= "</tbody></table>";
            }
        }

        $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

            $tabla .= "<tr>
                    <td width='10%' style='font-weight: bold'>Total Repuestos</td>
                    <td  width='10%' style='font-weight: bold'>Total Dinero</td>
                    ";

        $tabla .= "<tr>
                    <td  width='10%' style='font-weight: bold'>$totalCantidad</td>
                    <td  width='10%' style='font-weight: bold'>$$totalDinero</td>
                    ";

        $tabla .= "</tbody></table>";

        $stylesheet = file_get_contents('css/cssregistro.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla, 2);

        $mpdf->Output();
    }


    public function reportePdfCantidadLlanta(){
        //$lista = Llantas::orderBy('nombre', 'ASC')->get();

        $lista = DB::table('llantas AS lla')
            ->join('marca_llanta AS marca', 'lla.id_marca', '=', 'marca.id')
            ->join('medida_rin AS medi', 'lla.id_medida', '=', 'medi.id')
            ->select('marca.nombre', 'medi.medida', 'lla.id')
            ->orderBy('marca.nombre', 'ASC')
            ->get();

        $firmas = FirmasLlantas::where('id', 1)->first();

        $dt = Carbon::now();
        $fechaFormat = date("d-m-Y", strtotime($dt));

        $totalDinero = 0;
        $totalCantidad = 0;
        $totalarticulos = 0;

        $resultsBloque = array();
        $index = 0;

        foreach ($lista as $item) {
            array_push($resultsBloque, $item);

            $sumadinerobloque = 0;

            // obtener todas las entradas detalle de este material

            $entradaDetalle = EntradaLLantasDeta::where('id_llanta', $item->id)->get();
            $valor = 0;
            $haybloque = false;

            foreach ($entradaDetalle as $data){
                $dinerobloque = 0;
                // buscar la entrada_detalle de cada salida. obtener la suma de salidas
                $salidaDetalle = SalidaLLantasDeta::where('id_l_entrada_detalle', $data->id)
                    ->where('id_llanta', $item->id)
                    ->sum('cantidad');

                $infoBodega = UbicacionBodega::where('id', $data->id_ubicacion)->first();

                // restar
                $totalActual = $data->cantidad - $salidaDetalle;
                $valor = $valor + $totalActual;

                if($totalActual > 0){

                    $dinerobloque = $dinerobloque + ($data->precio * $totalActual);

                    $sumadinerobloque = $sumadinerobloque + $dinerobloque;
                    $data->dinerobloque = number_format((float)$dinerobloque, 2, '.', ',');
                    $totalarticulos = $totalarticulos + $totalActual;
                }else{
                    $data->dinerobloque = 0;
                }

                if($totalActual > 0){
                    $haybloque = true;
                }

                $data->ubicacion = $infoBodega->nombre;
                $data->totalactual = $totalActual;
            }

            $totalCantidad = $totalCantidad + $valor;
            $item->total = $valor;
            $totalDinero = $totalDinero + $sumadinerobloque;

            $item->haybloque = $haybloque;

            $item->sumadinerobloque = number_format((float)$sumadinerobloque, 2, '.', ',');
            $item->sumadinerobloqueformat = number_format((float)$sumadinerobloque, 2, '.', ',');

            $resultsBloque[$index]->detalle = $entradaDetalle;
            $index++;
        }

        $totalDinero = number_format((float)$totalDinero, 2, '.', ',');

        //$mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('Actuales');

        $mpdf->AddPage();
        $mpdf->WriteHTML('', 2);


        $logoalcaldia = 'images/logo2.png';

        $tabla = "<div class='content'>
        <img id='logo' src='$logoalcaldia'>
        <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
        Reporte de Llantas Actuales <br>
        Fecha: $fechaFormat
        </div>";

        foreach ($lista as $ll){

            if($ll->haybloque){

                $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

                $tabla .= "<tr>
                <td width='15%' >Marca</td>
                <td width='8%'>Tipo Llanta</td>
            </tr>";

                $tabla .= "<tr>
                <td width='15%' >$ll->nombre</td>
                <td width='8%'>$ll->medida</td>
            </tr>";

                $tabla .= "</tbody></table>";

                $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

                $unavuelta = true;

                foreach ($ll->detalle as $dd){

                    if($dd->totalactual > 0){

                        if($unavuelta){
                            $unavuelta = false;

                            $tabla .= "<tr>
                                <td width='10%'>Ubicación</td>
                                <td width='10%'>Cantidad</td>
                                <td width='10%'>Precio</td>
                                <td width='10%'>Total</td>
                                </tr>";
                        }

                            $tabla .= "<tr>
                                <td width='10%'>$dd->ubicacion</td>
                                <td width='10%'>$dd->totalactual</td>
                                <td width='10%'>$$dd->precio</td>
                                <td width='10%'>$$dd->dinerobloque</td>
                                </tr>";
                    }
                }

                $tabla .= "<tr>
                        <td width='10%' style='font-weight: bold'>Total</td>
                        <td width='10%' style='font-weight: bold'>$ll->total</td>
                        <td width='10%'></td>
                        <td width='10%' style='font-weight: bold'>$$ll->sumadinerobloqueformat</td>
                        </tr>";

                $tabla .= "</tbody></table>";
            }
        }

        $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 25px'>
            <tbody>";

        $tabla .= "<tr>
                    <td width='10%' style='font-weight: bold'>Total LLantas</td>
                    <td  width='10%' style='font-weight: bold'>Total Dinero</td>
                    ";

        $tabla .= "<tr>
                    <td  width='10%' style='font-weight: bold'>$totalCantidad</td>
                    <td  width='10%' style='font-weight: bold'>$$totalDinero</td>
                    ";

        $tabla .= "</tbody></table>";

        if($firmas->saltopagina == 1) {
            $tabla .= "<pagebreak />";
            $tabla .= "<div style='padding-top: 1px'></div>";
        }


        $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: $firmas->distancia'>
            <tbody>";

        $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>F. __________________________________________</td>
                    <td width='25%' style='font-weight: bold'>F. __________________________________________</td>
                    </tr>";

        $tabla .= "</tbody></table>";

        $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

        $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    </tr>";

        $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_1</td>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_3</td>
                    </tr>";

        $tabla .= "<tr>
                    <td width='25%' style='font-size: 16px'>$firmas->nombre_2</td>
                    <td width='25%' style='font-size: 16px'>$firmas->nombre_4</td>
                    </tr>";

        $tabla .= "</tbody></table>";

        $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: $firmas->distancia2'>
            <tbody>";

        $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>F.__________________________________________</td>
                    </tr>";

        $tabla .= "</tbody></table>";

        $tabla .= "<table width='100%' id='tablaForTranspa' style='margin-top: 35px'>
            <tbody>";

        $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>N. __________________________________________</td>
                    </tr>";

        $tabla .= "<tr>
                    <td width='25%' style='font-weight: bold'>$firmas->nombre_5</td>
                    </tr>";


        $tabla .= "<tr>
                    <td width='25%' style='font-size: 16px'>$firmas->nombre_6</td>
                    </tr>";

        $tabla .= "</tbody></table>";


        $stylesheet = file_get_contents('css/cssregistro.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla, 2);

        $mpdf->Output();
    }


}
