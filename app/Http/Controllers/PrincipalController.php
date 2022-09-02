<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PrincipalController extends Controller
{

    public function index(){
        $ruta = 'admin.unidadmedida.index';
        return view('backend.index', compact( 'ruta'));
    }

    public function indexUnidadMedida(){
        return view('backend.admin.medida.vistaunidadmedida');
    }

    public function tablaUnidadMedida(){
        $lista = UnidadMedida::orderBy('medida', 'ASC')->get();
        return view('backend.admin.medida.tablaunidadmedida', compact('lista'));
    }

    public function nuevaUnidadMedida(Request $request){
        $regla = array(
            'medida' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new UnidadMedida();
        $dato->medida = $request->medida;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionUnidadMedida(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = UnidadMedida::where('id', $request->id)->first()){

            return ['success' => 1, 'medida' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarUnidadMedida(Request $request){

        $regla = array(
            'id' => 'required',
            'medida' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(UnidadMedida::where('id', $request->id)->first()){

            UnidadMedida::where('id', $request->id)->update([
                'medida' => $request->medida
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    // **********************************************************************

    public function indexMateriales(){
        $lUnidad = UnidadMedida::orderBy('medida', 'ASC')->get();
        return view('backend.admin.materiales.vistacatalogomateriales', compact('lUnidad'));
    }

    public function tablaMateriales(){
        $lista = Materiales::orderBy('nombre', 'ASC')->get();

        foreach ($lista as $item) {
            if($dataUnidad = UnidadMedida::where('id', $item->id_medida)->first()){
                $item->medida = $dataUnidad->medida;
            }

            $cantidadEntrada = EntradaDetalle::where('id_material', $item->id)->sum('cantidad');
            $cantidadSalida = SalidaDetalle::where('id_material', $item->id)->sum('cantidad');

            $item->cantidad = $cantidadEntrada - $cantidadSalida;
        }

        return view('backend.admin.materiales.tablacatalogomateriales', compact('lista'));
    }

    public function nuevoMaterial(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Materiales::where('nombre', $request->nombre)
            ->where('id_medida', $request->unidad)
            ->first()){
            return ['success' => 3];
        }

        $dato = new Materiales();
        $dato->id_medida = $request->unidad;
        $dato->nombre = $request->nombre;
        $dato->codigo = $request->codigo;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionMaterial(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Materiales::where('id', $request->id)->first()){

            $arrayUnidad = UnidadMedida::orderBy('medida', 'ASC')->get();

            return ['success' => 1, 'material' => $lista, 'unidad' => $arrayUnidad];
        }else{
            return ['success' => 2];
        }
    }

    public function editarMaterial(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Materiales::where('id', '!=', $request->id)
            ->where('nombre', $request->nombre)
            ->where('id_medida', $request->unidad)
            ->first()){
            return ['success' => 3];
        }

        Materiales::where('id', $request->id)->update([
            'id_medida' => $request->unidad,
            'nombre' => $request->nombre,
            'codigo' => $request->codigo
        ]);

        return ['success' => 1];
    }

    //***********************************************************

    public function indexRegistroEntrada(){

        $equipos = Equipos::orderBy('nombre')->get();

        return view('backend.admin.registros.entradaregistro', compact('equipos'));
    }

    public function buscadorMaterial(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $data = Materiales::where('nombre', 'LIKE', "%{$query}%")
                ->orWhere('codigo', 'LIKE', "%{$query}%")
                ->get();

            foreach ($data as $dd){
                if($info = UnidadMedida::where('id', $dd->id_medida)->first()){
                    $dd->medida = "- " . $info->medida;
                }else{
                    $dd->medida = "";
                }
            }

            $output = '<ul class="dropdown-menu" style="display:block; position:relative;">';
            $tiene = true;
            foreach($data as $row){

                // si solo hay 1 fila, No mostrara el hr, salto de linea
                if(count($data) == 1){
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' ' .$row->medida .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' ' .$row->medida .'</a></li>
                   <hr>
                ';
                    }
                }
            }
            $output .= '</ul>';
            if($tiene){
                $output = '';
            }
            echo $output;
        }
    }

    public function guardarEntrada(Request $request){

        $rules = array(
            'fecha' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            if ($request->hasFile('documento')) {

                $cadena = Str::random(15);
                $tiempo = microtime();
                $union = $cadena . $tiempo;
                $nombre = str_replace(' ', '_', $union);

                $extension = '.' . $request->documento->getClientOriginalExtension();
                $nomDocumento = $nombre . strtolower($extension);
                $avatar = $request->file('documento');
                $archivo = Storage::disk('archivos')->put($nomDocumento, \File::get($avatar));

                if($archivo){

                    $r = new Entradas();
                    $r->fecha = $request->fecha;
                    $r->descripcion = $request->descripcion;
                    $r->documento = null;
                    $r->inventario = $request->entrada;
                    $r->factura = $request->factura;
                    $r->save();

                    for ($i = 0; $i < count($request->cantidad); $i++) {

                        $rDetalle = new EntradaDetalle();
                        $rDetalle->id_entrada = $r->id;
                        $rDetalle->id_material = $request->datainfo[$i];
                        $rDetalle->cantidad = $request->cantidad[$i];
                        $rDetalle->precio = $request->precio[$i];
                        $rDetalle->id_equipo = $request->equipo[$i];
                        $rDetalle->save();
                    }

                    DB::commit();
                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                }
            }
            else{

                $r = new Entradas();
                $r->fecha = $request->fecha;
                $r->descripcion = $request->descripcion;
                $r->documento = null;
                $r->inventario = $request->entrada;
                $r->factura = $request->factura;
                $r->save();


                for ($i = 0; $i < count($request->cantidad); $i++) {

                    $rDetalle = new EntradaDetalle();
                    $rDetalle->id_entrada = $r->id;
                    $rDetalle->id_material = $request->datainfo[$i];
                    $rDetalle->cantidad = $request->cantidad[$i];
                    $rDetalle->precio = $request->precio[$i];
                    $rDetalle->id_equipo = $request->equipo[$i];
                    $rDetalle->save();
                }

                DB::commit();
                return ['success' => 1];
            }
        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 2];
        }
    }


    //**********************************************************************

    public function indexRegistroSalida(){
        return view('backend.admin.registros.salidaregistro');
    }

    public function guardarSalida(Request $request){

        $rules = array(
            'fecha' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $r = new Salidas();
            $r->fecha = $request->fecha;
            $r->descripcion = $request->descripcion;
            $r->save();

            for ($i = 0; $i < count($request->cantidad); $i++) {

                // verificar cada material, para ver su cantidad
                $cantidadEntrada = EntradaDetalle::where('id_material', $request->datainfo[$i])->sum('cantidad');
                $cantidadSalida = SalidaDetalle::where('id_material', $request->datainfo[$i])->sum('cantidad');

                // cantidad que hay en bodega
                $cantidadBodega = $cantidadEntrada - $cantidadSalida;
                $resta = $cantidadBodega - $request->cantidad[$i];

                if($resta < 0){
                    return ['success' => 3, 'fila' => ($i + 1), 'cantidad' => $cantidadBodega];
                }


                $rDetalle = new SalidaDetalle();
                $rDetalle->id_salida = $r->id;
                $rDetalle->id_material = $request->datainfo[$i];
                $rDetalle->cantidad = $request->cantidad[$i];
                $rDetalle->save();
            }

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            DB::rollback();
            return ['success' => 2];
        }
    }

    //***************************************************************************

    public function indexEntradas(){
        return view('backend.admin.entradas.vistaentradas');
    }

    public function indexTablaEntradas(){

        $lista = Entradas::orderBy('fecha', 'ASC')->get();

        foreach ($lista as $dd){
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.entradas.tablaentradas', compact('lista'));
    }

    //*****************************************************************************

    public function indexEntradasDetalle($id){
        $dato = Entradas::where('id', $id)->first();
        $fecha = date("d-m-Y", strtotime($dato->fecha));

        return view('backend.admin.entradas.detalle.vistaentradadetalle', compact('id', 'fecha'));
    }

    public function indexEntradasDetalleTabla($id){
        $lista = DB::table('entradas_detalle AS ed')
            ->join('materiales AS m', 'ed.id_material', '=', 'm.id')
            ->select('ed.cantidad', 'm.nombre', 'm.codigo')
            ->where('ed.id_entrada', $id)
            ->orderBy('m.nombre', 'ASC')
            ->get();

        return view('backend.admin.entradas.detalle.tablaentradadetalle', compact('lista'));
    }


    //****************************************************************************

    public function indexSalidas(){
        return view('backend.admin.salidas.vistasalidas');
    }

    public function indexTablaSalidas(){

        $lista = Salidas::orderBy('fecha', 'ASC')->get();

        foreach ($lista as $dd){
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.salidas.tablasalidas', compact('lista'));
    }

    //*****************************************************************************

    public function indexSalidasDetalle($id){
        $dato = Salidas::where('id', $id)->first();
        $fecha = date("d-m-Y", strtotime($dato->fecha));

        return view('backend.admin.salidas.detalle.vistasalidadetalle', compact('id', 'fecha'));
    }

    public function indexSalidasDetalleTabla($id){
        $lista = DB::table('salidas_detalle AS ed')
            ->join('materiales AS m', 'ed.id_material', '=', 'm.id')
            ->select('ed.cantidad', 'm.nombre', 'm.codigo')
            ->where('ed.id_salida', $id)
            ->orderBy('m.nombre', 'ASC')
            ->get();

        return view('backend.admin.salidas.detalle.tablasalidadetalle', compact('lista'));
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

                array_push($resultsBloque,$ll);

                // obtener detalle
                $listaDetalle = DB::table('entradas_detalle AS ed')
                    ->join('materiales AS m', 'ed.id_material', '=', 'm.id')
                    ->select('m.nombre', 'ed.cantidad', 'm.id_medida')
                    ->where('ed.id_entrada', $ll->id)
                    ->orderBy('m.id', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd){
                    if($info = UnidadMedida::where('id', $dd->id_medida)->first()){
                        $dd->nombre = $dd->nombre . " - " . $info->medida;
                    }
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
                    <td colspan='1' width='17%'>Fecha</td>
                    <td colspan='3' width='30%'>Descripción</td>
                </tr>
                ";

                $tabla .= "<tr>
                    <td colspan='1' width='17%'>$dd->fecha</td>
                    <td colspan='3' width='30%'>$dd->descripcion</td>
                </tr>
                ";

                $tabla .= "</tbody></table>";

                $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
            <tbody>";

                $tabla .= "<tr>
                    <td width='20%'>Nombre</td>
                    <td width='10%'>Cantidad</td>
                </tr>";

                foreach ($dd->detalle as $gg) {
                    $tabla .= "<tr>
                    <td width='20%'>$gg->nombre</td>
                    <td width='10%'>$gg->cantidad</td>
                </tr>";
                }

                $tabla .= "</tbody></table>";
            }

            $stylesheet = file_get_contents('css/cssregistro.css');
            $mpdf->WriteHTML($stylesheet,1);

            $mpdf->setFooter("Página: " . '{PAGENO}' . "/" . '{nb}');
            $mpdf->WriteHTML($tabla,2);

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
                    ->select('m.nombre', 'ed.cantidad', 'm.id_medida')
                    ->where('ed.id_salida', $ll->id)
                    ->orderBy('m.id', 'ASC')
                    ->get();

                foreach ($listaDetalle as $dd){
                    if($info = UnidadMedida::where('id', $dd->id_medida)->first()){
                        $dd->nombre = $dd->nombre . " - " . $info->medida;
                    }
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
                    <td colspan='1' width='17%'>Fecha</td>
                    <td colspan='3' width='30%'>Descripción</td>
                </tr>
                ";

                $tabla .= "<tr>
                    <td colspan='1' width='17%'>$dd->fecha</td>
                    <td colspan='3' width='30%'>$dd->descripcion</td>
                </tr>
                ";

                $tabla .= "</tbody></table>";

                $tabla .= "<table width='100%' id='tablaFor' style='margin-top: 20px'>
            <tbody>";

                $tabla .= "<tr>
                    <td width='20%'>Nombre</td>
                    <td width='10%'>Cantidad</td>
                </tr>";

                foreach ($dd->detalle as $gg) {
                    $tabla .= "<tr>
                    <td width='20%'>$gg->nombre</td>
                    <td width='10%'>$gg->cantidad</td>
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

}
