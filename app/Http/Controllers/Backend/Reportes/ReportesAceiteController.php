<?php

namespace App\Http\Controllers\Backend\Reportes;

use App\Http\Controllers\Controller;
use App\Models\EmpresaLicitacion;
use App\Models\EntradaAceites;
use App\Models\EntradaAceitesDetalle;
use App\Models\Equipos;
use App\Models\MaterialesAceites;
use App\Models\RegistroAceiteDetalle;
use App\Models\SalidaAceites;
use App\Models\SalidaAceitesDetalle;
use App\Models\UbicacionBodega;
use App\Models\UnidadMedidaAceites;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesAceiteController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function indexEntradaReporte(){
        return view('backend.admin.repuestos.reporte.aceites.entradasalidas.vistareporteentradaaceites');
    }

    public function reportePdfEntradaAceite($tipo, $desde, $hasta){

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $resultsBloque = array();
        $index = 0;

        $desdeFormat = date("d-m-Y", strtotime($desde));
        $hastaFormat = date("d-m-Y", strtotime($hasta));

        // entrada
        if($tipo == 1) {

            // lista de entradas
            $listaEntrada = EntradaAceites::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($listaEntrada as $ll){

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                $infoEmpresa = EmpresaLicitacion::where('id', $ll->id_empresa)->first();
                $ll->empresa = $infoEmpresa->nombre;

                array_push($resultsBloque,$ll);

                // obtener detalle
                $listaDetalle = DB::table('entrada_aceites_detalle AS ed')
                    ->join('materiales_aceites AS m', 'ed.id_material_aceite', '=', 'm.id')
                    ->select('m.nombre', 'ed.cantidad', 'm.id_medida', 'm.tipo', 'ed.id_ubicacion')
                    ->where('ed.id_entrada_aceite', $ll->id)
                    ->orderBy('m.id', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd){
                    if($info = UnidadMedidaAceites::where('id', $dd->id_medida)->first()){
                        $dd->medida = $info->nombre;
                    }else{
                        $dd->medida = "";
                    }

                    $infoUbicacion = UbicacionBodega::where('id', $dd->id_ubicacion)->first();
                    $dd->ubicacion = $infoUbicacion->nombre;

                    if($dd->tipo == 1){
                        $dd->tipoingreso = "Aceites";
                    }else{
                        $dd->tipoingreso = "Lubricantes";
                    }
                }

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            //$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Entradas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Entradas Aceites y Lubricantes<br>
            Fecha: $desdeFormat  -  $hastaFormat</p>
            </div>";

            foreach ($listaEntrada as $dd) {

                $tabla .= "<table width='100%' id='tablaFor'>
            <tbody>";

                $tabla .= "<tr>
                    <td  width='17%'>Fecha</td>
                    <td  width='15%'>Licitación</td>

                </tr>
                ";

                $tabla .= "<tr>
                    <td width='17%'>$dd->fecha</td>
                    <td  width='15%'>$dd->empresa</td>";


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
                    <td width='25%'>Material</td>
                    <td width='8%'>Medida</td>
                    <td width='8%'>Ubicación</td>
                    <td width='8%'>Cantidad</td>
                    <td width='10%'>Tipo Ingreso</td>
                </tr>";

                foreach ($dd->detalle as $gg) {
                    $tabla .= "<tr>
                    <td width='25%'>$gg->nombre</td>
                    <td width='8%'>$gg->medida</td>
                    <td width='8%'>$gg->ubicacion</td>
                    <td width='8%'>$gg->cantidad</td>
                    <td width='10%'>$gg->tipoingreso</td>
                </tr>";
                }

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
            $listaSalida = SalidaAceites::whereBetween('fecha', [$start, $end])
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($listaSalida as $ll){

                $ll->fecha = date("d-m-Y", strtotime($ll->fecha));

                array_push($resultsBloque, $ll);

                // obtener detalle
                $listaDetalle = DB::table('salida_aceites_detalle AS ed')
                    ->join('materiales_aceites AS m', 'ed.id_material_aceite', '=', 'm.id')
                    ->select('m.nombre', 'ed.cantidad', 'm.id_medida', 'm.tipo', 'ed.id_entrada_aceite_deta')
                    ->where('ed.id_salida_aceites', $ll->id)
                    ->orderBy('m.id', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd){
                    if($info = UnidadMedidaAceites::where('id', $dd->id_medida)->first()){
                        $dd->medida = $info->nombre;
                    }else{
                        $dd->medida = "";
                    }

                    if($dd->tipo == 1){
                        $dd->tipoaceite = "Aceites";
                    }else{
                        $dd->tipoaceite = "Lubricantes";
                    }

                    $infoEntradaDeta = EntradaAceitesDetalle::where('id', $dd->id_entrada_aceite_deta)->first();
                    $infoEntrada = EntradaAceites::where('id', $infoEntradaDeta->id_entrada_aceite)->first();

                    $infoEmpresa = EmpresaLicitacion::where('id', $infoEntrada->id_empresa)->first();
                    $dd->empresa = $infoEmpresa->nombre;
                }

                $resultsBloque[$index]->detalle = $listaDetalle;
                $index++;
            }

            $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
            //$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
            $mpdf->SetTitle('Salidas');

            // mostrar errores
            $mpdf->showImageErrors = false;

            $logoalcaldia = 'images/logo2.png';

            $tabla = "<div class='content'>
            <img id='logo' src='$logoalcaldia'>
            <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
            Salidas Aceites y Lubricantes<br>
            Fecha: $desdeFormat  -  $hastaFormat </p>
            </div>";

            foreach ($listaSalida as $dd) {

                $tabla .= "<table width='100%' id='tablaFor'>
                    <tbody>";

                $tabla .= "<tr>
                    <td  width='13%' style='font-weight: bold'>Fecha</td>
                    <td  width='15%' style='font-weight: bold'>Descripción</td>
                </tr>
                ";

                $tabla .= "<tr>
                    <td width='13%'>$dd->fecha</td>
                    <td width='15%'>$dd->descripcion</td>";

                $tabla .= "</tbody></table>";

                $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
            <tbody>";

                $tabla .= "<tr>
                    <td width='25%'>Material</td>
                    <td width='8%'>Medida</td>
                    <td width='20px'>Cantidad</td>
                    <td width='20px'>Tipo</td>
                    <td width='20px'>Licitación</td>
                </tr>";

                foreach ($dd->detalle as $gg) {
                    $tabla .= "<tr>
                        <td width='25%'>$gg->nombre</td>
                        <td width='8%'>$gg->medida</td>
                        <td width='20px'>$gg->cantidad</td>
                        <td width='20px'>$gg->tipoaceite</td>
                        <td width='20px'>$gg->empresa</td>
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

    public function indexEntradaReporteEquiposAceites(){
        $equipos = Equipos::orderBy('nombre')->get();
        return view('backend.admin.repuestos.reporte.aceites.equipos.vistaequiporeporteaceites', compact('equipos'));
    }

    public function reportePorEquipoAceites($desde, $hasta, $unidad){

        $porciones = explode("-", $unidad);

        $desdeFormat = date("d-m-Y", strtotime($desde));
        $hastaFormat = date("d-m-Y", strtotime($hasta));

        $start = Carbon::parse($desde)->startOfDay();
        $end = Carbon::parse($hasta)->endOfDay();

        $resultsBloque = array();
        $index = 0;

        $listaEquipos = Equipos::whereIn('id', $porciones)->orderBy('nombre')->get();

        foreach ($listaEquipos as $ll) {

            array_push($resultsBloque, $ll);

            // obtener detalle
            $listaDetalle = RegistroAceiteDetalle::whereBetween('fecha', [$start, $end])
                ->where('id_equipo', $ll->id)
                ->orderBy('fecha', 'ASC')
                ->get();

            foreach ($listaDetalle as $dd) {

                $medida = "";
                if ($infoMedida = UnidadMedidaAceites::where('id', $dd->id_medida)->first()) {
                    $medida = $infoMedida->nombre;
                }
                $dd->medida = $medida;

                $fecha = date("d-m-Y", strtotime($dd->fecha));

                if($dd->hora != null){
                    $hora = date("g:i A", strtotime($dd->hora));
                    $fecha .= " " . $hora;
                }

                $dd->fecha = $fecha;

                $infoSalidaAceiteDeta = SalidaAceitesDetalle::where('id', $dd->id_salida_acei_deta)->first();
                $infoMaterial = MaterialesAceites::where('id', $infoSalidaAceiteDeta->id_material_aceite)->first();

                if($infoMaterial->tipo == 1){
                    $dd->tipoaceite = "Aceites";
                }else{
                    $dd->tipoaceite = "Lubricantes";
                }

                $dd->nombrematerial = $infoMaterial->nombre;

                $infoEntradaAceiteDeta = EntradaAceitesDetalle::where('id', $infoSalidaAceiteDeta->id_entrada_aceite_deta)->first();
                $infoEntradaAceite = EntradaAceites::where('id', $infoEntradaAceiteDeta->id_entrada_aceite)->first();
                $infoEmpresa = EmpresaLicitacion::where('id', $infoEntradaAceite->id_empresa)->first();
                $dd->empresa = $infoEmpresa->nombre;
            }

            $resultsBloque[$index]->detalle = $listaDetalle;
            $index++;
        }


        $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER', 'orientation' => 'L']);
        //$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER', 'orientation' => 'L']);
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

        foreach ($listaEquipos as $dd) {

            $tabla .= "<table width='100%' id='tablaFor'><tbody>";

            $tabla .= "<tr>
                    <td  width='17%'>Equipo: $dd->nombre</td>
                    </tr>";

            $tabla .= "</tbody></table>";

            $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
                    <tbody>";

            $tabla .= "<tr>
                            <td width='12%'>Fecha</td>
                            <td width='12%'>Material</td>
                            <td width='12%'>Licitación</td>
                            <td width='10%'>Cantidad Salida</td>
                            <td width='10%'>Medida</td>
                            <td width='8%'>Tipo</td>
                            <td width='12%'>Descripción</td>
                        </tr>";

            foreach ($dd->detalle as $gg) {
                $tabla .= "<tr>
                            <td width='12%'>$gg->fecha</td>
                            <td width='12%'>$gg->nombrematerial</td>
                            <td width='12%'>$gg->empresa</td>
                            <td width='10%'>$gg->cantidad_salida</td>
                            <td width='10%'>$gg->medida</td>
                            <td width='8%'>$gg->tipoaceite</td>
                            <td width='12%'>$gg->descripcion</td>
                        </tr>";
            }

            $tabla .= "</tbody></table>";
        }

        $stylesheet = file_get_contents('css/cssregistro.css');
        $mpdf->WriteHTML($stylesheet, 1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla, 2);

        $mpdf->Output();
    }


    public function indexEntradaReporteCantidadAceites(){
        return view('backend.admin.repuestos.reporte.aceites.cantidad.vistacantidadactualaceites');
    }


    public function reportePdfCantidadAceites(){

        $lista = MaterialesAceites::orderBy('nombre', 'ASC')->get();

        $dt = Carbon::now();
        $fechaFormat = date("d-m-Y", strtotime($dt));

        $totalCantidad = 0;
        $totalarticulos = 0;

        $resultsBloque = array();
        $index = 0;

        foreach ($lista as $item) {
            array_push($resultsBloque, $item);

            $medida = '';
            if($dataUnidad = UnidadMedidaAceites::where('id', $item->id_medida)->first()){
                $medida = $dataUnidad->nombre;
            }

            if($item->tipo == 1){
                $item->tipoaceite = "Aceites";
            }else{
                $item->tipoaceite = "Lubricantes";
            }

            $haybloque = false;

            // obtener licitación
            $licitacion = "";

            // OBTENER NOMBRE DE LCIITACIÓN, COMO SE VERIFICA POR MATERIAL, ESTO PODRIA SER NULL
            // ASI QUE SE SIEMPRE SE MOSTRARA NOMBRE YA QUE DEBE HABER ENTRADA ACEITE DETALLE
            if($infoEntraDeta = EntradaAceitesDetalle::where('id_material_aceite', $item->id)->first()){
                $infoEntradaAceite = EntradaAceites::where('id', $infoEntraDeta->id_entrada_aceite)->first();
                $infoEmpresa = EmpresaLicitacion::where('id', $infoEntradaAceite->id_empresa)->first();
                $licitacion = $infoEmpresa->nombre;
            }

            $item->licitacion = $licitacion;

            // obtener todas las entradas detalle de este material
            $entradaDetalle = EntradaAceitesDetalle::where('id_material_aceite', $item->id)->get();
            $valor = 0;

            foreach ($entradaDetalle as $data){

                // buscar la entrada_detalle de cada salida. obtener la suma de salidas
                $salidaDetalle = SalidaAceitesDetalle::where('id_entrada_aceite_deta', $data->id)
                    ->where('id_material_aceite', $item->id)
                    ->sum('cantidad');

                // restar
                $totalActual = $data->cantidad - $salidaDetalle;
                $valor = $valor + $totalActual;

                if($totalActual > 0){
                    $totalarticulos = $totalarticulos + $totalActual;
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

            $resultsBloque[$index]->detalle = $entradaDetalle;
            $index++;
        }


        $mpdf = new \Mpdf\Mpdf(['format' => 'LETTER']);
        //$mpdf = new \Mpdf\Mpdf(['tempDir' => sys_get_temp_dir(), 'format' => 'LETTER']);
        $mpdf->SetTitle('Actuales');

        // mostrar errores
        $mpdf->showImageErrors = false;

        $logoalcaldia = 'images/logo2.png';

        $tabla = "<div class='content'>
        <img id='logo' src='$logoalcaldia'>
        <p id='titulo'>ALCALDÍA MUNICIPAL DE METAPÁN <br>
        Aceite y Lubricantes Actuales <br>
        Fecha: $fechaFormat
        </div>";

        foreach ($lista as $ll) {

            if($ll->haybloque){

                $tabla .= "<table width='100%' id='tablaFor'>
                <tbody>";

                $tabla .= "<tr>
                    <td width='13%' style='font-weight: bold'>Material</td>
                    <td width='10%' style='font-weight: bold'>Medida</td>
                    <td width='14%' style='font-weight: bold'>Licitación</td>
                    <td width='8%' style='font-weight: bold'>Tipo</td>
                    <td width='8%' style='font-weight: bold'>Total</td>
                </tr>";

                $tabla .= "<tr>
                    <td width='13%'>$ll->nombre</td>
                    <td width='10%'>$ll->medida</td>
                    <td width='14%'>$ll->licitacion</td>
                    <td width='8%'>$ll->tipoaceite</td>
                    <td width='8%'>$ll->total</td>
                </tr>";

                $tabla .= "</tbody></table>";
            }
        }

        $stylesheet = file_get_contents('css/cssregistro.css');
        $mpdf->WriteHTML($stylesheet,1);

        $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
        $mpdf->WriteHTML($tabla, 2);

        $mpdf->Output();
    }


}
