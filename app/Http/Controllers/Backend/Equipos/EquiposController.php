<?php

namespace App\Http\Controllers\Backend\Equipos;

use App\Http\Controllers\Controller;
use App\Models\Equipos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EquiposController extends Controller
{
    public function indexEquipos(){
        return view('backend.admin.repuestos.equipos.vistaequipos');
    }

    public function tablaEquipos(){
        $lista = Equipos::orderBy('nombre', 'ASC')->get();
        return view('backend.admin.repuestos.equipos.tablaequipos', compact('lista'));
    }

    public function nuevaEquipos(Request $request){
        $regla = array(
            'equipo' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        $dato = new Equipos();
        $dato->nombre = $request->equipo;

        if($dato->save()){
            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

    public function informacionEquipos(Request $request){
        $regla = array(
            'id' => 'required',
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if($lista = Equipos::where('id', $request->id)->first()){

            return ['success' => 1, 'equipo' => $lista];
        }else{
            return ['success' => 2];
        }
    }

    public function editarEquipos(Request $request){

        $regla = array(
            'id' => 'required',
            'equipo' => 'required'
        );

        $validar = Validator::make($request->all(), $regla);

        if ($validar->fails()){ return ['success' => 0];}

        if(Equipos::where('id', $request->id)->first()){

            Equipos::where('id', $request->id)->update([
                'nombre' => $request->equipo
            ]);

            return ['success' => 1];
        }else{
            return ['success' => 2];
        }
    }

}
