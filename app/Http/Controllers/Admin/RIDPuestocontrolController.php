<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use App\Models\RIDPuestocontrol;

use DB;
use Excel;

class RIDPuestocontrolController extends Controller
{
    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function Index(){

        return \view('admin/RIDpuestocontrol/index');
    }

    public function PostIndex(){
      
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
        
                $query = DB::table('RID_puestoControl')
                 ->select('RID_puestoControl.*')
                 ->orderBy($orderBy,$order);
        
        
                if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
                    $query->where(
                        function ($query) use ($request) {
                            $query->orWhere('RID_puestoControl.descripcion', 'like', '%'.trim($request['searchInput']).'%');
                            $query->orWhere('RID_puestoControl.codigo', 'like', '%'.trim($request['searchInput']).'%');
                        }
                     );		
                }
                   
                $pais = $query->paginate($length); 
            
                $result = $pais->toArray();
                $data = array();
                foreach($result['data'] as $value){
        
                    if($value->estatus == 'Activo'){
                        $detalle = '<a href="/admin/RIDpuestocontrol/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/admin/RIDpuestocontrol/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <!--a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a-->';
                    }else{
                        $detalle = '<a href="/admin/RIDpuestocontrol/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/admin/RIDpuestocontrol/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <!--a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a-->';
                    }
        
                    $data[] = array(
                          "DT_RowId" => $value->id,
                          "id" => $value->id,
                          "nombre"=> $value->descripcion,
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
                
                return \View::make('admin/RIDpuestocontrol/nuevo');
            }
        
            public function postNuevo(){
        
                
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
        
                //return $this->request->all();
        
                $puntoControlExiste = RIDPuestocontrol::where('descripcion', $this->request->descripcion)
                //->where('distribuidorId', Auth::user()->distribuidorId)
                ->first();
        
                if(!empty($puntoControlExiste)){
                    return redirect('admin/RIDpuestocontrol/nuevo')->withErrors("ERROR AL GUARDAR PUESTO DE CONTROL CODE-0001");
                }
        
                DB::beginTransaction();
                try { 	
                    $puntoControl = new RIDPuestocontrol;
                    $puntoControl->descripcion         = trim($this->request->descripcion);
        
                    if(isset($this->request->comentario)){
                        $puntoControl->infoextra       = trim($this->request->comentario); 
                    }
                    
                    $puntoControl->estatus          = 'Activo';
                    $puntoControl->created_at       = date('Y-m-d H:i:s');
                    $puntoControl->usuarioId        = Auth::user()->id;
                    $result = $puntoControl->save();
        
                    $puntoControlId = $puntoControl->id;
        
                    if(empty($puntoControlId)){
                        DB::rollBack();
                        return redirect('admin/RIDpuestocontrol/nuevo')->withErrors("ERROR AL GUARDAR PUESTO DE CONTROL CODE-0002");
                    }
                    
                    $puntoControlCode = str_pad($puntoControlId,5, "0",STR_PAD_LEFT);
                    //return $departamentoCode;
                    $puntoControlUpdate = RIDPuestocontrol::find($puntoControlId);
                    $puntoControlUpdate->codigo = $puntoControlCode;
                    $result = $puntoControlUpdate->save();	
        
                } catch(\Illuminate\Database\QueryException $ex){ 
                    DB::rollBack();
                    return redirect('admin/RIDpuestocontrol/nuevo')->withErrors('ERROR AL GUARDAR PUESTO DE CONTROL CODE-0003'.$ex);
                }
                
                if($result != 1){
                    DB::rollBack();
                    return redirect('admin/RIDpuestocontrol/nuevo')->withErrors("ERROR AL GUARDAR PUESTO DE CONTROL CODE-0004");
                }
                DB::commit();
        
                return redirect('admin/RIDpuestocontrol')->with('alertSuccess', 'EL PUESTO DE CONTROL HA SIDO INGRESADO');
            }

            public function Editar($puntocontrolId){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('admin/dashboard')->withErrors($this->common->message);
                }*/
        
                $RID_puestoControl = DB::table('RID_puestoControl')
                 ->where('RID_puestoControl.id', '=', $puntocontrolId)
                 //->where('rubro.distribuidorId', Auth::user()->distribuidorId)
                 ->select('RID_puestoControl.*')->first();
        
                if(empty($RID_puestoControl)){
                    return redirect('admin/RIDpuestocontrol')->withErrors("ERROR PUESTO DE CONTROL NO EXISTE CODE-0005");
                }
        
                view()->share('RIDPuestoControl', $RID_puestoControl);
                return \View::make('admin/RIDpuestocontrol/editar');
            }
        
            public function PostEditar(){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('admin/dashboard')->withErrors($this->common->message);
                }*/
        
                $request = $this->request->all();
        
                //return $request;
        
                $puntocontrolId = isset($this->request->puntocontrolId) ? $this->request->puntocontrolId: '';
        
                //return $departamentoId;
        
                $puntocontrol = RIDPuestocontrol::where('id', $puntocontrolId)
                //->where('distribuidorId',Auth::user()->distribuidorId)
                ->first();
        
                if(empty($puntocontrol)){
                    return redirect('admin/RIDpuestocontrol')->withErrors("ERROR PUESTO DE CONTROL NO EXISTE CODE-0006");
                }
        
                DB::beginTransaction();
                    $afinidadUpdate = RIDPuestocontrol::find($puntocontrolId);
                    $afinidadUpdate->descripcion        = $this->request->descripcion;
                    $afinidadUpdate->infoextra          = $this->request->comentario;
                    
                    $result = $afinidadUpdate->save();
        
                if($result != 1){
                    DB::rollBack();
        
                    return redirect('admin/RIDpuestocontrol/editar/'.$puntocontrolId)->withErrors("ERROR AL EDITAR ELEMENTOS DEL PUESTO DE CONTROL CODE-0007");
                }
        
                DB::commit();
        
                return redirect('admin/RIDpuestocontrol/')->with('alertSuccess', 'EL PUESTO DE CONTROL HA SIDO EDITADO');
            }
        
            public function Mostrar($puntocontrolId){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('admin/dashboard')->withErrors($this->common->message);
                }*/
        
                $RID_puestoControl = DB::table('RID_puestoControl')
                 ->where('RID_puestoControl.id', '=', $puntocontrolId)
                 ->select('RID_puestoControl.*')->first();
        
                if(empty($RID_puestoControl)){
                    return redirect('admin/RIDpuestocontrol')->withErrors("ERROR EL PUESTO DE CONTROL NO EXISTE CODE-0008");
                }
        
                //return $compania;
        
                 view()->share('RIDPuestoControl', $RID_puestoControl);
        
                return \View::make('admin/RIDpuestocontrol/mostrar');
            }

}
