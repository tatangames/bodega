<?php

namespace App\Http\Controllers\Backend\Repuestos;

use App\Http\Controllers\Controller;
use App\Models\EntradaDetalle;
use App\Models\EntradaLLantas;
use App\Models\EntradaLLantasDeta;
use App\Models\Entradas;
use App\Models\Equipos;
use App\Models\Llantas;
use App\Models\Materiales;
use App\Models\Proveedor;
use App\Models\SalidaDetalle;
use App\Models\SalidaLLantas;
use App\Models\SalidaLLantasDeta;
use App\Models\Salidas;
use App\Models\UbicacionBodega;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

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

        $dataArray = array();

        $hayCantidad = false;

        foreach ($lista as $dd){

            $infoEntrada = Entradas::where('id', $dd->id_entrada)->first();
            $dd->fecha =
            $dd->factura = $infoEntrada->factura;

            // buscar la entrada_detalle de cada salida. obtener la suma de salidas
            $salidaDetalle = SalidaDetalle::where('id_entrada_detalle', $dd->id)
                ->where('id_material', $id)
                ->sum('cantidad');

            // total de la cantidad actual
            $cantidadtotal = $dd->cantidad - $salidaDetalle;

            if($cantidadtotal > 0){
                $dataArray[] = [
                    'id' => $dd->id,
                    'fecha' => date("d-m-Y", strtotime($infoEntrada->fecha)),
                    'precio' => number_format((float)$dd->precio, 2, '.', ','),
                    'cantidadtotal' => $cantidadtotal,
                ];
            }
        }

        if(sizeof($dataArray) > 0){
            $hayCantidad = true;
        }

        return view('backend.admin.registros.modal.modalentrada', compact('dataArray', 'hayCantidad'));
    }

    // id material
    public function vistaDetalleMaterial($id){

        $info = Materiales::where('id', $id)->first();
        $repuesto = $info->nombre;
        $medida = '';
        if($infoMedida = UnidadMedida::where('id', $info->id_medida)->first()){
            $medida = $infoMedida->medida;
        }


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

            $data->precio = number_format((float)$data->precio, 2, '.', ',');

            $totalprecio = $total * $data->precio;
            $data->totalprecio = number_format((float)$totalprecio, 2, '.', ',');
            $valor = $valor + $total;
            $data->total = $valor;
            $valor = 0;
        }

        return view('backend.admin.materiales.detalle.tabladetallematerial', compact('lista'));
    }

    public function informacionEntradaHistorial(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Entradas::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarEntradaHistorial(Request $request){

        $regla = array(
            'id' => 'required',
            'fecha' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        Entradas::where('id', $request->id)->update([
            'fecha' => $request->fecha,
            'descripcion' => $request->descripcion,
            'factura' => $request->factura,
            'inventario' => $request->inventario
        ]);

        return ['success' => 1];
    }


    public function informacionEntradaHistorialLlanta(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = EntradaLLantas::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarEntradaHistorialLlanta(Request $request){

        $regla = array(
            'id' => 'required',
            'fecha' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        EntradaLLantas::where('id', $request->id)->update([
            'fecha' => $request->fecha,
            'descripcion' => $request->descripcion,
            'factura' => $request->factura,
            'inventario' => $request->inventario
        ]);

        return ['success' => 1];
    }

    //*********************************************

    public function informacionSalidaHistorial(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Salidas::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarSalidaHistorial(Request $request){

        $regla = array(
            'id' => 'required',
            'fecha' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        Salidas::where('id', $request->id)->update([
            'fecha' => $request->fecha,
            'descripcion' => $request->descripcion,
            'talonario' => $request->talonario,
        ]);

        return ['success' => 1];
    }

    public function informacionSalidaHistorialLlanta(Request $request){

        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = SalidaLLantas::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarSalidaHistorialLlanta(Request $request){

        $regla = array(
            'id' => 'required',
            'fecha' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        SalidaLLantas::where('id', $request->id)->update([
            'fecha' => $request->fecha,
            'descripcion' => $request->descripcion,
            'talonario' => $request->talonario,
        ]);

        return ['success' => 1];
    }

    ///****** PROVEEDORES ****************


    public function indexProveedor(){
        return view('backend.admin.proveedor.vistaproveedor');
    }

    public function tablaProveedor(){
        $lista = Proveedor::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.proveedor.tablaproveedor', compact('lista'));
    }

    public function nuevaProveedor(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Proveedor();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionProveedor(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Proveedor::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarProveedor(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Proveedor::where('id', $request->id)->first()){

            Proveedor::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }



    ///****** UBICACION LLANTAS ****************


    public function indexUbicacion(){
        return view('backend.admin.ubicacion.vistaubicacion');
    }

    public function tablaUbicacion(){
        $lista = UbicacionBodega::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.ubicacion.tablaubicacion', compact('lista'));
    }

    public function nuevaUbicacion(Request $request){
        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new UbicacionBodega();
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionUbicacion(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = UbicacionBodega::where('id', $request->id)->first()){

            return ['success' => 1, 'info' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarUbicacion(Request $request){

        $regla = array(
            'id' => 'required',
            'nombre' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(UbicacionBodega::where('id', $request->id)->first()){

            UbicacionBodega::where('id', $request->id)->update([
                'nombre' => $request->nombre
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    //*********** REGISTRO DE LLANTAS

    public function indexRegistroEntradaLlanta(){
        $ubicacion = UbicacionBodega::orderBy('nombre')->get();
        return view('backend.admin.registros.llantas.vistaregistrollanta', compact('ubicacion'));
    }

    public function buscadorLlantas(Request $request){

        if($request->get('query')){
            $query = $request->get('query');
            $data = Llantas::where('nombre', 'LIKE', "%{$query}%")
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

    public function guardarEntradaLlantas(Request $request){

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

                    $r = new EntradaLLantas();
                    $r->fecha = $request->fecha;
                    $r->descripcion = $request->descripcion;
                    $r->documento = $nomDocumento;
                    $r->inventario = $request->entrada;
                    $r->factura = $request->factura;
                    $r->save();

                    for ($i = 0; $i < count($request->cantidad); $i++) {

                        $rDetalle = new EntradaLLantasDeta();
                        $rDetalle->id_entrada_llanta = $r->id;
                        $rDetalle->id_llanta = $request->datainfo[$i];
                        $rDetalle->cantidad = $request->cantidad[$i];
                        $rDetalle->precio = $request->precio[$i];
                        $rDetalle->id_ubicacion = $request->bodega[$i];
                        $rDetalle->save();
                    }

                    DB::commit();
                    return ['success' => 1];
                }else{
                    return ['success' => 2];
                }
            }
            else{

                $r = new EntradaLLantas();
                $r->fecha = $request->fecha;
                $r->descripcion = $request->descripcion;
                $r->documento = null;
                $r->inventario = $request->entrada;
                $r->factura = $request->factura;
                $r->save();

                for ($i = 0; $i < count($request->cantidad); $i++) {

                    $rDetalle = new EntradaLLantasDeta();
                    $rDetalle->id_entrada_llanta = $r->id;
                    $rDetalle->id_llanta = $request->datainfo[$i];
                    $rDetalle->cantidad = $request->cantidad[$i];
                    $rDetalle->precio = $request->precio[$i];
                    $rDetalle->id_ubicacion = $request->bodega[$i];
                    $rDetalle->save();
                }

                DB::commit();
                return ['success' => 1];
            }
        }catch(\Throwable $e){
            Log::info('ee' . $e);
            DB::rollback();
            return ['success' => 2];
        }
    }


    // REGISTRO DE LLANTAS
    // **********************************************************************

    public function indexLlantas(){
        $lUnidad = UnidadMedida::orderBy('medida', 'ASC')->get();
        return view('backend.admin.llantas.vistacatalogollantas', compact('lUnidad'));
    }

    public function tablaLlantas(){
        $lista = Llantas::orderBy('nombre', 'ASC')->get();

        foreach ($lista as $item) {

            $medida = '';
            if($dataUnidad = UnidadMedida::where('id', $item->id_medida)->first()){
                $medida = $dataUnidad->medida;
            }
            $item->medida = $medida;

            $entradaDetalle = EntradaLLantasDeta::where('id_llanta', $item->id)->get();

            $valor = 0;
            $dinero = 0;
            foreach ($entradaDetalle as $data){

                // buscar la entrada_detalle de cada salida. obtener la suma de salidas
                $salidaDetalle = SalidaLLantasDeta::where('id_l_entrada_detalle', $data->id)
                    ->where('id_llanta', $item->id)
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

        return view('backend.admin.llantas.tablacatalogollantas', compact('lista'));
    }

    public function nuevoLlantas(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Llantas::where('nombre', $request->nombre)
            ->where('id_medida', $request->unidad)
            ->first()){
            return ['success' => 3];
        }

        $dato = new Llantas();
        $dato->id_medida = $request->unidad;
        $dato->nombre = $request->nombre;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionLlantas(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Llantas::where('id', $request->id)->first()){

            $arrayUnidad = UnidadMedida::orderBy('medida', 'ASC')->get();

            return ['success' => 1, 'material' => $lista, 'unidad' => $arrayUnidad];
        }else{
            return ['success' => 2];
        }
    }

    public function editarLlantas(Request $request){

        $regla = array(
            'nombre' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Llantas::where('id', '!=', $request->id)
            ->where('nombre', $request->nombre)
            ->where('id_medida', $request->unidad)
            ->first()){
            return ['success' => 3];
        }

        Llantas::where('id', $request->id)->update([
            'id_medida' => $request->unidad,
            'nombre' => $request->nombre,
        ]);

        return ['success' => 1];
    }

    // ***** REGISTRO DE SALIDA PARA LLANTAS

    public function indexRegistroSalidaLlantas(){
        $equipos = Equipos::orderBy('nombre')->get();
        return view('backend.admin.registros.llantas.vistasalidallanta', compact('equipos'));
    }

    public function guardarSalidaLlantas(Request $request){

        $rules = array(
            'fecha' => 'required',
        );

        $validator = Validator::make($request->all(), $rules);
        if ( $validator->fails()){
            return ['success' => 0];
        }

        DB::beginTransaction();

        try {

            $r = new SalidaLLantas();
            $r->fecha = $request->fecha;
            $r->descripcion = $request->descripcion;
            $r->talonario = $request->talonario;
            $r->save();

            for ($i = 0; $i < count($request->salida); $i++) {

                // sacar id del material
                $infoEntradaDetalle = EntradaLLantasDeta::where('id', $request->identrada[$i])->first();

                // iterar todas las entradas detalle

                $lista = EntradaLLantasDeta::where('id', $request->identrada[$i])->get();
                $total = 0;

                foreach ($lista as $data){

                    // buscar la entrada_detalle de cada salida. obtener la suma de salidas
                    $salidaDetalle = SalidaLLantasDeta::where('id_l_entrada_detalle', $data->id)
                        ->where('id_llanta', $infoEntradaDetalle->id_llanta)
                        ->sum('cantidad');

                    // total de la cantidad actual
                    $total = $data->cantidad - $salidaDetalle;
                }

                if($total < $request->salida[$i]){
                    return ['success' => 3, 'fila' => ($i), 'cantidad' => $total];
                }

                $rDetalle = new SalidaLLantasDeta();
                $rDetalle->id_salida_llanta = $r->id;
                $rDetalle->id_llanta = $infoEntradaDetalle->id_llanta;
                $rDetalle->id_equipo = $request->equipo[$i];
                $rDetalle->cantidad = $request->salida[$i];
                $rDetalle->id_l_entrada_detalle = $request->identrada[$i];
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

    // muestra lo disponible de cantidades para descontar en llantas
    public function bloqueCantidadesLlantas($id){

        // obtener todas las entradas y obtener cada fila de cantidad

        $lista = EntradaLLantasDeta::where('id_llanta', $id)
            ->where('cantidad', '>', 0)
            ->get();

        $dataArray = array();

        $hayCantidad = false;

        foreach ($lista as $dd){

            $infoEntrada = EntradaLLantas::where('id', $dd->id_entrada_llanta)->first();
            $dd->fecha =
            $dd->factura = $infoEntrada->factura;

            // buscar la entrada_detalle de cada salida. obtener la suma de salidas
            $salidaDetalle = SalidaLLantasDeta::where('id_l_entrada_detalle', $dd->id)
                ->where('id_llanta', $id)
                ->sum('cantidad');

            // total de la cantidad actual
            $cantidadtotal = $dd->cantidad - $salidaDetalle;

            if($cantidadtotal > 0){
                $dataArray[] = [
                    'id' => $dd->id,
                    'fecha' => date("d-m-Y", strtotime($infoEntrada->fecha)),
                    'precio' => number_format((float)$dd->precio, 2, '.', ','),
                    'cantidadtotal' => $cantidadtotal,
                ];
            }
        }

        if(sizeof($dataArray) > 0){
            $hayCantidad = true;
        }

        return view('backend.admin.registros.modal.modalsalidallanta', compact('dataArray', 'hayCantidad'));
    }


    // id material
    public function vistaDetalleLlanta($id){

        $info = Llantas::where('id', $id)->first();
        $repuesto = $info->nombre;
        $medida = '';
        if($infoMedida = UnidadMedida::where('id', $info->id_medida)->first()){
            $medida = $infoMedida->medida;
        }

        return view('backend.admin.llantas.detalle.vistadetallellantas', compact('id', 'repuesto', 'medida'));
    }

    // id material
    public function tablaDetalleLlanta($id){

        $lista =  EntradaLLantasDeta::where('id_llanta', $id)->get();

        $valor = 0;
        foreach ($lista as $data){

            // buscar la entrada_detalle de cada salida. obtener la suma de salidas
            $salidaDetalle = SalidaLLantasDeta::where('id_l_entrada_detalle', $data->id)
                ->where('id_llanta', $id)
                ->sum('cantidad');

            $infoEntrada = EntradaLLantas::where('id', $data->id_entrada_llanta)->first();
            $data->factura = $infoEntrada->factura;
            $data->fecha = date("d-m-Y", strtotime($infoEntrada->fecha));

            $infoBodega = UbicacionBodega::where('id', $data->id_ubicacion)->first();
            $data->ubicacion = $infoBodega->nombre;
            $data->inventario = $infoEntrada->inventario;

            // total de la cantidad actual
            $total = $data->cantidad - $salidaDetalle;

            $data->precio = number_format((float)$data->precio, 2, '.', ',');

            $totalprecio = $total * $data->precio;
            $data->totalprecio = number_format((float)$totalprecio, 2, '.', ',');
            $valor = $valor + $total;
            $data->total = $valor;
            $valor = 0;
        }

        return view('backend.admin.llantas.detalle.tabladetallellantas', compact('lista'));
    }







}
