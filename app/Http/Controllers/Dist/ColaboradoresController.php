<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\Colaboradores;
use App\Models\User; 

use DB;
use Excel;

class ColaboradoresController extends Controller
{
    //
    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function Index(){

        return \view('dist/colaboradores/index');
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

    $query = DB::table('colaboradores')
    ->leftjoin('departamento', 'departamento.id', '=', 'colaboradores.departamentoId')
     ->select('colaboradores.*', 'departamento.nombre as departamento')
     ->orderBy($orderBy,$order);


    if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
        $query->where(
            function ($query) use ($request) {
                $query->orWhere('colaboradores.nombre', 'like', '%'.trim($request['searchInput']).'%');
                $query->orWhere('colaboradores.codigo', 'like', '%'.trim($request['searchInput']).'%');
                $query->orWhere('departamento.nombre', 'like', '%'.trim($request['searchInput']).'%');
            }
         );		
    }
       
    $colaboradores = $query->paginate($length); 

    $result = $colaboradores->toArray();
    $data = array();
    foreach($result['data'] as $value){

        if($value->estatus == 'Activo'){
            $detalle = '<a href="/dist/colaboradores/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                            <a href="/dist/colaboradores/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                            <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
        }else{
            $detalle = '<a href="/dist/colaboradores/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                            <a href="/dist/colaboradores/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                            <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
        }

        $data[] = array(
              "DT_RowId" => $value->id,
              "id" => $value->id,
              "nombre"=> $value->nombre,
              "codigo"=> $value->codigo,
              "departamento"=> $value->correo,
              "correo"=> $value->telefono,
              "tipousuario"=> $value->tipoUsuario,
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

        

        $provincia = DB::table('provincia')
    	->where('estatus', '=', 'Activo')
    	//->where('paisId', '=', '124')
		->select('id', 'nombre', 'codigo')
		->get();

		if(empty($provincia)){
    		return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
    	}	
		view()->share('provincia', $provincia);	


    return \View::make('dist/colaboradores/nuevo');
}

public function postNuevo(){

    //return $this->request->all();

    $posicionesExiste = Colaboradores::where('cedula', $this->request->cedula)
    //->where('distribuidorId', Auth::user()->distribuidorId)
    ->first();
    if(!empty($posicionesExiste)){
        return redirect('dist/colaboradores/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0001");
    }

    DB::beginTransaction();
    try { 	
        $posiciones = new Colaboradores;

        $posiciones->cedula         = trim($this->request->cedula);
        $posiciones->nombre         = trim($this->request->nombre);
        $posiciones->apellido       = trim($this->request->apellido);
        $posiciones->correo         = trim($this->request->correo);
        $posiciones->tipoSangre     = trim($this->request->tipoSangre);
        $posiciones->genero         = trim($this->request->genero);
        $posiciones->tipoUsuario    = trim($this->request->tipoUsuario);
        $posiciones->telefono       = trim($this->request->telefono);
        $posiciones->departamentoId         = trim($this->request->departamento);
        $posiciones->posicionId     = trim($this->request->posiciones);

        $posiciones->estatus          = 'Activo';
        $posiciones->created_at       = date('Y-m-d H:i:s');
        $posiciones->usuarioId        = Auth::user()->id;

        if(isset($this->request->comentario)){
            $posiciones->infoextra       = trim($this->request->comentario); 
        }

        $result = $posiciones->save();

        $posicionesId = $posiciones->id;

        if(empty($posicionesId)){
            DB::rollBack();
            return redirect('dist/colaboradores/nuevo')->withErrors("ERROR AL GUARDAR EL CONTRATO NO SE GENERO UN # DE CONTRATO CORRECTO CODE-0196");
        }
        
        $posicionesCode = str_pad($posicionesId,5, "0",STR_PAD_LEFT);

        //return $posicionesCode;

        $posicionesUpdate = Colaboradores::find($posicionesId);
        $posicionesUpdate->codigo = $posicionesCode;
        $result = $posicionesUpdate->save();	

    } catch(\Illuminate\Database\QueryException $ex){ 
        DB::rollBack();
        return redirect('dist/colaboradores/nuevo')->withErrors('ERROR AL GUARDAR STORE CEBECECO CODE-0002'.$ex);
    }
    
    if($result != 1){
        DB::rollBack();
        return redirect('dist/colaboradores/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0003");
    }
    DB::commit();

    return redirect('dist/colaboradores')->with('alertSuccess', 'STORE CEBECECO HA SIDO INGRESADA');

}

public function Editar($colaboradoresId){
    /*if(!$this->common->usuariopermiso('004')){
        return redirect('dist/dashboard')->withErrors($this->common->message);
    }*/

    $colaboradores = DB::table('colaboradores')
     ->where('colaboradores.id', '=', $colaboradoresId)
     //->where('rubro.distribuidorId', Auth::user()->distribuidorId)
     ->leftjoin('departamento', 'departamento.id', '=', 'colaboradores.departamentoId')
     ->select('colaboradores.*', 'departamento.nombre as departamento')
     ->first();

    if(empty($colaboradores)){
        return redirect('dist/colaboradores')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0004");
    }

    //return $colaboradores;

    /*$departamento = DB::table('departamento')
    ->where('estatus', '=', 'Activo')
    ->where('organizacionId', '=', '1')
    ->select('id', 'nombre', 'codigo')
    ->get();

    if(empty($departamento)){
        return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
    }	
*/
    view()->share('usuario', $colaboradores);	

    //view()->share('colaboradores', $colaboradores);

    $departamento = DB::table('departamento')
    ->where('estatus', '=', 'Activo')
    ->where('organizacionId', '=', '1')
    ->select('id', 'nombre', 'codigo')
    ->get();

    if(empty($departamento)){
        return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
    }	
    view()->share('departamento', $departamento);	

    

    $provincia = DB::table('provincia')
    ->where('estatus', '=', 'Activo')
    //->where('paisId', '=', '124')
    ->select('id', 'nombre', 'codigo')
    ->get();

    if(empty($provincia)){
        return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
    }	
    view()->share('provincia', $provincia);

    //return \View::make('dist/colaboradores/editar');
    return \View::make('dist/colaboradores/editar');
}

public function PostEditar(){
    /*if(!$this->common->usuariopermiso('004')){
        return redirect('dist/dashboard')->withErrors($this->common->message);
    }*/

    $request = $this->request->all();

    //return $request;

    $usuarioId = isset($this->request->usuarioId) ? $this->request->usuarioId: '';

    //return $usuarioId;

    // $usuarioId = Colaboradores::where('id', $usuarioId)
    // //->where('distribuidorId',Auth::user()->distribuidorId)
    // ->first();

    // if(empty($usuarioId)){
    //     return redirect('dist/posiciones')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0005");
    // }

    DB::beginTransaction();


    $colaborador = Colaboradores::find($usuarioId);
        if (!$colaborador) {
            //return response()->json(['error' => 'El usuario no existe'], 404);
            return redirect('dist/colaboradores/editar/'.$usuarioId)->withErrors("ERROR AL EDITAR ELEMENTOS DE STORE CEBECECO CODE-0006");
        }

     

         $posicionesUpdate = Colaboradores::find($usuarioId);
         $posicionesUpdate->nombre           = $this->request->nombre;
         $posicionesUpdate->apellido         = $this->request->apellido;
         $posicionesUpdate->cedula           = $this->request->cedula;
        
         $posicionesUpdate->correo           = $this->request->correo;
         $posicionesUpdate->telefono         = $this->request->telefono;
         $posicionesUpdate->genero           = $this->request->genero;
         $posicionesUpdate->tipoSangre       = $this->request->tipoSangre;
         $posicionesUpdate->tipoUsuario      = $this->request->tipoUsuario;
         $posicionesUpdate->cubico           = $this->request->cubiculo;

        // //$posicionesUpdate->departamentoId   = $this->request->departamento;
         $posicionesUpdate->infoextra        = $this->request->comentario;
        
         $result = $posicionesUpdate->save();

        $newPassword = Hash::make($this->request->contrasena);
        $newRememberToken = Str::random(60);

       

        // Realizar la actualización en la base de datos
        User::where('id', $usuarioId)->update([
        'password' => $newPassword,
        'remember_token' => $newRememberToken
        ]);

        // 'password' => Hash::make($request->password),
        // 'remember_token' => Str::random(60),

    if($result != 1){
        DB::rollBack();

        return redirect('dist/colaboradores/editar/'.$usuarioId)->withErrors("ERROR AL EDITAR ELEMENTOS DE STORE CEBECECO CODE-0006");
    }

    DB::commit();

    return redirect('dist/colaboradores/')->with('alertSuccess', 'STORE CEBECECO HA SIDO EDITADO');
}

public function Mostrar($colaboradoresId){
    /*if(!$this->common->usuariopermiso('004')){
        return redirect('dist/dashboard')->withErrors($this->common->message);
    }*/

    $colaboradores = DB::table('colaboradores')
     ->where('colaboradores.id', '=', $colaboradoresId)
     ->leftjoin('departamento', 'departamento.id', '=', 'colaboradores.departamentoId')
     ->leftjoin('posiciones', 'posiciones.id', '=', 'colaboradores.posicionId')
     ->select('colaboradores.*', 'departamento.nombre as departamento', 'posiciones.nombre as posicion')
     ->first();

    if(empty($colaboradores)){
        return redirect('dist/colaboradores')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0007");
    }

     view()->share('colaboradores', $colaboradores);

    return \View::make('dist/colaboradores/mostrar');
}
public function Desactivar(){
    /*if(!$this->common->usuariopermiso('004')){
        return response()
          ->json(['response' => false]);
    }*/
    
    $colaboradoresExiste = Colaboradores::where('id', $this->request->colaboradoresId)
                    //->where('distribuidorId', Auth::user()->distribuidorId)
                    ->first();
    if(!empty($colaboradoresExiste)){

        $estatus = 'Inactivo';
        if($colaboradoresExiste->estatus == 'Inactivo'){
            $estatus = 'Activo';	
        }

        $affectedRows = Colaboradores::where('id', '=', $this->request->colaboradoresId)
                        ->update(['estatus' => $estatus]);
        
        return response()
          ->json(['response' => TRUE]);
    }

    return response()
          ->json(['response' => false]);
}

public function postBuscadistrito(){

    $provincia = $this->request->provincia;
    
    $distrito = DB::table('distrito')
    ->where('estatus', '=', 'Activo')
    ->where('provinciaId', '=', $provincia)
    ->select('id', 'nombre', 'codigo')
    ->get();

    foreach ($distrito as $key => $value) {
        
        $distritoid = $value->id;
        $distritonombre = $value->nombre;
        $distritocodigo = $value->codigo;

        $data[] = array(
            "detalle"=> "<option value='".$distritoid."' >".$distritonombre."</option>"
        );		  		 
            
    }		
        $response = array(
            'response' => TRUE,
            'data' => $data,
        );

        return response()
        ->json($response);				
            
}

public function postBuscaposiciones(){

    $departamento = $this->request->departamento;
    
    $posiciones = DB::table('posiciones')
    ->where('estatus', '=', 'Activo')
    ->where('departamentoId', '=', $departamento)
    ->select('id', 'nombre', 'codigo')
    ->get();

    foreach ($posiciones as $key => $value) {
        
        $posicionesid = $value->id;
        $posicionesnombre = $value->nombre;
        $posicionescodigo = $value->codigo;

        $data[] = array(
            "detalle"=> "<option value='".$posicionesid."' >".$posicionesnombre."</option>"
        );		  		 
            
    }		
        $response = array(
            'response' => TRUE,
            'data' => $data,
        );

        return response()
        ->json($response);				
            
}

}
