<?php

namespace App\Http\Controllers\Backend\Aceites;

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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AceitesController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function indexCatalogoAceiteLubicantes(){
        $lUnidad = UnidadMedidaAceites::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.repuestos.materiales.aceitelubricantes.vistacatalogoaceites', compact('lUnidad'));
    }

    public function tablaCatalogoAceiteLubicantes(){
        $lista = MaterialesAceites::orderBy('nombre', 'ASC')->get();

        foreach ($lista as $item) {
            $medida = '';
            if($dataUnidad = UnidadMedidaAceites::where('id', $item->id_medida)->first()){
                $medida = $dataUnidad->nombre;
            }
            $item->medida = $medida;
        }

        return view('backend.admin.repuestos.materiales.aceitelubricantes.tablacatalogoaceites', compact('lista'));
    }

    public function nuevoCatalogoAceiteLubicantes(Request $request){

        $regla = array(
            'nombre' => 'required',
            'unidad' => 'required',
            'tipo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(MaterialesAceites::where('nombre', $request->nombre)
            ->where('id_medida', $request->unidad)
            ->where('tipo', $request->tipo)
            ->first()){
            return ['success' => 1];
        }

        $dato = new MaterialesAceites();
        $dato->id_medida = $request->unidad;
        $dato->nombre = $request->nombre;
        $dato->tipo = $request->tipo;

        if($dato->save()){
            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }

    public function informacionCatalogoAceiteLubicantes(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = MaterialesAceites::where('id', $request->id)->first()){

            $arrayUnidad = UnidadMedidaAceites::orderBy('nombre', 'ASC')->get();

            return ['success' => 1, 'material' => $lista, 'unidad' => $arrayUnidad];
        }else{
            return ['success' => 2];
        }
    }

    public function editarCatalogoAceiteLubicantes(Request $request){

        $regla = array(
            'nombre' => 'required',
            'unidad' => 'required',
            'tipo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(MaterialesAceites::where('id', '!=', $request->id)
            ->where('nombre', $request->nombre)
            ->where('id_medida', $request->unidad)
            ->where('tipo', $request->tipo)
            ->first()){
            return ['success' => 1];
        }

        MaterialesAceites::where('id', $request->id)->update([
            'id_medida' => $request->unidad,
            'nombre' => $request->nombre,
            'tipo' => $request->tipo
        ]);

        return ['success' => 2];
    }


    //*****************************************************************************

    public function indexRegistroEntrada(){

        $empresa = EmpresaLicitacion::orderBy('nombre')->get();
        $ubicacion = UbicacionBodega::orderBy('nombre')->get();

        return view('backend.admin.repuestos.registros.aceites.entrada.entradaregistroaceites', compact('empresa', 'ubicacion'));
    }

    public function buscadorAceiteLubricante(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $data = MaterialesAceites::where('nombre', 'LIKE', "%{$query}%")->get();

            foreach ($data as $dd){
                if($info = UnidadMedidaAceites::where('id', $dd->id_medida)->first()){
                    $dd->medida = "- " . $info->nombre;
                }else{
                    $dd->medida = "";
                }

                if($dd->tipo == 1){
                    $dd->tipo = "- ACEITES";
                }else{
                    $dd->tipo = "- LUBRICANTES";
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
                 <li onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . '  ' .$row->medida . ' ' .$row->tipo .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' ' .$row->medida . ' ' .$row->tipo .'</a></li>
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


    public function guardarEntradaAceiteLubricante(Request $request){

        $rules = array(
            'fecha' => 'required',
            'empresa' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $r = new EntradaAceites();
            $r->fecha = $request->fecha;
            $r->descripcion = $request->descripcion;
            $r->id_empresa = $request->empresa;
            $r->save();

            for ($i = 0; $i < count($request->cantidad); $i++) {

                $rDetalle = new EntradaAceitesDetalle();
                $rDetalle->id_entrada_aceite = $r->id;
                $rDetalle->id_material_aceite = $request->datainfo[$i];
                $rDetalle->id_ubicacion = $request->idubicacion[$i];
                $rDetalle->cantidad = $request->cantidad[$i];
                $rDetalle->precio = $request->precio[$i];
                $rDetalle->save();
            }

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('ee ' . $e);
            DB::rollback();
            return ['success' => 2];
        }
    }


    public function indexRegistroSalida(){
        return view('backend.admin.repuestos.registros.aceites.salida.salidaregistroaceites');
    }

    // muestra lo disponible de cantidades para descontar
    public function bloqueCantidades($id){

        // obtener todas las entradas y obtener cada fila de cantidad
        $lista = EntradaAceitesDetalle::where('id_material_aceite', $id)
            ->where('cantidad', '>', 0)
            ->get();

        $dataArray = array();

        $hayCantidad = false;

        foreach ($lista as $dd){

            $infoEntrada = EntradaAceites::where('id', $dd->id_entrada_aceite)->first();

            // buscar la entrada_detalle de cada salida. obtener la suma de salidas
            $salidaDetalle = SalidaAceitesDetalle::where('id_entrada_aceite_deta', $dd->id)
                ->where('id_material_aceite', $id)
                ->sum('cantidad');

            $ubicacion = "";
            if($infoUbi = UbicacionBodega::where('id', $dd->id_ubicacion)->first()){
                $ubicacion = $infoUbi->nombre;
            }

            // total de la cantidad actual
            $cantidadtotal = $dd->cantidad - $salidaDetalle;

            if($cantidadtotal > 0){
                $dataArray[] = [
                    'id' => $dd->id,
                    'fecha' => date("d-m-Y", strtotime($infoEntrada->fecha)),
                    'precio' => number_format((float)$dd->precio, 2, '.', ','),
                    'cantidadtotal' => $cantidadtotal,
                    'ubicacion' => $ubicacion,
                ];
            }
        }

        if(sizeof($dataArray) > 0){
            $hayCantidad = true;
        }

        return view('backend.admin.repuestos.registros.aceites.modal.modalbloquesalidaaceite', compact('dataArray', 'hayCantidad'));
    }

    public function guardarSalidaAceiteLubricante(Request $request){

        $rules = array(
            'fecha' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $r = new SalidaAceites();
            $r->fecha = $request->fecha;
            $r->descripcion = $request->descripcion;
            $r->save();

            for ($i = 0; $i < count($request->salida); $i++) {

                // sacar id del material
                $infoEntradaDetalle = EntradaAceitesDetalle::where('id', $request->identrada[$i])->first();

                // iterar todas las entradas detalle

                $lista = EntradaAceitesDetalle::where('id', $request->identrada[$i])->get();
                $total = 0;

                foreach ($lista as $data){

                    // buscar la entrada_detalle de cada salida. obtener la suma de salidas
                    $salidaDetalle = SalidaAceitesDetalle::where('id_entrada_aceite_deta', $data->id)
                        ->where('id_material_aceite', $infoEntradaDetalle->id_material_aceite)
                        ->sum('cantidad');

                    // total de la cantidad actual
                    $total = $data->cantidad - $salidaDetalle;
                }

                if($total < $request->salida[$i]){
                    return ['success' => 1, 'fila' => ($i), 'cantidad' => $total];
                }

                $rDetalle = new SalidaAceitesDetalle();
                $rDetalle->id_salida_aceites = $r->id;
                $rDetalle->id_material_aceite = $infoEntradaDetalle->id_material_aceite;
                $rDetalle->id_entrada_aceite_deta = $request->identrada[$i];
                $rDetalle->cantidad = $request->salida[$i];
                $rDetalle->uso = 0;
                $rDetalle->fecha_finalizo = null;
                $rDetalle->descripcion = $request->descripciondeta[$i];
                $rDetalle->save();
            }

            DB::commit();
            return ['success' => 2];

        }catch(\Throwable $e){
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function indexEnUsoAceites(){
        return view('backend.admin.repuestos.registros.aceites.uso.vistaaceiteuso');
    }

    public function tablaEnUsoAceites(){

        $lista = SalidaAceitesDetalle::where('uso', 0)->get();

        foreach ($lista as $dd){

            $infoSalida = SalidaAceites::where('id', $dd->id_salida_aceites)->first();
            $infoMaterial = MaterialesAceites::where('id', $dd->id_material_aceite)->first();
            $infoMedida = UnidadMedidaAceites::where('id', $infoMaterial->id_medida)->first();

            $dd->fecha = date("d-m-Y", strtotime($infoSalida->fecha));
            $dd->viscosidad = $infoMaterial->nombre;
            $dd->medida = $infoMedida->nombre;
            $dd->tipo = $infoMaterial->tipo;

            $infoEntradaDetalle = EntradaAceitesDetalle::where('id', $dd->id_entrada_aceite_deta)->first();
            $infoEntradaAceite = EntradaAceites::where('id', $infoEntradaDetalle->id_entrada_aceite)->first();
            $infoEmpresa = EmpresaLicitacion::where('id', $infoEntradaAceite->id_empresa)->first();

            $dd->empresa = $infoEmpresa->nombre;
        }

        return view('backend.admin.repuestos.registros.aceites.uso.tablaaceiteuso', compact('lista'));
    }


    public function finalizarUsoAceitesLubricantes(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        SalidaAceitesDetalle::where('id', $request->id)->update([
            'uso' => 1, // finalizar
            'fecha_finalizo' => Carbon::now('America/El_Salvador'),
        ]);

        return ['success' => 1];
    }


    public function detalleAceiteLubricanteUso($id){
        // ID salida_aceites_detalle

        $infoSalida = SalidaAceitesDetalle::where('id', $id)->first();
        $infoMaterial = MaterialesAceites::where('id', $infoSalida->id_material_aceite)->first();

        $infoSalidaAceite = SalidaAceites::where('id', $infoSalida->id_salida_aceites)->first();

        $viscosidad = $infoMaterial->nombre;

        $equipos = Equipos::orderBy('nombre')->get();
        $medidas = UnidadMedidaAceites::orderBy('nombre')->get();

        $fechasalida = date("d-m-Y", strtotime($infoSalidaAceite->fecha));

        return view('backend.admin.repuestos.registros.aceites.uso.detalle.vistadetalleaceiteuso', compact('id', 'viscosidad', 'equipos',
            'medidas', 'fechasalida'));
    }

    public function tablaAceiteLubricanteUso($id){
        // ID salida_aceites_detalle

        $lista = RegistroAceiteDetalle::where('id_salida_acei_deta', $id)->orderBy('fecha', 'ASC')->get();

        foreach ($lista as $dd){

            $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
            $infoMedida = UnidadMedidaAceites::where('id', $dd->id_medida)->first();

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            if($dd->hora != null){
                $dd->hora = date("g:i A", strtotime($dd->hora));
            }

            $dd->equipo = $infoEquipo->nombre;
            $dd->medida = $infoMedida->nombre;
        }

        return view('backend.admin.repuestos.registros.aceites.uso.detalle.tabladetalleaceiteuso', compact('lista'));
    }


    public function guardarDetalleSalida(Request $request){

        $rules = array(
            'id' => 'required', // ID salida_aceites_detalle
            'fecha' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $r = new RegistroAceiteDetalle();
            $r->id_salida_acei_deta = $request->id;
            $r->id_equipo = $request->equipo;
            $r->id_medida = $request->medida;
            $r->fecha = $request->fecha;
            $r->hora = $request->hora;
            $r->cantidad_salida = $request->cantidad;
            $r->descripcion = $request->descripcion;
            $r->save();

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            //Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 99];
        }
    }

    public function indexAceitesFinalizados(){
        return view('backend.admin.repuestos.registros.aceites.finalizados.vistafinalizadosaceite');
    }

    public function tablaAceitesFinalizados(){

        $lista = SalidaAceitesDetalle::where('uso', 1)->get();

        foreach ($lista as $dd){

            $infoSalida = SalidaAceites::where('id', $dd->id_salida_aceites)->first();
            $infoMaterial = MaterialesAceites::where('id', $dd->id_material_aceite)->first();
            $infoMedida = UnidadMedidaAceites::where('id', $infoMaterial->id_medida)->first();

            $dd->fecha = date("d-m-Y", strtotime($infoSalida->fecha));
            $dd->viscosidad = $infoMaterial->nombre;
            $dd->medida = $infoMedida->nombre;
            $dd->tipo = $infoMaterial->tipo;

            $infoEntradaDetalle = EntradaAceitesDetalle::where('id', $dd->id_entrada_aceite_deta)->first();
            $infoEntradaAceite = EntradaAceites::where('id', $infoEntradaDetalle->id_entrada_aceite)->first();
            $infoEmpresa = EmpresaLicitacion::where('id', $infoEntradaAceite->id_empresa)->first();

            $dd->empresa = $infoEmpresa->nombre;

            if($dd->fecha_finalizo != null){
                $dd->fecha_finalizo = date("d-m-Y", strtotime($dd->fecha_finalizo));
            }

        }

        return view('backend.admin.repuestos.registros.aceites.finalizados.tablafinalizadosaceite', compact('lista'));
    }


    public function reutilizarUsoAceitesLubricantes(Request $request){

        $rules = array(
            'id' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        SalidaAceitesDetalle::where('id', $request->id)->update([
            'uso' => 0, // reutilizar
            'fecha_finalizo' => null,
        ]);

        return ['success' => 1];
    }

    public function indexFinalizadosAceitesDetalle($id){

        $infoSalida = SalidaAceitesDetalle::where('id', $id)->first();
        $infoMaterial = MaterialesAceites::where('id', $infoSalida->id_material_aceite)->first();
        $infoSalidaAceite = SalidaAceites::where('id', $infoSalida->id_salida_aceites)->first();

        $viscosidad = $infoMaterial->nombre;
        $fechasalida = date("d-m-Y", strtotime($infoSalidaAceite->fecha));

        return view('backend.admin.repuestos.registros.aceites.finalizados.detalle.vistadetallefinalizado', compact('viscosidad', 'fechasalida', 'id'));
    }

    public function tablaFinalizadosAceitesDetalle($id){

        // ID salida_aceites_detalle

        $lista = RegistroAceiteDetalle::where('id_salida_acei_deta', $id)->orderBy('fecha', 'ASC')->get();

        foreach ($lista as $dd){

            $infoEquipo = Equipos::where('id', $dd->id_equipo)->first();
            $infoMedida = UnidadMedidaAceites::where('id', $dd->id_medida)->first();

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));

            if($dd->hora != null){
                $dd->hora = date("g:i A", strtotime($dd->hora));
            }

            $dd->equipo = $infoEquipo->nombre;
            $dd->medida = $infoMedida->nombre;
        }

        return view('backend.admin.repuestos.registros.aceites.finalizados.detalle.tabladetallefinalizado', compact('lista'));
    }


    public function eliminarDetalleAceites(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(RegistroAceiteDetalle::where('id', $request->id)->first()){

            RegistroAceiteDetalle::where('id', $request->id)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 3];
        }
    }


}
