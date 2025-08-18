<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use App\Models\Permisos;

use DB;
use Excel;


class PermisosController extends Controller
{
    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function Index(){

        return view('dist.permisos.index');
    }      
    
    public function PostIndex(){    
    
        $request = $this->request->all();

        //return  $this->request->all();
        
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

        $query = DB::table('codigo_permiso')
        //->leftjoin('rid_puestocontrol', 'RID_migrante.puestoId', '=', 'rid_puestocontrol.id')
        //->leftjoin('rid_paises', 'RID_migrante.paisId', '=', 'rid_paises.id')
        //->leftjoin('rid_regiones', 'rid_paises.region_id', '=', 'rid_regiones.id')
        //->leftjoin('rid_afinidad', 'RID_migrante.afinidadId', '=', 'rid_afinidad.id')
        ->whereBetween('codigo_permiso.created_at', [$desde, $hasta])
        ->select(
                'codigo_permiso.*', 
                //'rid_puestocontrol.descripcion AS puesto_control',
                //'rid_paises.pais AS pais',
               // 'rid_regiones.continente AS region_continente',
                //'rid_regiones.region AS region',
                //'rid_afinidad.descripcion AS afinidad'
                )
        ->orderBy($orderBy,$order);

        if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
            $query->where(
                function ($query) use ($request) {
                    $query->orWhere('codigo_permiso.descripcion', 'like', '%'.trim($request['searchInput']).'%');
                    $query->orWhere('codigo_permiso.codigo', 'like', '%'.trim($request['searchInput']).'%');
                    //$query->orWhere('RID_migrante.documento', 'like', '%'.trim($request['searchInput']).'%');
                }
                );		 
        }
        
        $migrantes = $query->paginate($length); 
    
        $result = $migrantes->toArray();
        $data = array();
        foreach($result['data'] as $value){

            if($value->estatus == 'Activo'){
                $detalle = '<a href="/admin/RIDmigrantes/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                <a href="/admin/RIDmigrantes/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
            }else{
                $detalle = '<a href="/admin/RIDmigrantes/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                <a href="/admin/RIDmigrantes/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
            }

            $data[] = array(
                    "DT_RowId" => $value->id,
                    "id" => $value->id,
                    "descripcion"=> $value->descripcion,                         
                    "codigo"=> $value->codigo,                  
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
        
        return response()->json($response);    
    
    }
}
