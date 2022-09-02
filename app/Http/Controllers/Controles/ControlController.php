<?php

namespace App\Http\Controllers\Controles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ControlController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function indexRedireccionamiento(){

        $user = Auth::user();

        // ADMINISTRADOR
        if($user->hasPermissionTo('sidebar.roles.y.permisos')){
            $ruta = 'admin.roles.index';
        }

        // Inventario
        else  if($user->hasPermissionTo('sidebar.seccion.materiales')){
            $ruta = 'admin.estadisticas.index';
        }

        return view('backend.index', compact( 'ruta', 'user'));
    }

    public function indexSinPermiso(){
        return view('errors.403');
    }
}
