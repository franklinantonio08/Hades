<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\Usuariocitas;
use App\Models\Citasconsular;
use App\Models\Consulados;
use App\Models\Usuarioscitas;
use App\Models\Serviciosconsulares;

use App\Helpers\CommonHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\CitaConfirmacionMail;

use DB;
use Excel;

class CitasconsularController extends Controller
{
    private $request;
    private $common;

    public function __construct(Request $request){

        $this->request = $request;
        $this->common = New CommonHelper();
    }

    public function Index(){

        return view('dist.citasconsular.index');
    }


    public function PostIndex(){

        //return $colaboradoresId;

        $request = $this->request->all();

        //return $request;
        $columnsOrder = isset($request['order'][0]['column']) ? $request['order'][0]['column'] : '0';
        $orderBy=isset($request['columns'][$columnsOrder]['data']) ? $request['columns'][$columnsOrder]['data'] : 'id';
        $order = isset($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'ASC';
        $length = isset($request['length']) ? $request['length'] : '15';

        $currentPage = $request['currentPage'];  
        Paginator::currentPageResolver(function() use ($currentPage){
            return $currentPage;
        });

        $desde = date('Y-m-d', strtotime($request['desde'])) . ' 00:00:00';
        $hasta = date('Y-m-d', strtotime($request['hasta'])) . ' 23:59:59';

        $query = DB::table('citas')
        ->leftjoin('servicios_consulares', 'servicios_consulares.id', '=', 'citas.servicioId')
        ->leftjoin('consulado', 'consulado.id', '=', 'citas.consuladoId')
        //->leftjoin('users', 'users.id', '=', 'citas.usuarioId')
        ->leftjoin('usuarios_citas', 'usuarios_citas.usuarioId', '=', 'citas.usuarioId')
        ->leftjoin('rid_paises', 'rid_paises.id', '=', 'consulado.paisId')
        //->select('citas.*', 'servicios_consulares.descripcion as servicios', 'consulado.descripcion as consulado', '')
        ->where('usuarios_citas.usuarioId', '=',  Auth::user()->id)
        ->whereBetween('citas.created_at', [$desde, $hasta])
        ->select(
            'citas.*', 
            'servicios_consulares.descripcion as servicios', 
            'consulado.descripcion as consulado',
            'rid_paises.pais',
            DB::raw("CONCAT(usuarios_citas.primerNombre, ' ', usuarios_citas.primerApellido) as usuario_nombre_completo"), // Concatenación de nombre y apellido
           DB::raw("ROW_NUMBER() OVER (ORDER BY citas.id) AS row_num")
        )
        //->where('users.tipo_usuario', '=', 'UsuarioExterno')
        ->orderBy($orderBy, $order);


        if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
            $query->where(
                function ($query) use ($request) {
                    $query->orWhere('servicios_consulares.descripcion', 'like', '%'.trim($request['searchInput']).'%');
                    $query->orWhere('citas.codigo', 'like', '%'.trim($request['searchInput']).'%');
                }
             );		
        }
           
        $solicitud = $query->paginate($length); 
    
        $result = $solicitud->toArray();
        $data = array();
        foreach($result['data'] as $value){

            $detalle = '<div class="dropdown text-center">
                <button class="btn btn-primary border-0 dropdown-toggle p-1" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical fs-5"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">';

            // Ver (siempre)
            $detalle .= '<li><a href="#" attr-id="'.$value->id.'" class="dropdown-item text-warning mostrar">
                            <i class="bi bi-eye me-2 text-warning"></i> Ver
                        </a></li>';

            // Editar (comentar o descomentar según se necesite)
            $detalle .= '<!-- 
            <li><a href="/dist/citasConsular/editar/'.$value->id.'" class="dropdown-item text-secondary">
                <i class="bi bi-pencil me-2 text-secondary"></i> Editar
            </a></li>
            -->';

            // Si está pendiente: imprimir y desactivar
            if ($value->estatus === 'Pendiente') {
                $detalle .= '<li><a href="#" attr-id="'.$value->id.'" class="dropdown-item text-success impresion">
                                <i class="bi bi-printer me-2 text-success"></i> Imprimir
                            </a></li>';

                $detalle .= '<li><a href="#" attr-id="'.$value->id.'" class="dropdown-item text-danger desactivar">
                                <i class="bi bi-trash me-2 text-danger"></i> Desactivar
                            </a></li>';
            }

            $detalle .= '</ul></div>';

            $fecha_sol = (new \DateTime($value->created_at))->format('Y-m-d');


            $data[] = array(
                  "DT_RowId" => $value->row_num,
                  "id" => $value->row_num,
                  "consulado"=> $value->consulado,
                  "servicios"=> $value->servicios,
                  "usuario"=> $value->usuario_nombre_completo,                  
                  "codigo"=> $value->codigo,
                  "fecha_sol"=> $fecha_sol,                  
                  "fecha"=> $value->fecha,
                  "pais"=> $value->pais,
                  "estatus"=> $value->estatus,
                  "detalle"=> $detalle
            );
        }

        $response = array(
                'draw' => isset($request['draw']) ? $request['draw'] : '1',
                'recordsTotal' => $result['total'],
                'recordsFiltered' => $result['total'],
                'data' => $data,
            );
        return response()
              ->json($response);


    }
}
