<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

use DB;

class CommonHelper
{
    public $message = 'No tiene permiso para acceder a esta sección.';

    public function usuariopermiso($codigoPermiso)
    {
        $usuariopermiso = DB::table('usuario_permiso')
        ->where('usuario_permiso.usuarioId', '=', Auth::user()->id)
        ->where('usuario_permiso.codigo', '=', $codigoPermiso)
        ->select('usuario_permiso.valor')->first();

        if(empty($usuariopermiso)){
            return false;
        }
        if($usuariopermiso->valor != 'TRUE'){
            return false;
        }

        return true;
    }
}
