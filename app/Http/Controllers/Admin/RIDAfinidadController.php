<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use App\Models\RIDAfinidad;

use App\Helpers\CommonHelper;

use DB;
use Excel;

class RIDAfinidadController extends Controller
{    
    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
        $this->common = New CommonHelper();
    }

    public function Index(){

        return view('admin.RIDafinidad.index');
        
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
        
                $query = DB::table('RID_afinidad')
                 ->select('RID_afinidad.*')
                 ->orderBy($orderBy,$order);
        
        
                if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
                    $query->where(
                        function ($query) use ($request) {
                            $query->orWhere('RID_afinidad.descripcion', 'like', '%'.trim($request['searchInput']).'%');
                            $query->orWhere('RID_afinidad.codigo', 'like', '%'.trim($request['searchInput']).'%');
                        }
                     );		
                }
                   
                $afinidad = $query->paginate($length); 
            
                $result = $afinidad->toArray();
                $data = array();
                foreach($result['data'] as $value){
        
                    if($value->estatus == 'Activo'){
                        $detalle = '<a href="/admin/RIDafinidad/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/admin/RIDafinidad/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <!-- a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a-->';
                    }else{
                        $detalle = '<a href="/admin/RIDafinidad/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/admin/RIDafinidad/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
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

                if(!$this->common->usuariopermiso('003')){
                    return redirect('admin/RIDafinidad')->withErrors($this->common->message);
                }
                
                return view('admin.RIDafinidad.nuevo');
            }
        
            public function postNuevo(){
        
                
                
        
                //return $this->request->all();
                $descripcion = trim($this->request->descripcion);  

                $RIDAfinidadExiste = RIDAfinidad::where('descripcion', $descripcion)
                //->where('distribuidorId', Auth::user()->distribuidorId)
                ->first();                            
        
                if(!empty($RIDAfinidadExiste)){
                    return redirect('admin/RIDafinidad/nuevo')->withErrors("ERROR AL GUARDAR AFINIDAD CODE-0009");
                }
        
                DB::beginTransaction();
                try { 	
                    $RIDAfinidad = new RIDAfinidad;
                    $RIDAfinidad->descripcion      = trim($descripcion);
                    $RIDAfinidad->tipo_afinidad    = trim($this->request->tipoAfinidad);
                    $RIDAfinidad->codigo           = strtoupper(substr($descripcion, 0, 3));
        
                    if(isset($this->request->comentario)){
                        $RIDAfinidad->infoextra       = trim($this->request->comentario); 
                    }
                    
                    $RIDAfinidad->estatus          = 'Activo';
                    $RIDAfinidad->created_at       = date('Y-m-d H:i:s');
                    $RIDAfinidad->usuarioId        = Auth::user()->id;
                    $result = $RIDAfinidad->save();
        
                    $RIDAfinidadId = $RIDAfinidad->id;
        
                    if(empty($RIDAfinidadId)){
                        DB::rollBack();
                        return redirect('admin/RIDafinidad/nuevo')->withErrors("ERROR AL GUARDAR AFINIDAD NO SE GENERO CORRECTAMENTE CODE-0010");
                    }
                    
                    // $RIDAfinidadCode = str_pad($RIDAfinidadId,5, "0",STR_PAD_LEFT);
                    // //return $departamentoCode;
                    // $RIDAfinidadUpdate = RIDAfinidad::find($RIDAfinidadId);
                    // $RIDAfinidadUpdate->codigo = $RIDAfinidadCode;
                    // $result = $RIDAfinidadUpdate->save();	
        
                } catch(\Illuminate\Database\QueryException $ex){ 
                    DB::rollBack();
                    return redirect('admin/RIDafinidad/nuevo')->withErrors('ERROR AL GUARDAR AFINIDAD CODE-0011'.$ex);
                }
                
                if($result != 1){
                    DB::rollBack();
                    return redirect('admin/RIDafinidad/nuevo')->withErrors("ERROR AL GUARDAR AFINIDAD CODE-0012");
                }
                DB::commit();
        
                return redirect('admin/RIDafinidad')->with('alertSuccess', 'LA AFINIDAD HA SIDO INGRESADA');
            }

            public function Editar($afinidadId){

                if(!$this->common->usuariopermiso('003')){
                    return redirect('admin/RIDafinidad')->withErrors($this->common->message);
                }
        
                $RID_afinidad = DB::table('RID_afinidad')
                 ->where('RID_afinidad.id', '=', $afinidadId)
                 //->where('rubro.distribuidorId', Auth::user()->distribuidorId)
                 ->select('RID_afinidad.*')->first();
        
                if(empty($RID_afinidad)){
                    return redirect('admin/RIDafinidad')->withErrors("ERROR AFINIDAD NO EXISTE CODE-0013");
                }
        
                view()->share('RID_afinidad', $RID_afinidad);
                return \View::make('admin/RIDafinidad/editar');
            }
        
            public function PostEditar(){

                if(!$this->common->usuariopermiso('003')){
                    return redirect('admin/RIDafinidad')->withErrors($this->common->message);
                }
        
                $request = $this->request->all();
        
                //return $request;
        
                $afinidadId = isset($this->request->afinidadId) ? $this->request->afinidadId: '';
        
                //return $departamentoId;
        
                $afinidad = RIDAfinidad::where('id', $afinidadId)
                //->where('distribuidorId',Auth::user()->distribuidorId)
                ->first();
        
                if(empty($afinidad)){
                    return redirect('admin/RIDafinidad')->withErrors("ERROR AFINIDAD NO EXISTE CODE-0014");
                }
        
                DB::beginTransaction();
                    $afinidadUpdate = RIDAfinidad::find($afinidadId);
                    $afinidadUpdate->descripcion        = $this->request->descripcion;
                    $afinidadUpdate->infoextra          = $this->request->comentario;
                    
                    $result = $afinidadUpdate->save();
        
                if($result != 1){
                    DB::rollBack();
        
                    return redirect('admin/RIDafinidad/editar/'.$afinidadId)->withErrors("ERROR AL EDITAR ELEMENTOS DE AFINIDAD CODE-0015");
                }
        
                DB::commit();
        
                return redirect('admin/RIDafinidad/')->with('alertSuccess', 'AFINIDAD HA SIDO EDITADO');
            }
        
            public function Mostrar($afinidadId){
                
                if(!$this->common->usuariopermiso('003')){
                    return redirect('admin/RIDafinidad')->withErrors($this->common->message);
                }
        
                $RID_afinidad = DB::table('RID_afinidad')
                 ->where('RID_afinidad.id', '=', $afinidadId)
                 ->select('RID_afinidad.*')->first();
        
                if(empty($RID_afinidad)){
                    return redirect('admin/RIDafinidad')->withErrors("ERROR AFINIDAD NO EXISTE CODE-0016");
                }
        
                //return $compania;
        
                 view()->share('RIDafinidad', $RID_afinidad);
        
                return \View::make('admin/RIDafinidad/mostrar');
            }

}
