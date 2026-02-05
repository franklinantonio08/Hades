<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

use App\Models\Solicitud;
use App\Models\Afinidad;
use App\Models\User;

use App\Helpers\CommonHelper;

use DB;
use Excel;

class AfiliacionController extends Controller
{
    private $request;
    private $common;

    public function __construct(Request $request){

        $this->request = $request;
        $this->common = New CommonHelper();
    }

    public function Index(){

        $Usuario = User::find(Auth::id());
        $esAbogado = strcasecmp(trim((string)$Usuario->tipo_usuario), 'abogado') === 0;

        $afinidad = Afinidad::query()
                ->where('estatus', 'Activo')
                ->when($esAbogado, fn($q) => $q->where('id', 1)) // <= filtro solo para abogado
                ->select('id', 'descripcion')
                ->orderByRaw("CASE WHEN id = 1 THEN 0 ELSE 1 END, descripcion") // deja 1 primero
                ->get();

        if ($afinidad->isEmpty()) {
            return back()->withErrors("ERROR ESTATUS OPERATIVO ESTA VACIO CODE-0002");
        }

        // (opcional) si quieres asegurar que abogado tenga id=1 disponible:
        if ($esAbogado && !$afinidad->contains('id', 1)) {
            return back()->withErrors("No está disponible la afinidad requerida (id=1) para abogado.");
        }

        $tipo_usuario = $esAbogado ? 'abogado' : 'solicitante';
        $afinidad_preseleccionada = old('afinidadId') ?? ($esAbogado ? 1 : '');

        return view('dist.filiacion.index', compact('afinidad', 'tipo_usuario', 'afinidad_preseleccionada'));

    }

    public function Nuevo(){


        return view('dist.filiacion.nuevo');

    }
}
