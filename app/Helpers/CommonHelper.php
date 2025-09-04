<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

use DB;

class CommonHelper
{
    public $message = 'No tiene permiso para acceder a esta sección.';

    public function usuariopermiso($codigoPermiso) {

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

    public function generaCodigoSCR(): string {
        // Ajusta a tu formato preferido (y/o valida uniqueness si quieres)
        return 'SCR-' . now()->format('YmdHis') . '-' . random_int(100, 999);
    }

    public function algunaPersonaTieneSolicitudActiva(array $numFiliaciones): bool{

        $numFiliaciones = array_values(array_unique(
            array_map(fn($v) => trim((string)$v), $numFiliaciones)
        ));

        if (empty($numFiliaciones)) return false;

        return DB::table('solicitudes_cambio_personas as p')
            ->join('solicitudes_cambio_residencia as s', 's.id', '=', 'p.solicitud_id')
            ->whereIn('p.num_filiacion', $numFiliaciones)
            ->whereNotIn('s.estatus', ['Rechazada','Cancelada'])
            ->exists();

    }

}
