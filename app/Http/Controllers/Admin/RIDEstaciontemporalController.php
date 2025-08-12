<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use App\Models\RIDEstaciontemporal;

use DB;
use Excel;

class RIDEstaciontemporalController extends Controller
{
    //

    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function Index(){

        return \view('admin/RIDestaciontemporal/index');
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
        
                $query = DB::table('rid_estaciontemporal')
                 ->select('rid_estaciontemporal.*')
                 ->orderBy($orderBy,$order);
        
        
                if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
                    $query->where(
                        function ($query) use ($request) {
                            $query->orWhere('rid_estaciontemporal.descripcion', 'like', '%'.trim($request['searchInput']).'%');
                            $query->orWhere('rid_estaciontemporal.codigo', 'like', '%'.trim($request['searchInput']).'%');
                        }
                     );		
                }
                   
                $pais = $query->paginate($length); 
            
                $result = $pais->toArray();
                $data = array();
                foreach($result['data'] as $value){
        
                    if($value->estatus == 'Activo'){
                        $detalle = '<a href="/admin/RIDestaciontemporal/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/admin/RIDestaciontemporal/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <!--a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a-->';
                    }else{
                        $detalle = '<a href="/admin/RIDestaciontemporal/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/admin/RIDestaciontemporal/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
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
                
                return \View::make('admin/RIDestaciontemporal/nuevo');
            }
        
            public function postNuevo(){
        
                
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
        
                //return $this->request->all();
        
                $puntoControlExiste = RIDEstaciontemporal::where('descripcion', $this->request->descripcion)
                //->where('distribuidorId', Auth::user()->distribuidorId)
                ->first();
        
                if(!empty($puntoControlExiste)){
                    return redirect('admin/RIDestaciontemporal/nuevo')->withErrors("ERROR AL GUARDAR PUESTO DE CONTROL CODE-0001");
                }
        
                DB::beginTransaction();
                try { 	
                    $puntoControl = new RIDEstaciontemporal;
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
                        return redirect('admin/RIDestaciontemporal/nuevo')->withErrors("ERROR AL GUARDAR PUESTO DE CONTROL CODE-0002");
                    }
                    
                    $puntoControlCode = str_pad($puntoControlId,5, "0",STR_PAD_LEFT);
                    //return $departamentoCode;
                    $puntoControlUpdate = RIDEstaciontemporal::find($puntoControlId);
                    $puntoControlUpdate->codigo = $puntoControlCode;
                    $result = $puntoControlUpdate->save();	
        
                } catch(\Illuminate\Database\QueryException $ex){ 
                    DB::rollBack();
                    return redirect('admin/RIDestaciontemporal/nuevo')->withErrors('ERROR AL GUARDAR PUESTO DE CONTROL CODE-0003'.$ex);
                }
                
                if($result != 1){
                    DB::rollBack();
                    return redirect('admin/RIDestaciontemporal/nuevo')->withErrors("ERROR AL GUARDAR PUESTO DE CONTROL CODE-0004");
                }
                DB::commit();
        
                return redirect('admin/RIDestaciontemporal')->with('alertSuccess', 'EL PUESTO DE CONTROL HA SIDO INGRESADO');
            }

            public function Editar($estaciontemporalId){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('admin/dashboard')->withErrors($this->common->message);
                }*/
        
                $RID_estaciontemporal = DB::table('rid_estaciontemporal')
                 ->where('rid_estaciontemporal.id', '=', $estaciontemporalId)
                 //->where('rubro.distribuidorId', Auth::user()->distribuidorId)
                 ->select('rid_estaciontemporal.*')->first();
        
                if(empty($RID_estaciontemporal)){
                    return redirect('admin/RIDestaciontemporal')->withErrors("ERROR PUESTO DE CONTROL NO EXISTE CODE-0005");
                }
        
                view()->share('RIDestaciontemporal', $RID_estaciontemporal);
                return \View::make('admin/RIDestaciontemporal/editar');
            }
        
            public function PostEditar(){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('admin/dashboard')->withErrors($this->common->message);
                }*/
        
                $request = $this->request->all();
        
                //return $request;
        
                $estaciontemporalId = isset($this->request->estaciontemporalId) ? $this->request->estaciontemporalId: '';
        
                //return $departamentoId;
        
                $estaciontemporal = RIDEstaciontemporal::where('id', $estaciontemporalId)
                //->where('distribuidorId',Auth::user()->distribuidorId)
                ->first();
        
                if(empty($estaciontemporal)){
                    return redirect('admin/RIDestaciontemporal')->withErrors("ERROR PUESTO DE CONTROL NO EXISTE CODE-0006");
                }
        
                DB::beginTransaction();
                    $estaciontemporalUpdate = RIDEstaciontemporal::find($estaciontemporalId);
                    $estaciontemporalUpdate->descripcion        = $this->request->descripcion;
                    $estaciontemporalUpdate->infoextra          = $this->request->comentario;
                    
                    $result = $estaciontemporalUpdate->save();
        
                if($result != 1){
                    DB::rollBack();
        
                    return redirect('admin/RIDestaciontemporal/editar/'.$estaciontemporalId)->withErrors("ERROR AL EDITAR ELEMENTOS DEL PUESTO DE CONTROL CODE-0007");
                }
        
                DB::commit();
        
                return redirect('admin/RIDestaciontemporal/')->with('alertSuccess', 'EL PUESTO DE CONTROL HA SIDO EDITADO');
            }
        
            public function Mostrar($estaciontemporalId){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('admin/dashboard')->withErrors($this->common->message);
                }*/
        
                $RID_estacionTemporal = DB::table('rid_estaciontemporal')
                 ->where('rid_estaciontemporal.id', '=', $estaciontemporalId)
                 ->select('rid_estaciontemporal.*')->first();
        
                if(empty($RID_estacionTemporal)){
                    return redirect('admin/RIDestaciontemporal')->withErrors("ERROR EL PUESTO DE CONTROL NO EXISTE CODE-0008");
                }
        
                //return $compania;
        
                 view()->share('RIDestaciontemporal', $RID_estacionTemporal);
        
                return \View::make('admin/RIDestaciontemporal/mostrar');
            }
}
