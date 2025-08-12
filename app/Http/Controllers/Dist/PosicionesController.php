<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Models\Posiciones;

use DB;
use Excel;

class PosicionesController extends Controller
{
    //
    private $request;
    private $common;

public function __construct(Request $request){
    $this->request = $request;
}

public function Index(){

    return \view('dist/posiciones/index');
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

    $query = DB::table('posiciones')
    ->leftjoin('departamento', 'departamento.id', '=', 'posiciones.departamentoId')
     ->select('posiciones.*', 'departamento.nombre as departamento')
     ->orderBy($orderBy,$order);


    if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
        $query->where(
            function ($query) use ($request) {
                $query->orWhere('posiciones.nombre', 'like', '%'.trim($request['searchInput']).'%');
                $query->orWhere('posiciones.codigo', 'like', '%'.trim($request['searchInput']).'%');
                $query->orWhere('departamento.nombre', 'like', '%'.trim($request['searchInput']).'%');
            }
         );		
    }
       
    $posiciones = $query->paginate($length); 

    $result = $posiciones->toArray();
    $data = array();
    foreach($result['data'] as $value){

        if($value->estatus == 'Activo'){
            $detalle = '<a href="/dist/posiciones/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                            <a href="/dist/posiciones/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                            <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
        }else{
            $detalle = '<a href="/dist/posiciones/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                            <a href="/dist/posiciones/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                            <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
        }

        $data[] = array(
              "DT_RowId" => $value->id,
              "id" => $value->id,
              "nombre"=> $value->nombre,
              "departamento"=> $value->departamento,
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
    return response()
          ->json($response);


}

public function Nuevo(){
    /*if(!$this->common->usuariopermiso('004')){
        return redirect('dist/dashboard')->withErrors($this->common->message);
    }*/
        $departamento = DB::table('departamento')
    	->where('estatus', '=', 'Activo')
    	->where('organizacionId', '=', '1')
		->select('id', 'nombre', 'codigo')
		->get();

		if(empty($departamento)){
    		return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
    	}	
		view()->share('departamento', $departamento);	


    return \View::make('dist/posiciones/nuevo');
}

public function postNuevo(){
    
    /*if(!$this->common->usuariopermiso('004')){
        return redirect('dist/dashboard')->withErrors($this->common->message);
    }*/

    //return $this->request->all();

    $posicionesExiste = Posiciones::where('nombre', $this->request->nombre)
    //->where('distribuidorId', Auth::user()->distribuidorId)
    ->first();
    if(!empty($posicionesExiste)){
        return redirect('dist/posiciones/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0001");
    }

    DB::beginTransaction();
    try { 	
        $posiciones = new Posiciones;
        $posiciones->nombre         = trim($this->request->nombre);

        if(isset($this->request->comentario)){
            $posiciones->infoextra       = trim($this->request->comentario); 
        }
        
        $posiciones->estatus          = 'Activo';
        $posiciones->created_at       = date('Y-m-d H:i:s');
        $posiciones->usuarioId        = Auth::user()->id;
        $posiciones->departamentoId   = trim($this->request->departamento);
        $result = $posiciones->save();

        $posicionesId = $posiciones->id;

        if(empty($posicionesId)){
            DB::rollBack();
            return redirect('dist/posiciones/nuevo')->withErrors("ERROR AL GUARDAR EL CONTRATO NO SE GENERO UN # DE CONTRATO CORRECTO CODE-0196");
        }
        
        $posicionesCode = str_pad($posicionesId,5, "0",STR_PAD_LEFT);

        //return $posicionesCode;

        $posicionesUpdate = Posiciones::find($posicionesId);
        $posicionesUpdate->codigo = $posicionesCode;
        $result = $posicionesUpdate->save();	

    } catch(\Illuminate\Database\QueryException $ex){ 
        DB::rollBack();
        return redirect('dist/posiciones/nuevo')->withErrors('ERROR AL GUARDAR STORE CEBECECO CODE-0002'.$ex);
    }
    
    if($result != 1){
        DB::rollBack();
        return redirect('dist/posiciones/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0003");
    }
    DB::commit();

    return redirect('dist/posiciones')->with('alertSuccess', 'STORE CEBECECO HA SIDO INGRESADA');
}

public function Editar($posicionesId){
    /*if(!$this->common->usuariopermiso('004')){
        return redirect('dist/dashboard')->withErrors($this->common->message);
    }*/

    $posiciones = DB::table('posiciones')
     ->where('posiciones.id', '=', $posicionesId)
     //->where('rubro.distribuidorId', Auth::user()->distribuidorId)
     ->leftjoin('departamento', 'departamento.id', '=', 'posiciones.departamentoId')
     ->select('posiciones.*', 'departamento.nombre as departamento')->first();

     //return $posiciones;

    if(empty($posiciones)){
        return redirect('dist/posiciones')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0004");
    }

    $departamento = DB::table('departamento')
    ->where('estatus', '=', 'Activo')
    ->where('organizacionId', '=', '1')
    ->select('id', 'nombre', 'codigo')
    ->get();

    if(empty($departamento)){
        return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
    }	

    view()->share('departamento', $departamento);	

    view()->share('posiciones', $posiciones);

    return \View::make('dist/posiciones/editar');
}

public function PostEditar(){
    /*if(!$this->common->usuariopermiso('004')){
        return redirect('dist/dashboard')->withErrors($this->common->message);
    }*/

    $request = $this->request->all();

    //return $request;

    $posicionesId = isset($this->request->posicionesId) ? $this->request->posicionesId: '';

    //return $posicionesId;

    $posiciones = Posiciones::where('id', $posicionesId)
    //->where('distribuidorId',Auth::user()->distribuidorId)
    ->first();

    if(empty($posiciones)){
        return redirect('dist/posiciones')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0005");
    }

    DB::beginTransaction();
        $posicionesUpdate = Posiciones::find($posicionesId);
        $posicionesUpdate->nombre           = $this->request->nombre;
        $posicionesUpdate->departamentoId   = $this->request->departamento;
        $posicionesUpdate->infoextra        = $this->request->comentario;
        
        $result = $posicionesUpdate->save();

    if($result != 1){
        DB::rollBack();

        return redirect('dist/posiciones/editar/'.$posicionesId)->withErrors("ERROR AL EDITAR ELEMENTOS DE STORE CEBECECO CODE-0006");
    }

    DB::commit();

    return redirect('dist/posiciones/')->with('alertSuccess', 'STORE CEBECECO HA SIDO EDITADO');
}

public function Mostrar($posicionesId){
    /*if(!$this->common->usuariopermiso('004')){
        return redirect('dist/dashboard')->withErrors($this->common->message);
    }*/

    $posiciones = DB::table('posiciones')
     ->where('posiciones.id', '=', $posicionesId)
     ->leftjoin('departamento', 'departamento.id', '=', 'posiciones.departamentoId')
     ->select('posiciones.*', 'departamento.nombre as departamento')->first();

    if(empty($posiciones)){
        return redirect('dist/posiciones')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0007");
    }

     view()->share('posiciones', $posiciones);

    return \View::make('dist/posiciones/mostrar');
}
public function Desactivar(){
    /*if(!$this->common->usuariopermiso('004')){
        return response()
          ->json(['response' => false]);
    }*/
    
    $posicionesExiste = Posiciones::where('id', $this->request->posicionesId)
                    //->where('distribuidorId', Auth::user()->distribuidorId)
                    ->first();
    if(!empty($posicionesExiste)){

        $estatus = 'Inactivo';
        if($posicionesExiste->estatus == 'Inactivo'){
            $estatus = 'Activo';	
        }

        $affectedRows = Posiciones::where('id', '=', $this->request->posicionesId)
                        ->update(['estatus' => $estatus]);
        
        return response()
          ->json(['response' => TRUE]);
    }

    return response()
          ->json(['response' => false]);
}

}
