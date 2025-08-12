<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use App\Models\Submotivo;

use DB;
use Excel;

class SubmotivoController extends Controller
{
    private $request;
    private $common;
  
    public function __construct(Request $request){
        $this->request = $request;
    }
  
    public function Index(){

        return view('dist.submotivo.index');

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
    
        $query = DB::table('submotivo')
        ->leftjoin('departamento', 'departamento.id', '=', 'submotivo.departamentoId')
        ->leftjoin('motivo', 'motivo.id', '=', 'submotivo.motivoId')
         ->select('submotivo.*', 'motivo.descripcion as motivo', 'departamento.nombre as departamento')
         ->orderBy($orderBy,$order);
    
    
        if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
            $query->where(
                function ($query) use ($request) {
                    $query->orWhere('submotivo.descripcion', 'like', '%'.trim($request['searchInput']).'%');
                    $query->orWhere('submotivo.codigo', 'like', '%'.trim($request['searchInput']).'%');
                    $query->orWhere('motivo.descripcion', 'like', '%'.trim($request['searchInput']).'%');
                    $query->orWhere('departamento.nombre', 'like', '%'.trim($request['searchInput']).'%');
                }
             );		
        }
           
        $motivo = $query->paginate($length); 
    
        $result = $motivo->toArray();
        $data = array();
        foreach($result['data'] as $value){
    
            if($value->estatus == 'Activo'){
                $detalle = '<a href="/dist/motivo/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                <a href="/dist/motivo/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
            }else{
                $detalle = '<a href="/dist/motivo/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                <a href="/dist/motivo/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
            }
    
            $data[] = array(
                  "DT_RowId" => $value->id,
                  "id" => $value->id,
                  "nombre"=> $value->descripcion,
                  "codigo"=> $value->codigo,
                  "departamento"=> $value->departamento,
                  "motivo"=> $value->motivo,
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
            $motivo = DB::table('motivo')
            ->where('estatus', '=', 'Activo')
            //->where('organizacionId', '=', '1')
            ->select('id', 'descripcion', 'codigo')
            ->get();
    
            if(empty($motivo)){
                return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
            }	
            view()->share('motivo', $motivo);	
    
    
        return \View::make('dist/submotivo/nuevo');
    }

    public function postNuevo(){
        
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('dist/dashboard')->withErrors($this->common->message);
        }*/
    
        //return $this->request->all();
    
        $motivoExiste = SubMotivo::where('descripcion', $this->request->nombre)
        //->where('distribuidorId', Auth::user()->distribuidorId)
        ->first();
        if(!empty($motivoExiste)){
            return redirect('dist/submotivo/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0001");
        }
    
        DB::beginTransaction();
        try { 	
            $submotivo = new Submotivo;
            $submotivo->descripcion         = trim($this->request->nombre);
    
            if(isset($this->request->comentario)){
                $submotivo->infoextra       = trim($this->request->comentario); 
            }
            
            $submotivo->estatus          = 'Activo';
            //$motivo->prioridad        = trim($this->request->prioridad); 
            $submotivo->created_at       = date('Y-m-d H:i:s');
            $submotivo->usuarioId        = Auth::user()->id;
            $submotivo->departamentoId   = trim($this->request->departamento);
            $result = $submotivo->save();
    
            $submotivoId = $submotivo->id;
    
            if(empty($submotivoId)){
                DB::rollBack();
                return redirect('dist/submotivo/nuevo')->withErrors("ERROR AL GUARDAR EL CONTRATO NO SE GENERO UN # DE CONTRATO CORRECTO CODE-0196");
            }
            
            $submotivoCode = str_pad($submotivoId,5, "0",STR_PAD_LEFT);
    
            $submotivoUpdate = Submotivo::find($submotivoId);
            $submotivoUpdate->codigo = $submotivoCode;
            $result = $submotivoUpdate->save();	
    
        } catch(\Illuminate\Database\QueryException $ex){ 
            DB::rollBack();
            return redirect('dist/submotivo/nuevo')->withErrors('ERROR AL GUARDAR STORE CEBECECO CODE-0002'.$ex);
        }
        
        if($result != 1){
            DB::rollBack();
            return redirect('dist/submotivo/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0003");
        }
        DB::commit();
    
        return redirect('dist/submotivo')->with('alertSuccess', 'STORE CEBECECO HA SIDO INGRESADA');
    }
    
    public function Editar($submotivoId){
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('dist/dashboard')->withErrors($this->common->message);
        }*/
    
        $motivo = DB::table('motivo')
         ->where('motivo.id', '=', $motivoId)
         //->where('rubro.distribuidorId', Auth::user()->distribuidorId)
         ->leftjoin('departamento', 'departamento.id', '=', 'motivo.departamentoId')
         ->select('motivo.*', 'departamento.nombre as departamento')->first();
    
        if(empty($motivo)){
            return redirect('dist/motivo')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0004");
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
    
        view()->share('motivo', $motivo);
    
        return \View::make('dist/motivo/editar');
    }
    
    public function PostEditar(){
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('dist/dashboard')->withErrors($this->common->message);
        }*/
    
        $request = $this->request->all();
    
        //return $request;
    
        $motivoId = isset($this->request->motivoId) ? $this->request->motivoId: '';
    
    
        $motivo = Submotivo::where('id', $motivoId)
        //->where('distribuidorId',Auth::user()->distribuidorId)
        ->first();
    
        if(empty($motivo)){
            return redirect('dist/motivo')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0005");
        }
    
        //return $motivo;

        
        DB::beginTransaction();
            $motivoUpdate = Submotivo::find($motivoId);
            $motivoUpdate->descripcion        = $this->request->nombre;
            $motivoUpdate->departamentoId     = $this->request->departamento;
            $motivoUpdate->estatus          = $this->request->estatus;

            if(isset($this->request->comentario)){
                $motivoUpdate->infoextra       = trim($this->request->comentario); 
            }
            $result = $motivoUpdate->save();


            //return 1;
    
        if($result != 1){
            DB::rollBack();
    
            return redirect('dist/motivo/editar/'.$motivoId)->withErrors("ERROR AL EDITAR ELEMENTOS DE STORE CEBECECO CODE-0006");
        }
    
        DB::commit();
    
        return redirect('dist/motivo/')->with('alertSuccess', 'STORE CEBECECO HA SIDO EDITADO');
    }
    
    public function Mostrar($motivoId){
        /*if(!$this->common->usuariopermiso('004')){
            return redirect('dist/dashboard')->withErrors($this->common->message);
        }*/
    
        $motivo = DB::table('motivo')
         ->where('motivo.id', '=', $motivoId)
         ->leftjoin('departamento', 'departamento.id', '=', 'motivo.departamentoId')
         ->select('motivo.*', 'departamento.nombre as departamento')->first();
    
        if(empty($motivo)){
            return redirect('dist/motivo')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0007");
        }
    
         view()->share('motivo', $motivo);
    
        return \View::make('dist/motivo/mostrar');
    }
    public function Desactivar(){
        /*if(!$this->common->usuariopermiso('004')){
            return response()
              ->json(['response' => false]);
        }*/
        
        $motivoExiste = Posiciones::where('id', $this->request->motivoId)
                        //->where('distribuidorId', Auth::user()->distribuidorId)
                        ->first();
        if(!empty($motivoExiste)){
    
            $estatus = 'Inactivo';
            if($motivoExiste->estatus == 'Inactivo'){
                $estatus = 'Activo';	
            }
    
            $affectedRows = Posiciones::where('id', '=', $this->request->motivoId)
                            ->update(['estatus' => $estatus]);
            
            return response()
              ->json(['response' => TRUE]);
        }
    
        return response()
              ->json(['response' => false]);
    }

}
