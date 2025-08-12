<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use App\Models\Pais;

use DB;
use Excel;


class PaisController extends Controller
{
    //
    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function Index(){

        return \view('dist/pais/index');
    }

    public function PostIndex(){

//return 

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

		$query = DB::table('ca_pais')
		 ->select('ca_pais.*')
         ->orderBy($orderBy,$order);


        if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
			$query->where(
				function ($query) use ($request) {
					$query->orWhere('ca_pais.descripcion', 'like', '%'.trim($request['searchInput']).'%');
					$query->orWhere('ca_pais.codNumericoISo', 'like', '%'.trim($request['searchInput']).'%');
				}
			 );		
		}
		   
		$pais = $query->paginate($length); 
	
		$result = $pais->toArray();
		$data = array();
		foreach($result['data'] as $value){

			if($value->estatus == 'Activo'){
				$detalle = '<a href="/dist/pais/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
								<a href="/dist/pais/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
								<a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
			}else{
				$detalle = '<a href="/dist/pais/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
								<a href="/dist/pais/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
								<a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
			}

			$data[] = array(
				  "DT_RowId" => $value->id,
				  "id" => $value->id,
				  "nombre"=> $value->descripcion,
				  "codigo"=> $value->codNumericoIso,
                  "codigoalfa3"=> $value->codigoAlfa3,
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
    	
    	return \View::make('dist/departamento/nuevo');
    }

    public function postNuevo(){

    	
        /*if(!$this->common->usuariopermiso('004')){
    		return redirect('dist/dashboard')->withErrors($this->common->message);
    	}*/

        //return $this->request->all();

    	$departamentoExiste = Departamento::where('nombre', $this->request->nombre)
        //->where('distribuidorId', Auth::user()->distribuidorId)
        ->first();

    	if(!empty($departamentoExiste)){
    		return redirect('dist/departamento/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0001");
    	}

		DB::beginTransaction();
		try { 	
			$departamento = new Departamento;
			$departamento->nombre         = trim($this->request->nombre);

			if(isset($this->request->comentario)){
				$departamento->infoextra       = trim($this->request->comentario); 
			}
			
			$departamento->estatus          = 'Activo';
			$departamento->created_at       = date('Y-m-d H:i:s');
			$departamento->usuarioId        = Auth::user()->id;
            $departamento->organizacionId = 1;
			$result = $departamento->save();

            $departamentoId = $departamento->id;

			if(empty($departamentoId)){
				DB::rollBack();
				return redirect('dist/departamento/nuevo')->withErrors("ERROR AL GUARDAR EL CONTRATO NO SE GENERO UN # DE CONTRATO CORRECTO CODE-0196");
			}
			
			$departamentoCode = str_pad($departamentoId,5, "0",STR_PAD_LEFT);
            //return $departamentoCode;
			$departamentoUpdate = Departamento::find($departamentoId);
			$departamentoUpdate->codigo = $departamentoCode;
			$result = $departamentoUpdate->save();	

		} catch(\Illuminate\Database\QueryException $ex){ 
			DB::rollBack();
			return redirect('dist/departamento/nuevo')->withErrors('ERROR AL GUARDAR STORE CEBECECO CODE-0002'.$ex);
		}
		
		if($result != 1){
			DB::rollBack();
			return redirect('dist/departamento/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0003");
		}
		DB::commit();

		return redirect('dist/departamento')->with('alertSuccess', 'STORE CEBECECO HA SIDO INGRESADA');
    }

	public function Editar($departamentoId){
		/*if(!$this->common->usuariopermiso('004')){
    		return redirect('dist/dashboard')->withErrors($this->common->message);
    	}*/

    	$departamento = DB::table('departamento')
		 ->where('departamento.id', '=', $departamentoId)
		 //->where('rubro.distribuidorId', Auth::user()->distribuidorId)
		 ->select('departamento.*')->first();

    	if(empty($departamento)){
    		return redirect('dist/departamento')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0004");
    	}

    	view()->share('departamento', $departamento);
    	return \View::make('dist/departamento/editar');
    }

    public function PostEditar(){
    	/*if(!$this->common->usuariopermiso('004')){
    		return redirect('dist/dashboard')->withErrors($this->common->message);
    	}*/

    	$request = $this->request->all();

        //return $request;

    	$departamentoId = isset($this->request->departamentoId) ? $this->request->departamentoId: '';

        //return $departamentoId;

    	$departamento = departamento::where('id', $departamentoId)
        //->where('distribuidorId',Auth::user()->distribuidorId)
        ->first();

    	if(empty($departamento)){
    		return redirect('dist/departamento')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0005");
    	}

		DB::beginTransaction();
	    	$departamentoUpdate = Departamento::find($departamentoId);
			$departamentoUpdate->nombre          = $this->request->nombre;
            $departamentoUpdate->infoextra        = $this->request->comentario;
			
            $result = $departamentoUpdate->save();

		if($result != 1){
			DB::rollBack();

			return redirect('dist/departamento/editar/'.$departamentoId)->withErrors("ERROR AL EDITAR ELEMENTOS DE STORE CEBECECO CODE-0006");
		}

		DB::commit();

		return redirect('dist/departamento/')->with('alertSuccess', 'STORE CEBECECO HA SIDO EDITADO');
    }

    public function Mostrar($departamentoId){
    	/*if(!$this->common->usuariopermiso('004')){
    		return redirect('dist/dashboard')->withErrors($this->common->message);
    	}*/

    	$departamento = DB::table('departamento')
    	 ->where('departamento.id', '=', $departamentoId)
		 ->select('departamento.*')->first();

    	if(empty($departamento)){
    		return redirect('dist/departamento')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0007");
    	}

        //return $compania;

    	 view()->share('departamento', $departamento);

    	return \View::make('dist/departamento/mostrar');
    }
    public function Desactivar(){
    	/*if(!$this->common->usuariopermiso('004')){
    		return response()
              ->json(['response' => false]);
    	}*/
    	
    	$departamentoExiste = Departamento::where('id', $this->request->departamentoId)
    					//->where('distribuidorId', Auth::user()->distribuidorId)
    					->first();
		if(!empty($departamentoExiste)){

			$estatus = 'Inactivo';
			if($departamentoExiste->estatus == 'Inactivo'){
				$estatus = 'Activo';	
			}

			$affectedRows = Departamento::where('id', '=', $this->request->departamentoId)
							->update(['estatus' => $estatus]);
			
			return response()
              ->json(['response' => TRUE]);
		}

		return response()
              ->json(['response' => false]);
    }
}
