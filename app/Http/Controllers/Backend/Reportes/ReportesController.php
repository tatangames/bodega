<?php

namespace App\Http\Controllers\Backend\Reportes;

use App\Http\Controllers\Controller;
use App\Models\Entradas;
use App\Models\Equipos;
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

        // entrada
        if($tipo == 1) {

            // lista de entradas
            $listaEntrada = Entradas::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($listaEntrada as $ll){

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                // 0: el repuesto es nuevo
                // 1: el repuesto ya estaba en bodega
                if($ll->inventario == 0){
                    $ll->tipo = "Repuesto Nuevo";
                }else{
                    $ll->tipo = "Repuesto en Inventario";
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

                    $dd->precio = '$' . number_format((float)$dd->precio, 2, '.', ',');

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

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
            Reporte de Entradas <br></p>
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
                    <td width='8%'>Cantidad</td>
                    <td width='8%'>Precio</td>
                    <td width='8%'>Equipo</td>
                </tr>";

                foreach ($dd->detalle as $gg) {
                    $tabla .= "<tr>
                    <td width='25%'>$gg->nombre</td>
                    <td width='8%'>$gg->medida</td>
                    <td width='8%'>$gg->cantidad</td>
                    <td width='8%'>$gg->precio</td>
                    <td width='8%'>$gg->equipo</td>
                </tr>";
                }

                $tabla .= "</tbody></table>";
            }

            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet,1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            //$mpdf->WriteHTML($tabla,2);
            $mpdf->WriteHTML("<h1>dd</h1>", 2);

            $mpdf->Output();

        }else {
            // salida

            // lista de salidas
            $listaSalida = Salidas::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($listaSalida as $ll){

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                array_push($resultsBloque,$ll);

                // obtener detalle
                $listaDetalle = DB::table('salidas_detalle AS ed')
                    ->join('materiales AS m', 'ed.id_material', '=', 'm.id')
                    ->select('m.nombre', 'ed.cantidad', 'm.id_medida', 'ed.id_equipo')
                    ->where('ed.id_salida', $ll->id)
                    ->orderBy('m.id', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd){
                    if($info = UnidadMedida::where('id', $dd->id_medida)->first()){
                        $dd->medida = $info->medida;
                    }else{
                        $dd->medida = "";
                    }

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

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
            Reporte de Salidas <br></p>
            </div>";

            foreach ($listaSalida as $dd) {

                $tabla .= "<table width='100%' id='tablaFor'>
                    <tbody>";


                $tabla .= "<tr>
                    <td  width='17%'>Fecha</td>
                    <td  width='15%'># Talonario</td>
                </tr>";

                $tabla .= "<tr>
                    <td width='17%'>$dd->fecha</td>
                    <td width='15%'>$dd->talonario</td>";


                if($dd->descripcion != null){
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
                    <td width='8%'>Cantidad</td>
                    <td width='8%'>Equipo</td>
                </tr>";

                foreach ($dd->detalle as $gg) {
                    $tabla .= "<tr>
                    <td width='25%'>$gg->nombre</td>
                    <td width='8%'>$gg->medida</td>
                    <td width='8%'>$gg->cantidad</td>
                    <td width='8%'>$gg->equipo</td>
                </tr>";
                }

                $tabla .= "</tbody></table>";
            }

            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet,1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla,2);

            $mpdf->Output();
        }
    }


    public function reportePorEquipo($desde, $hasta, $tipo, $unidad)
    {

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

                // 0: el repuesto es nuevo
                // 1: el repuesto ya estaba en bodega
                if ($ll->inventario == 0) {
                    $ll->tipo = "Repuesto Nuevo";
                } else {
                    $ll->tipo = "Repuesto en Inventario";
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

                    $dd->precio = '$' . number_format((float)$dd->precio, 2, '.', ',');

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

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
            Fecha: $desdeFormat  &nbsp;-&nbsp; $hastaFormat <br>
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
                    <td width='8%'>Cantidad</td>
                    <td width='8%'>Precio</td>
                    <td width='8%'>Equipo</td>
                </tr>";

                    foreach ($dd->detalle as $gg) {
                        $tabla .= "<tr>
                    <td width='25%'>$gg->nombre</td>
                    <td width='8%'>$gg->medida</td>
                    <td width='8%'>$gg->cantidad</td>
                    <td width='8%'>$gg->precio</td>
                    <td width='8%'>$gg->equipo</td>
                </tr>";
                    }

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

                array_push($resultsBloque, $ll);

                // obtener detalle
                $listaDetalle = DB::table('salidas_detalle AS ed')
                    ->join('materiales AS m', 'ed.id_material', '=', 'm.id')
                    ->select('m.nombre', 'ed.cantidad', 'm.id_medida', 'ed.id_equipo')
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

                    $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
                    $dd->equipo = $infoEquipo->nombre;
                }

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
                    <td width='8%'>Cantidad</td>
                    <td width='8%'>Equipo</td>
                </tr>";

                    foreach ($dd->detalle as $gg) {
                        $tabla .= "<tr>
                    <td width='25%'>$gg->nombre</td>
                    <td width='8%'>$gg->medida</td>
                    <td width='8%'>$gg->cantidad</td>
                    <td width='8%'>$gg->equipo</td>
                </tr>";
                    }

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

}
