<?php

namespace App\Http\Controllers;

use App\Models\EntradaDetalle;
use App\Models\EntradaLLantas;
use App\Models\EntradaLLantasDeta;
use App\Models\Entradas;
use App\Models\Equipos;
use App\Models\Llantas;
use App\Models\Materiales;
use App\Models\MedidaRin;
use App\Models\Proveedor;
use App\Models\SalidaDetalle;
use App\Models\SalidaLLantas;
use App\Models\SalidaLLantasDeta;
use App\Models\Salidas;
use App\Models\UbicacionBodega;
use App\Models\UbicacionRepuesto;
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
        return view('backend.admin.repuestos.medida.vistaunidadmedida');
    }

    public function tablaUnidadMedida(){
        $lista = UnidadMedida::orderBy('medida', 'ASC')->get();
        return view('backend.admin.repuestos.medida.tablaunidadmedida', compact('lista'));
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


    public function indexRinllanta(){
        return view('backend.admin.llantas.medidarin.vistamedidarin');
    }

    public function tablaRinllanta(){
        $lista = MedidaRin::orderBy('medida', 'ASC')->get();
        return view('backend.admin.llantas.medidarin.tablamedidarin', compact('lista'));
    }

    public function nuevaRinllanta(Request $request){
        $regla = array(
            'medida' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new MedidaRin();
        $dato->medida = $request->medida;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionRinllanta(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = MedidaRin::where('id', $request->id)->first()){

            return ['success' => 1, 'medida' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarRinllanta(Request $request){

        $regla = array(
            'id' => 'required',
            'medida' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(MedidaRin::where('id', $request->id)->first()){

            MedidaRin::where('id', $request->id)->update([
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
        return view('backend.admin.repuestos.materiales.vistacatalogomateriales', compact('lUnidad'));
    }

    public function tablaMateriales(){
        $lista = Materiales::orderBy('nombre', 'ASC')->get();

        foreach ($lista as $item) {
            $medida = '';
            if($dataUnidad = UnidadMedida::where('id', $item->id_medida)->first()){
                $medida = $dataUnidad->medida;
            }
            $item->medida = $medida;

            // obtener todas las entradas detalle de este material

            $entradaDetalle = EntradaDetalle::where('id_material', $item->id)->get();

            $valor = 0;
            $dinero = 0;
            foreach ($entradaDetalle as $data){

                // buscar la entrada_detalle de cada salida. obtener la suma de salidas
                $salidaDetalle = SalidaDetalle::where('id_entrada_detalle', $data->id)
                    ->where('id_material', $item->id)
                    ->sum('cantidad');

                // total: es la cantidad actual
                $total = $data->cantidad - $salidaDetalle;

                // valor: es la suma de cantidad actual
                $valor = $valor + $total;

                // dinero: es la suma del precio del repuesto
                $dinero = $dinero + ($data->precio * $total);
            }

            $item->total = $valor;
            $item->dinero = number_format((float)$dinero, 2, '.', ',');
        }

        return view('backend.admin.repuestos.materiales.tablacatalogomateriales', compact('lista'));
    }

    public function nuevoMaterial(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Materiales::where('nombre', $request->nombre)
            ->where('id_medida', $request->unidad)
            ->where('codigo', $request->codigo)
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
            ->where('codigo', $request->codigo)
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

        $ubicaciones = UbicacionRepuesto::orderBy('nombre')->get();

        return view('backend.admin.repuestos.registros.entradaregistro', compact('equipos', 'ubicaciones'));
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

                if($dd->codigo != null){
                    $dd->code = "- " . $dd->codigo;
                }else{
                    $dd->code = "";
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
                 <li onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . '  ' .$row->medida . ' ' .$row->code .'</a></li>
                ';
                    }
                }

                else{
                    if(!empty($row)){
                        $tiene = false;
                        $output .= '
                 <li onclick="modificarValor(this)" id="'.$row->id.'"><a href="#" style="margin-left: 3px">'.$row->nombre . ' ' .$row->medida . ' ' .$row->code .'</a></li>
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
                    $r->documento = $nomDocumento;
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
                        $rDetalle->id_ubicacion = $request->idubicacion[$i];
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
                    $rDetalle->id_ubicacion = $request->idubicacion[$i];
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
        $equipos = Equipos::orderBy('nombre')->get();
        return view('backend.admin.repuestos.registros.salidaregistro', compact('equipos'));
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
            $r->talonario = $request->talonario;
            $r->save();

            for ($i = 0; $i < count($request->salida); $i++) {

                // sacar id del material
                $infoEntradaDetalle = EntradaDetalle::where('id', $request->identrada[$i])->first();

                // iterar todas las entradas detalle

                $lista = EntradaDetalle::where('id', $request->identrada[$i])->get();
                $total = 0;

                foreach ($lista as $data){

                    // buscar la entrada_detalle de cada salida. obtener la suma de salidas
                    $salidaDetalle = SalidaDetalle::where('id_entrada_detalle', $data->id)
                        ->where('id_material', $infoEntradaDetalle->id_material)
                        ->sum('cantidad');

                    // total de la cantidad actual
                    $total = $data->cantidad - $salidaDetalle;
                }

                if($total < $request->salida[$i]){
                    return ['success' => 3, 'fila' => ($i), 'cantidad' => $total];
                }

                $rDetalle = new SalidaDetalle();
                $rDetalle->id_salida = $r->id;
                $rDetalle->id_material = $infoEntradaDetalle->id_material;
                $rDetalle->id_equipo = $request->equipo[$i];
                $rDetalle->cantidad = $request->salida[$i];
                $rDetalle->id_entrada_detalle = $request->identrada[$i];
                $rDetalle->save();
            }

            DB::commit();
            return ['success' => 1];

        }catch(\Throwable $e){
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 2];
        }
    }

    //***************************************************************************

    public function indexEntradas(){
        return view('backend.admin.repuestos.entradas.vistaentradas');
    }

    public function indexTablaEntradas(){

        $lista = Entradas::orderBy('fecha', 'ASC')->get();

        // verificar cada entrada_detalle para ver si ya tiene salidas

        foreach ($lista as $dd){
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
            $btnBloqueo = true;
            $detalle = EntradaDetalle::where('id_entrada', $dd->id)->get();
            foreach ($detalle as $ll){
                if(SalidaDetalle::where('id_entrada_detalle', $ll->id)->first()){
                    $btnBloqueo = false;
                    break;
                }
            }

            $dd->btnbloqueo = $btnBloqueo;
        }

        return view('backend.admin.repuestos.entradas.tablaentradas', compact('lista'));
    }

    //*****************************************************************************

    public function indexEntradasDetalle($id){
        $dato = Entradas::where('id', $id)->first();
        $fecha = date("d-m-Y", strtotime($dato->fecha));

        return view('backend.admin.repuestos.entradas.detalle.vistaentradadetalle', compact('id', 'fecha'));
    }

    public function indexEntradasDetalleTabla($id){
        $lista = DB::table('entradas_detalle AS ed')
            ->join('materiales AS m', 'ed.id_material', '=', 'm.id')
            ->select('ed.cantidad', 'm.nombre', 'ed.id', 'm.codigo', 'ed.id_ubicacion', 'ed.id_equipo', 'ed.precio', 'm.id as idmaterial')
            ->where('ed.id_entrada', $id)
            ->orderBy('m.nombre', 'ASC')
            ->get();

        foreach ($lista as $ll){

            $ll->precio = number_format((float)$ll->precio, 2, '.', ',');

            $infoEquipo = Equipos::where('id', $ll->id_equipo)->first();
            $ll->equipo = $infoEquipo->nombre;

            $infoMaterial = Materiales::where('id', $ll->idmaterial)->first();
            $medida = '';
            if($infoUnidad = UnidadMedida::where('id', $infoMaterial->id_medida)->first()){
                $medida = $infoUnidad->medida;
            }
            $ll->unidad = $medida;

            $ubicacion = "";
            if($infoUbi = UbicacionRepuesto::where('id', $ll->id_ubicacion)->first()){
                $ubicacion = $infoUbi->nombre;
            }

            $ll->ubicacion = $ubicacion;
        }

        return view('backend.admin.repuestos.entradas.detalle.tablaentradadetalle', compact('lista'));
    }


    public function informacionEntradaHistorialDetalle(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = EntradaDetalle::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarEntradaHistorialDetalle(Request $request){

        $regla = array(
            'id' => 'required',
            'precio' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        EntradaDetalle::where('id', $request->id)->update([
            'precio' => $request->precio
        ]);

        return ['success' => 1];
    }




    //****************************************************************************

    public function indexSalidas(){
        return view('backend.admin.repuestos.historial.salidas.vistasalidas');
    }

    public function indexTablaSalidas(){

        $lista = Salidas::orderBy('fecha', 'ASC')->get();

        foreach ($lista as $dd){
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.repuestos.historial.salidas.tablasalidas', compact('lista'));
    }

    public function indexSalidasLlantas(){
        return view('backend.admin.llantas.historial.salida.vistahistosalidallanta');
    }

    public function indexTablaSalidasLlantas(){

        $lista = SalidaLLantas::orderBy('fecha', 'ASC')->get();

        foreach ($lista as $dd){
            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
        }

        return view('backend.admin.llantas.historial.salida.tablahistosalidallanta', compact('lista'));
    }

    //*****************************************************************************

    public function indexSalidasDetalle($id){
        $dato = Salidas::where('id', $id)->first();
        $fecha = date("d-m-Y", strtotime($dato->fecha));

        return view('backend.admin.repuestos.historial.salidas.detalle.vistasalidadetalle', compact('id', 'fecha'));
    }

    public function indexSalidasDetalleTabla($id){
        $lista = DB::table('salidas_detalle AS ed')
            ->join('materiales AS m', 'ed.id_material', '=', 'm.id')
            ->select('ed.cantidad', 'm.nombre', 'm.codigo', 'ed.id_equipo', 'm.id as idmaterial')
            ->where('ed.id_salida', $id)
            ->orderBy('m.nombre', 'ASC')
            ->get();

        foreach ($lista as $ll){

            $infoEquipo = Equipos::where('id', $ll->id_equipo)->first();
            $ll->equipo = $infoEquipo->nombre;

            $infoMaterial = Materiales::where('id', $ll->idmaterial)->first();
            $medida = '';
            if($infoUnidad = UnidadMedida::where('id', $infoMaterial->id_medida)->first()){
                $medida = $infoUnidad->medida;
            }
            $ll->unidad = $medida;
        }

        return view('backend.admin.repuestos.historial.salidas.detalle.tablasalidadetalle', compact('lista'));
    }

    public function indexSalidasDetalleLlanta($id){
        $dato = SalidaLLantas::where('id', $id)->first();
        $fecha = date("d-m-Y", strtotime($dato->fecha));

        return view('backend.admin.llantas.historial.salida.detalle.vistadetallantahistorialsalida', compact('id', 'fecha'));
    }

    public function indexSalidasDetalleTablaLlanta($id){
        $lista = DB::table('salida_llanta_deta AS ed')
            ->join('llantas AS m', 'ed.id_llanta', '=', 'm.id')
            ->join('marca_llanta AS marca', 'm.id_marca', '=', 'marca.id')
            ->select('ed.cantidad', 'marca.nombre', 'ed.id_equipo', 'ed.id_l_entrada_detalle', 'm.id as idllanta')
            ->where('ed.id_salida_llanta', $id)
            ->orderBy('marca.nombre', 'ASC')
            ->get();

        foreach ($lista as $ll){

            $infoEquipo = Equipos::where('id', $ll->id_equipo)->first();
            $ll->equipo = $infoEquipo->nombre;

            $infoMaterial = Llantas::where('id', $ll->idllanta)->first();

            $infoUnidad = MedidaRin::where('id', $infoMaterial->id_medida)->first();
            $ll->unidad = $infoUnidad->medida;

            $info = EntradaLLantasDeta::where('id', $ll->id_l_entrada_detalle)->first();
            $ll->precio = number_format((float)$info->precio, 2, '.', ',');
        }

        return view('backend.admin.llantas.historial.salida.detalle.tabladetallantahistorialsalida', compact('lista'));
    }

    public function documentoEntrada($id){

        $url = Entradas::where('id', $id)->pluck('documento')->first();
        $pathToFile = "storage/archivos/".$url;
        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);
        $nombre = "Documento." . $extension;
        return response()->download($pathToFile, $nombre);
    }

    public function documentoEntradaLlanta($id){

        $url = EntradaLLantas::where('id', $id)->pluck('documento')->first();
        $pathToFile = "storage/archivos/".$url;
        $extension = pathinfo(($pathToFile), PATHINFO_EXTENSION);
        $nombre = "Documento." . $extension;
        return response()->download($pathToFile, $nombre);
    }

    public function borrarDocumento(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Entradas::where('id', $request->id)->first()){

            if(Storage::disk('archivos')->exists($lista->documento)){
                Storage::disk('archivos')->delete($lista->documento);
            }

            Entradas::where('id', $request->id)->update([
                'documento' => null
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function borrarRegistro(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Entradas::where('id', $request->id)->first()){

            // verificar que no haya salidas de esta entrada
            $detalle = EntradaDetalle::where('id_entrada', $lista->id)->get();
            foreach ($detalle as $ll){
                if(SalidaDetalle::where('id_entrada_detalle', $ll->id)->first()){
                   return ['success' => 2];
                }
            }

            if(Storage::disk('archivos')->exists($lista->documento)){
                Storage::disk('archivos')->delete($lista->documento);
            }

            EntradaDetalle::where('id_entrada', $request->id)->delete();
            Entradas::where('id', $request->id)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 3];
        }
    }

    public function guardarDocumento(Request $request){

        $rules = array(
            'id' => 'required',
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

                    $info = Entradas::where('id', $request->id)->first();

                    if(Storage::disk('archivos')->exists($info->documento)){
                        Storage::disk('archivos')->delete($info->documento);
                    }

                    Entradas::where('id', $request->id)->update([
                        'documento' => $nomDocumento
                    ]);

                    DB::commit();
                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                }
            }
            else{
                return ['success' => 2];
            }
        }catch(\Throwable $e){

            DB::rollback();
            return ['success' => 2];
        }
    }

    public function borrarRegistroSalida(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Salidas::where('id', $request->id)->first()){

            SalidaDetalle::where('id_salida', $request->id)->delete();
            Salidas::where('id', $request->id)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function borrarRegistroSalidaLlanta(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(SalidaLLantas::where('id', $request->id)->first()){

            SalidaLLantasDeta::where('id_salida_llanta', $request->id)->delete();
            SalidaLLantas::where('id', $request->id)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function indexHistorialEntradasLlanta(){
        return view('backend.admin.llantas.historial.entrada.vistahistoentradallanta');
    }

    public function indexTablaHistorialEntradasLlantas(){
        $lista = EntradaLLantas::orderBy('fecha', 'ASC')->get();

        // verificar cada entrada_detalle para ver si ya tiene salidas

        foreach ($lista as $dd){

            $infoProve = Proveedor::where('id', $dd->id_proveedor)->first();
            $dd->proveedor = $infoProve->nombre;

            $dd->fecha = date("d-m-Y", strtotime($dd->fecha));
            $btnBloqueo = true;
            $detalle = EntradaLLantasDeta::where('id_entrada_llanta', $dd->id)->get();
            foreach ($detalle as $ll){
                if(SalidaLLantasDeta::where('id_l_entrada_detalle', $ll->id)->first()){
                    $btnBloqueo = false;
                    break;
                }
            }

            $dd->btnbloqueo = $btnBloqueo;
        }


        return view('backend.admin.llantas.historial.entrada.tablahistoentradallanta', compact('lista'));
    }

    public function borrarDocumentoLlanta(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = EntradaLLantas::where('id', $request->id)->first()){

            if(Storage::disk('archivos')->exists($lista->documento)){
                Storage::disk('archivos')->delete($lista->documento);
            }

            EntradaLLantas::where('id', $request->id)->update([
                'documento' => null
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function borrarRegistroLlanta(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = EntradaLLantas::where('id', $request->id)->first()){

            // verificar que no haya salidas de esta entrada
            $detalle = EntradaLLantasDeta::where('id_entrada_llanta', $lista->id)->get();
            foreach ($detalle as $ll){
                if(SalidaLLantasDeta::where('id_l_entrada_detalle', $ll->id)->first()){
                    return ['success' => 2];
                }
            }

            if(Storage::disk('archivos')->exists($lista->documento)){
                Storage::disk('archivos')->delete($lista->documento);
            }

            EntradaLLantasDeta::where('id_entrada_llanta', $request->id)->delete();
            EntradaLLantas::where('id', $request->id)->delete();

            return ['success' => 1];
        }else{
            return ['success' => 3];
        }
    }

    public function guardarDocumentoLlanta(Request $request){

        $rules = array(
            'id' => 'required',
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

                    $info = EntradaLLantas::where('id', $request->id)->first();

                    if(Storage::disk('archivos')->exists($info->documento)){
                        Storage::disk('archivos')->delete($info->documento);
                    }

                    EntradaLLantas::where('id', $request->id)->update([
                        'documento' => $nomDocumento
                    ]);

                    DB::commit();
                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                }
            }
            else{
                return ['success' => 2];
            }
        }catch(\Throwable $e){

            DB::rollback();
            return ['success' => 2];
        }
    }


    public function indexEntradasDetalleLlanta($id){
        $dato = EntradaLLantas::where('id', $id)->first();
        $fecha = date("d-m-Y", strtotime($dato->fecha));

        return view('backend.admin.llantas.historial.entrada.detalle.vistadetallantahistorialentrada', compact('id', 'fecha'));
    }


    public function indexEntradasDetalleTablaLlanta($id){

        $lista = DB::table('entrada_llanta_deta AS ed')
            ->join('llantas AS m', 'ed.id_llanta', '=', 'm.id')
            ->join('marca_llanta AS marca', 'm.id_marca', '=', 'marca.id')
            ->select('ed.cantidad', 'ed.id', 'marca.nombre', 'ed.id_ubicacion', 'm.id_medida', 'ed.precio', 'm.id as idllanta')
            ->where('ed.id_entrada_llanta', $id)
            ->orderBy('marca.nombre', 'ASC')
            ->get();

        foreach ($lista as $ll){

            $ll->precio = number_format((float)$ll->precio, 2, '.', ',');

            $infoBodega = UbicacionBodega::where('id', $ll->id_ubicacion)->first();
            $ll->ubicacion = $infoBodega->nombre;

            $infoMaterial = Llantas::where('id', $ll->idllanta)->first();

            $infoUnidad = MedidaRin::where('id', $infoMaterial->id_medida)->first();
            $ll->unidad = $infoUnidad->medida;
        }

        return view('backend.admin.llantas.historial.entrada.detalle.tabladetallantahistorialentrada', compact('lista'));
    }


    //***************** REGISTRO DE UBICACIONES DE REPUESTOS  ************************


    public function indexUbicacionRepuestos(){
        return view('backend.admin.repuestos.ubicacion.vistaubicacionrepuesto');
    }

    public function tablaUbicacionRepuestos(){
        $lista = UbicacionRepuesto::orderBy('nombre', 'ASC')->get();

        return view('backend.admin.repuestos.ubicacion.tablaubicacionrepuesto', compact('lista'));
    }

    public function nuevaUbicacionRepuestos(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(UbicacionRepuesto::where('nombre', $request->nombre)->first()){
            return ['success' => 1];
        }

        $dato = new UbicacionRepuesto();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }

    public function informacionUbicacionRepuestos(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = UbicacionRepuesto::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarUbicacionRepuestos(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(UbicacionRepuesto::where('id', $request->id)->first()){

            if(UbicacionRepuesto::where('id', '!=', $request->id)
                ->where('nombre', $request->nombre)->first()){
                return ['success' => 1];
            }

                UbicacionRepuesto::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 2];
        }else{
            return ['success' => 99];
        }
    }


}
