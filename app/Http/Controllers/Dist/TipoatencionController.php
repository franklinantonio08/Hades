<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Models\TipoAtencion;

use DB;
use Excel;

class TipoatencionController extends Controller
{
    
      private $request;
      private $common;
  
      public function __construct(Request $request){
          $this->request = $request;
      }
  
      public function Index(){
  
          return \view('dist/tipoatencion/index');
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
    
        $query = DB::table('tipoatencion')
        ->leftjoin('departamento', 'departamento.id', '=', 'tipoatencion.departamentoId')
         ->select('tipoatencion.*', 'departamento.nombre as departamento')
         ->orderBy($orderBy,$order);
    
    
        if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
            $query->where(
                function ($query) use ($request) {
                    $query->orWhere('tipoatencion.descripcion', 'like', '%'.trim($request['searchInput']).'%');
                    $query->orWhere('tipoatencion.codigo', 'like', '%'.trim($request['searchInput']).'%');
                    $query->orWhere('departamento.nombre', 'like', '%'.trim($request['searchInput']).'%');
                }
             );		
        }
           
        $tipoatencion = $query->paginate($length); 
    
        $result = $tipoatencion->toArray();
        $data = array();
        foreach($result['data'] as $value){
    
            if($value->estatus == 'Activo'){
                $detalle = '<a href="/dist/tipoatencion/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                <a href="/dist/tipoatencion/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
            }else{
                $detalle = '<a href="/dist/tipoatencion/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                <a href="/dist/tipoatencion/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
            }
    
            $data[] = array(
                  "DT_RowId" => $value->id,
                  "id" => $value->id,
                  "nombre"=> $value->descripcion,
                  "codigo"=> $value->codigo,
                  "departamento"=> $value->departamento,
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
    
    
        return \View::make('dist/tipoatencion/nuevo');
    }
    
    public function postNuevo(){
        
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('dist/dashboard')->withErrors($this->common->message);
        }*/
    
        //return $this->request->all();
    
        $tipoatencionExiste = TipoAtencion::where('descripcion', $this->request->nombre)
        //->where('distribuidorId', Auth::user()->distribuidorId)
        ->first();
        if(!empty($tipoatencionExiste)){
            return redirect('dist/tipoatencion/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0001");
        }
    
        DB::beginTransaction();
        try { 	
            $tipoatencion = new TipoAtencion;
            $tipoatencion->descripcion         = trim($this->request->nombre);
    
            if(isset($this->request->comentario)){
                $tipoatencion->infoextra       = trim($this->request->comentario); 
            }
            
            $tipoatencion->estatus          = 'Activo';
            $tipoatencion->prioridad        = trim($this->request->prioridad); 
            $tipoatencion->created_at       = date('Y-m-d H:i:s');
            $tipoatencion->usuarioId        = Auth::user()->id;
            $tipoatencion->departamentoId   = trim($this->request->departamento);
            $result = $tipoatencion->save();
    
            $tipoatencionId = $tipoatencion->id;
    
            if(empty($tipoatencionId)){
                DB::rollBack();
                return redirect('dist/tipoatencion/nuevo')->withErrors("ERROR AL GUARDAR EL CONTRATO NO SE GENERO UN # DE CONTRATO CORRECTO CODE-0196");
            }
            
            $tipoatencionCode = str_pad($tipoatencionId,5, "0",STR_PAD_LEFT);
    
            $tipoatencionUpdate = TipoAtencion::find($tipoatencionId);
            $tipoatencionUpdate->codigo = $tipoatencionCode;
            $result = $tipoatencionUpdate->save();	
    
        } catch(\Illuminate\Database\QueryException $ex){ 
            DB::rollBack();
            return redirect('dist/tipoatencion/nuevo')->withErrors('ERROR AL GUARDAR STORE CEBECECO CODE-0002'.$ex);
        }
        
        if($result != 1){
            DB::rollBack();
            return redirect('dist/tipoatencion/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0003");
        }
        DB::commit();
    
        return redirect('dist/tipoatencion')->with('alertSuccess', 'STORE CEBECECO HA SIDO INGRESADA');
    }
    
    public function Editar($tipoatencionId){
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('dist/dashboard')->withErrors($this->common->message);
        }*/
    
        $tipoatencion = DB::table('tipoAtencion')
         ->where('tipoAtencion.id', '=', $tipoatencionId)
         //->where('rubro.distribuidorId', Auth::user()->distribuidorId)
         ->leftjoin('departamento', 'departamento.id', '=', 'tipoAtencion.departamentoId')
         ->select('tipoAtencion.*', 'departamento.nombre as departamento')->first();
    
        if(empty($tipoatencion)){
            return redirect('dist/tipoatencion')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0004");
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
    
        view()->share('tipoatencion', $tipoatencion);
    
        return \View::make('dist/tipoatencion/editar');
    }
    
    public function PostEditar(){
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('dist/dashboard')->withErrors($this->common->message);
        }*/
    
        $request = $this->request->all();
    
        //return $request;
    
        $tipoatencionId = isset($this->request->tipoatencionId) ? $this->request->tipoatencionId: '';
    
    
        $tipoatencion = TipoAtencion::where('id', $tipoatencionId)
        //->where('distribuidorId',Auth::user()->distribuidorId)
        ->first();
    
        if(empty($tipoatencion)){
            return redirect('dist/tipoatencion')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0005");
        }
    
        //return $tipoatencion;

        
        DB::beginTransaction();
            $tipoatencionUpdate = TipoAtencion::find($tipoatencionId);
            $tipoatencionUpdate->descripcion        = $this->request->nombre;
            $tipoatencionUpdate->departamentoId     = $this->request->departamento;
            $tipoatencionUpdate->prioridad          = $this->request->prioridad;

            if(isset($this->request->comentario)){
                $tipoatencionUpdate->infoextra       = trim($this->request->comentario); 
            }
            $result = $tipoatencionUpdate->save();
    
        if($result != 1){
            DB::rollBack();
    
            return redirect('dist/tipoatencion/editar/'.$tipoatencionId)->withErrors("ERROR AL EDITAR ELEMENTOS DE STORE CEBECECO CODE-0006");
        }
    
        DB::commit();
    
        return redirect('dist/tipoatencion/')->with('alertSuccess', 'STORE CEBECECO HA SIDO EDITADO');
    }
    
    public function Mostrar($tipoatencionId){
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('dist/dashboard')->withErrors($this->common->message);
        }*/
    
        $tipoatencion = DB::table('tipoatencion')
         ->where('tipoatencion.id', '=', $tipoatencionId)
         ->leftjoin('departamento', 'departamento.id', '=', 'tipoatencion.departamentoId')
         ->select('tipoatencion.*', 'departamento.nombre as departamento')->first();
    
        if(empty($tipoatencion)){
            return redirect('dist/tipoatencion')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0007");
        }
    
         view()->share('tipoatencion', $tipoatencion);
    
        return \View::make('dist/tipoatencion/mostrar');
    }
    public function Desactivar(){
        /*if(!$this->common->usuariopermiso('004')){
            return response()
              ->json(['response' => false]);
        }*/
        
        $tipoatencionExiste = Posiciones::where('id', $this->request->tipoatencionId)
                        //->where('distribuidorId', Auth::user()->distribuidorId)
                        ->first();
        if(!empty($tipoatencionExiste)){
    
            $estatus = 'Inactivo';
            if($tipoatencionExiste->estatus == 'Inactivo'){
                $estatus = 'Activo';	
            }
    
            $affectedRows = Posiciones::where('id', '=', $this->request->tipoatencionId)
                            ->update(['estatus' => $estatus]);
            
            return response()
              ->json(['response' => TRUE]);
        }
    
        return response()
              ->json(['response' => false]);
    }

}
