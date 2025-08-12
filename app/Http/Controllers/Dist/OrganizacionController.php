<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
//use App\Imports\StoreCebececoImport;

use App\Models\Organizacion;

use DB;
use Excel;

class OrganizacionController extends Controller
{
    private $request;
	private $common;

    public function __construct(Request $request){
        $this->request = $request;
        //$this->common = new Common;
    }

    public function Index(){

        return \View::make('dist/organizacion/index');

    }

    public function PostIndex(){

        	
		$request = $this->request->all();
		$columnsOrder = isset($request['order'][0]['column']) ? $request['order'][0]['column'] : '0';
		$orderBy=isset($request['columns'][$columnsOrder]['data']) ? $request['columns'][$columnsOrder]['data'] : 'id';
		$order = isset($request['order'][0]['dir']) ? $request['order'][0]['dir'] : 'ASC';
		$length = isset($request['length']) ? $request['length'] : '15';

		$currentPage = $request['currentPage'];  
		Paginator::currentPageResolver(function() use ($currentPage){
			return $currentPage;
		});

		$query = DB::table('bo_kpi_store_cebececo')
		 ->select('bo_kpi_store_cebececo.*')
         ->orderBy($orderBy,$order);

		/*if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
			$query->where('bo_kpi_store_cebececo.nombre_segmento', 'like', '%'.trim($request['searchInput']).'%');
		}*/

        if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
			$query->where(
				function ($query) use ($request) {
					$query->orWhere('bo_kpi_store_cebececo.regional', 'like', '%'.trim($request['searchInput']).'%');
					$query->orWhere('bo_kpi_store_cebececo.segmento', 'like', '%'.trim($request['searchInput']).'%');
                    $query->orWhere('bo_kpi_store_cebececo.formato', 'like', date('Y-m-d',strtotime($request['searchInput'])));
					$query->orWhere('cliebo_kpi_store_cebececonte.nombre_segmento', 'like', '%'.trim($request['searchInput']).'%');
					$query->orWhere('bo_kpi_store_cebececo.direccion', 'like', '%'.trim($request['searchInput']).'%');
                    $query->orWhere('bo_kpi_store_cebececo.cebe_ceco', 'like', '%'.trim($request['searchInput']).'%');
					$query->orWhere('bo_kpi_store_cebececo.nombre_cebe_ceco', 'like', '%'.trim($request['searchInput']).'%');
				}
			 );		
		}
		   
		$storecebececos = $query->paginate($length); 
	
		$result = $storecebececos->toArray();
		$data = array();
		foreach($result['data'] as $value){

			if($value->estatus == 'Activo'){
				$detalle = '<a href="/dist/storecebececo/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
								<a href="/dist/storecebececo/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
								<a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
			}else{
				$detalle = '<a href="/dist/storecebececo/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
								<a href="/dist/storecebececo/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
								<a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
			}

			$data[] = array(
				  "DT_RowId" => $value->id,
				  "id" => $value->id,
				  "regional"=> $value->regional,
				  "formato"=> $value->formato,
				  "nombreSegmento"=> $value->nombre_segmento,
				  "direccion"=> $value->direccion,
				  "cebeCeco"=> $value->cebe_ceco,
				  "nombreCebeCeco"=> $value->nombre_cebe_ceco,
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

    public function Nuevo(){
    	/*if(!$this->common->usuariopermiso('004')){
    		return redirect('dist/dashboard')->withErrors($this->common->message);
    	}*/
    	
    	return \View::make('dist/storecebececo/nuevo');
    }

    public function postNuevo(){
    	
        /*if(!$this->common->usuariopermiso('004')){
    		return redirect('dist/dashboard')->withErrors($this->common->message);
    	}*/

        //return $this->request->all();

    	$storecebececoExiste = StoreCebececo::where('cebe_ceco', $this->request->cebeCeco)
        //->where('distribuidorId', Auth::user()->distribuidorId)
        ->first();
    	if(!empty($storecebececoExiste)){
    		return redirect('dist/storecebececo/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0001");
    	}

		DB::beginTransaction();
		try { 	
			$storecebececo = new StoreCebececo;
			$storecebececo->regional         = trim($this->request->regional);
            $storecebececo->segmento         = trim($this->request->segmento);
			$storecebececo->formato          = trim($this->request->formato);
			$storecebececo->nombre_segmento  = trim($this->request->nombreSegmento);
			$storecebececo->direccion        = trim($this->request->direccion);
			$storecebececo->cebe_ceco        = trim($this->request->cebeCeco);
			$storecebececo->nombre_cebe_ceco = trim($this->request->nombreCebeCeco);
			$storecebececo->cod_region       = trim($this->request->codRegion);
			$storecebececo->cod_formato      = trim($this->request->codFormato);

			if(isset($this->request->codDireccion)){
			$storecebececo->cod_direccion    = trim($this->request->codDireccion);
			}
			if(isset($this->request->comentario)){
				$storecebececo->comentario       = trim($this->request->comentario); 
			}
			

			$storecebececo->estatus          = 'Activo';
			$storecebececo->created_at       = date('Y-m-d H:i:s');
			$storecebececo->usuarioId        = Auth::user()->id;
			$result = $storecebececo->save();

		} catch(\Illuminate\Database\QueryException $ex){ 
			DB::rollBack();
			return redirect('dist/storecebececo/nuevo')->withErrors('ERROR AL GUARDAR STORE CEBECECO CODE-0002'.$ex);
		}
		
		if($result != 1){
			DB::rollBack();
			return redirect('dist/storecebececo/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0003");
		}
		DB::commit();

		return redirect('dist/storecebececo')->with('alertSuccess', 'STORE CEBECECO HA SIDO INGRESADA');
    }


}
