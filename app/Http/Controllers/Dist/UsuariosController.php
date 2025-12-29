<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


use App\Models\User;
use App\Models\Paises;

use App\Helpers\CommonHelper;

use DB;
use Excel;

class UsuariosController extends Controller
{
    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

        public function Editar($solicitudId){
            /*if(!$this->common->usuariopermiso('004')){
                return redirect('dist/dashboard')->withErrors($this->common->message);
            }*/

            if (Auth::id() != $solicitudId && Auth::user()->tipo_usuario !== 'admin') {
                return response()->view('errors.permiso_denegado', ['mensaje' => 'No tienes permiso para editar esta filiación.'], 403);
            }

            $usuario = User::findOrFail($solicitudId);

            // $Usuario = User::find(Auth::id());

            $pais = Paises::where('estatus', 'Activo')->get();

            if ($pais->isEmpty()) {
                return redirect('dist/usuarios/editar')->withErrors("ERROR AL GUARDAR CODE-0002");
            }

            $catalogosPath = base_path('catalogos.json');

            if (!File::exists($catalogosPath)) {
                return redirect('dist/usuarios/editar')->withErrors("ERROR CATÁLOGOS NO ENCONTRADOS CODE-0003");
            }

            $catalogos = json_decode(
                File::get($catalogosPath),
                true
            );

            if (json_last_error() !== JSON_ERROR_NONE) {
                return redirect('dist/usuarios/editar')->withErrors("ERROR CATÁLOGOS MAL FORMADOS CODE-0004");
            }

            // $solicitud = DB::table('solicitud')
            //     ->where('solicitud.id', '=', $solicitudId)
            //     ->leftjoin('tipoAtencion', 'tipoAtencion.id', '=', 'solicitud.IdtipoAtencion')
            // ->leftjoin('departamento', 'departamento.id', '=', 'solicitud.departamentoId')
            // ->leftjoin('consumidor', 'consumidor.solicitudId', '=', 'solicitud.id')
            //     //->where('rubro.distribuidorId', Auth::user()->distribuidorId)
            // ->select('solicitud.*',
            // 'departamento.id as departamentoId', 
            // 'departamento.nombre as departamentoNombre',
            // 'tipoAtencion.id as IdTipoAtencion', 
            // 'tipoAtencion.descripcion', 
            // //'consumidor.*')
            // 'consumidor.cedula',
            // 'consumidor.nombre',
            // 'consumidor.apellido',
            // 'consumidor.fechaNacimiento',
            // 'consumidor.correo',
            // 'consumidor.genero',
            // 'consumidor.telefono',
            // 'consumidor.tipoConsumidor')
            // ->first();


            // //return $solicitud;

            // if(empty($solicitud)){
            //     return redirect('dist/solicitud')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0004");
            // }

            //  view()->share('solicitud', $solicitud);

            return view('dist.usuarios.editar', compact(
                'pais',
                'catalogos'
                // 'Sujeto',
                // 'provincia',
                // 'distrito',
                // 'corregimiento',
                // 'prefillTitularNF',
                // 'prefillAfinidadId',
                // 'Afinidad'
            ));
            // return \View::make('dist.usuarios.editar');
        }
}
