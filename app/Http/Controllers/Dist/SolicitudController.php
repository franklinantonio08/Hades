<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use App\Models\Solicitud;
use App\Models\Colaboradores;
use App\Models\Cubiculo;
use App\Models\Motivo;
use App\Models\Submotivo;
use App\Models\Consumidor;
use App\Models\Departamento;

use DB;
use Excel;

class SolicitudController extends Controller
{
    //



    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function Index(){
        
     

        return \view('dist/solicitud/index');

    }

    public function PostIndex(){

                //return $colaboradoresId;
        
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
        
                $query = DB::table('solicitud')
                ->leftjoin('departamento', 'departamento.id', '=', 'solicitud.departamentoId')
                ->leftjoin('tipoAtencion', 'tipoAtencion.id', '=', 'solicitud.idTipoAtencion');

                // Agrega la variable de usuario para simular ROW_NUMBER()
                $query->select([
                        DB::raw('@row_num := @row_num + 1 AS row_number'),
                        'solicitud.*',
                        'departamento.nombre',
                        'tipoAtencion.descripcion'
                    ])
                    ->from(DB::raw('(SELECT @row_num := 0) AS vars, solicitud'))
                    ->orderBy($orderBy, $order);

        
                if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
                    $query->where(
                        function ($query) use ($request) {
                            $query->orWhere('solicitud.nombre', 'like', '%'.trim($request['searchInput']).'%');
                            $query->orWhere('solicitud.codigo', 'like', '%'.trim($request['searchInput']).'%');
                        }
                     );		
                }
                   
                $solicitud = $query->paginate($length); 
            
                $result = $solicitud->toArray();
                $data = array();
                foreach($result['data'] as $value){
        
                    if($value->estatus == 'Activo'){
                        $detalle = '<a href="/dist/solicitud/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/dist/solicitud/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
                    }else{
                        $detalle = '<a href="/dist/solicitud/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/dist/solicitud/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
                    }
        
                    $data[] = array(
                          "DT_RowId" => $value->row_number,
                          "id" => $value->id,
                          "TipoAtencion"=> $value->descripcion,
                          "codigo"=> $value->codigo,
                          "departamento"=> $value->nombre,
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

            public function Missolicitudes(){
        
      
                return \view('dist/solicitud/missolicitudes');
        
            }

            public function PostMissolicitudes($colaboradoresId){

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
        
                /*$query = DB::table('solicitud')
                //->where('solicitud.funcionarioId', '=', $colaboradoresId)
                ->leftjoin('departamento', 'departamento.id', '=', 'solicitud.departamentoId')
                ->leftjoin('tipoAtencion', 'tipoAtencion.id', '=', 'solicitud.idTipoAtencion')
                
                 ->select('solicitud.*', 'departamento.nombre', 'tipoAtencion.descripcion' )
                 ->orderBy($orderBy,$order);
        */
                $query = DB::table('solicitud')
                ->leftjoin('departamento', 'departamento.id', '=', 'solicitud.departamentoId')
                ->leftjoin('tipoAtencion', 'tipoAtencion.id', '=', 'solicitud.idTipoAtencion');

                if ($colaboradoresId <> 0) {
                $query->where('solicitud.funcionarioId', '=', $colaboradoresId);
                }

                $query->select([
                    DB::raw('@row_num := @row_num + 1 AS row_number'),
                    'solicitud.*',
                    'departamento.nombre',
                    'tipoAtencion.descripcion'
                ])
                ->from(DB::raw('(SELECT @row_num := 0) AS vars, solicitud'))
                ->orderBy($orderBy, $order);
        
                if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
                    $query->where(
                        function ($query) use ($request) {
                            $query->orWhere('solicitud.nombre', 'like', '%'.trim($request['searchInput']).'%');
                            $query->orWhere('solicitud.codigo', 'like', '%'.trim($request['searchInput']).'%');
                        }
                     );		
                }
                   
                $solicitud = $query->paginate($length); 
            
                $result = $solicitud->toArray();
                $data = array();
                foreach($result['data'] as $value){
        
                    if($value->estatus == 'Activo'){
                        $detalle = '<a href="/dist/solicitud/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/dist/solicitud/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-danger text-white m-b-5 desactivar"> <i class="bi bi-trash"></i> </a>';
                    }else{
                        $detalle = '<a href="/dist/solicitud/mostrar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-warning text-white m-b-5"> <i class="bi bi-eye"></i> </a>
                                        <a href="/dist/solicitud/editar/'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-secondary text-white m-b-5"> <i class="bi bi-pencil"></i> </a>
                                        <a href="#" attr-id="'.$value->id.'" class="btn btn-icon waves-effect waves-light bg-primary text-white m-b-5 desactivar"> <i class="bi bi-check2-square"></i> </a>';
                    }
        
                    $data[] = array(
                          "DT_RowId" => $value->row_number,
                          "id" => $value->id,
                          "TipoAtencion"=> $value->descripcion,
                          "codigo"=> $value->codigo,
                          "departamento"=> $value->nombre,
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
                

                //return 'hola';


                $departamento = DB::table('departamento')
                ->where('estatus', '=', 'Activo')
                ->where('organizacionId', '=', '1')
                ->select('id', 'nombre', 'codigo')
                ->get();

                if(empty($departamento)){
                    return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
                }	
                view()->share('departamento', $departamento);	


                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
                
                return \View::make('dist/solicitud/nuevo');
            }
        
            public function postNuevo(){
        
                
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
        
                //return $this->request->all();
        
                /*$solicitudExiste = Solicitud::where('nombre', $this->request->nombre)
                //->where('distribuidorId', Auth::user()->distribuidorId)
                ->first();
        
                if(!empty($solicitudExiste)){
                    return redirect('dist/solicitud/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0001");
                }*/

                $departamento = DB::table('departamento')
                ->where('departamento.id', '=', trim($this->request->departamento))
                ->select(DB::raw("SUBSTRING(departamento.codigo, 1, 1) as cod_depart"))
                ->first();

                $cod_depart = $departamento->cod_depart;

                //return $departamento;
        
                DB::beginTransaction();
                try { 	
                    $solicitud = new Solicitud;
                    $solicitud->IdTipoAtencion          = trim($this->request->tipoAtencion);
                    $solicitud->departamentoId          = trim($this->request->departamento);
                    if(isset($this->request->comentario)){
                    $solicitud->infoextra               = trim($this->request->comentario); 
                    }
                    
                    $solicitud->estatus                 = 'Activo';
                    $solicitud->fechaAtencion           = date('Y-m-d H:i:s');
                    $solicitud->created_at              = date('Y-m-d H:i:s');
                    $solicitud->funcionarioId           = Auth::user()->id;
                    $solicitud->usuarioId               = Auth::user()->id;
                    //$solicitud->organizacionId          = 1;
                    $result = $solicitud->save();

                    $solicitudId = $solicitud->id;
        
                    if(empty($solicitudId)){
                        DB::rollBack();
                        return redirect('dist/solicitud/nuevo')->withErrors("ERROR AL GUARDAR EL CONTRATO NO SE GENERO UN # DE CONTRATO CORRECTO CODE-0196");
                    }

                    $ultimoCodigo = Solicitud::whereNotNull('codigo')
                    ->orderBy('id', 'desc')
                    ->value('codigo');

                    $ultimoCodigoletra = substr($ultimoCodigo, 0, 1);
                    $ultimoCodigoN = intval(substr($ultimoCodigo, 1));

                    $ultimoCodigoN++;
                    
                    // $departamento = Departamento::where('nombre', 'Atención al Cliente')->first();
                    // if ($departamento) {
                    //     $cod_depart = $departamento->codigo;
                    // } 

                    //$letra = substr($ultimaSolicitudCodigo, 0, 1); // Obtener la última letra del código de solicitud
                         //$numeros = intval(substr($ultimaSolicitudCodigo, 1)); // Obtener la parte numérica del código de solicitud


                     // Verificar si se necesita cambiar de letra y reiniciar el número de solicitud
                     if ($ultimoCodigoN > 999) {
                        $ultimoCodigoN = 1; // Reiniciar el número de solicitud
                        $ultimoCodigoletra = chr(ord($ultimoCodigoletra) + 1); // Obtener la siguiente letra ASCII
                        if ($ultimoCodigoletra > 'Z') {
                            $ultimoCodigoletra = 'A'; // Volver a 'A' si llega a 'Z'
                        }
                    }

                    // Crear el nuevo código de solicitud
                    $solicitudCode = $ultimoCodigoletra . str_pad($ultimoCodigoN, 3, "0", STR_PAD_LEFT);

                    //return $solicitudCode;

                    //$solicitudCode =  $cod_depart . str_pad($ultimoCodigo,3, "0",STR_PAD_LEFT);

                    $solicitudUpdate = Solicitud::find($solicitudId);
                    $solicitudUpdate->codigo = $solicitudCode;
                    $result = $solicitudUpdate->save();	

                    //return $solicitudCode;


                    // Obtener el último código de solicitud
                    //$ultimaSolicitud = Solicitud::orderBy('id', 'desc')->first();

                    //return $ultimaSolicitud;
                    //
                    // if ($ultimaSolicitud) {

                    //     $ultimaSolicitudCodigo = $ultimaSolicitud->codigo;

                    //     return $ultimaSolicitudCodigo;

                    //   
                        
                    // } 

                    //return  $cod_depart;

                    // Incrementar el número de solicitud
                    //$numeros++;

                   

                    //  $solicitudUpdate = Solicitud::find($solicitudId);
                    //  $solicitudUpdate->codigo = $solicitudCode;
                    //  $result = $solicitudUpdate->save();	

                    //$cubiculoCount = Cubiculo::count();
                    $cubiculoCount = Cubiculo::where('estatus', 'Activo')->count();
                    //return $cubiculoCount;

                    if ($cubiculoCount < 7) {
                        //return $cubiculoCount;

                    $colaboradorSinCubiculo = Colaboradores::where('estatus', 'Activo')
                    ->whereNotIn('id', function ($query) {
                    $query
                    ->select('funcionarioId')
                    ->where('estatus', 'Activo')
                    ->from('cubiculo');
                    })
                    ->first();

                    /*$colaboradorSinCubiculo = Colaboradores::where('estatus', 'Activo')
                    ->whereNotIn('id', function ($query) use ($solicitudId) {
                        $query
                            ->select('funcionarioId')
                            ->where('estatus', 'Activo')
                            ->where('solicitudId', '<>', $solicitudId)
                            ->from('cubiculo');
                    })
                    ->first();*/

                    //return $colaboradorSinCubiculo;

                    if ($colaboradorSinCubiculo) {

                        $cubiculo = new Cubiculo;
                        $cubiculo->solicitudId      = $solicitudId;
                        $cubiculo->funcionarioId    = $colaboradorSinCubiculo->id;
                        $cubiculo->llamado          = 0;
                        $cubiculo->estatus          = 'Activo';
                        $cubiculo->codigo           = $solicitudCode;
                        $cubiculo->usuarioId        = Auth::user()->id;
                        $result = $cubiculo->save();
                
                        if ($result) {
                            Solicitud::where('id', $solicitudId)
                                ->update([
                                    'usuarioId' => $colaboradorSinCubiculo->id,
                                    'funcionarioId' => $colaboradorSinCubiculo->id
                                ]);
                        }
                

                        // Puedes realizar alguna acción adicional después de asignar el cubículo si es necesario
                    }
                    
                
                    
                }


        
                } catch(\Illuminate\Database\QueryException $ex){ 
                    DB::rollBack();
                    return redirect('dist/solicitud/nuevo')->withErrors('ERROR AL GUARDAR STORE CEBECECO CODE-0002'.$ex);
                }
                
                if($result != 1){
                    DB::rollBack();
                    return redirect('dist/solicitud/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0003");
                }
                DB::commit();
        
                return redirect('dist/solicitud')->with('alertSuccess', 'STORE CEBECECO HA SIDO INGRESADA');
            }
        
            public function Editar($solicitudId){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
        
                $solicitud = DB::table('solicitud')
                 ->where('solicitud.id', '=', $solicitudId)
                 ->leftjoin('tipoAtencion', 'tipoAtencion.id', '=', 'solicitud.IdtipoAtencion')
                ->leftjoin('departamento', 'departamento.id', '=', 'solicitud.departamentoId')
                ->leftjoin('consumidor', 'consumidor.solicitudId', '=', 'solicitud.id')
                 //->where('rubro.distribuidorId', Auth::user()->distribuidorId)
                ->select('solicitud.*',
                'departamento.id as departamentoId', 
                'departamento.nombre as departamentoNombre',
                'tipoAtencion.id as IdTipoAtencion', 
                'tipoAtencion.descripcion', 
                //'consumidor.*')
                'consumidor.cedula',
                'consumidor.nombre',
                'consumidor.apellido',
                'consumidor.fechaNacimiento',
                'consumidor.correo',
                'consumidor.genero',
                'consumidor.telefono',
                'consumidor.tipoConsumidor')
                ->first();
        

                //return $solicitud;

                if(empty($solicitud)){
                    return redirect('dist/solicitud')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0004");
                }
        
                view()->share('solicitud', $solicitud);
                return \View::make('dist/solicitud/editar');
            }
        
            public function PostEditar(){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
        
                $request = $this->request->all();
        
                //return $request;
        
                $solicitudId = isset($this->request->solicitudId) ? $this->request->solicitudId: '';

                //$cedula = isset($this->request->cedula) ? $this->request->cedula: '';
        
                //return $solicitudId;
        
                /*$solicitud = solicitud::where('id', $solicitudId)
                //->where('distribuidorId',Auth::user()->distribuidorId)
                ->first();
        
                if(empty($solicitud)){
                    return redirect('dist/solicitud')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0005");
                }*/
        
                DB::beginTransaction();

                    $solicitudUpdate = Solicitud::find($solicitudId);
                    $solicitudUpdate->estatus          = $this->request->estatus;
                    $solicitudUpdate->infoextra        = $this->request->comentario;
                    $result = $solicitudUpdate->save();

                    if($this->request->estatus == 'Resuelto'){

                        $consumidorCount = Consumidor::where('solicitudId', $solicitudId)->count();

                        //return $consumidorCount;

                        if ($consumidorCount == 0) {
                            
                            $cedula = $this->request->cedula;
                            $nombre = $this->request->nombre;
                            $apellido = $this->request->apellido;
                            $fechaNacimiento = $this->request->fechaNacimiento;
                            $genero = $this->request->genero;
                            $tipoConsumidor = $this->request->tipoUsuario;

                            if(empty($cedula)){
                                return redirect('dist/solicitud/editar/'.$solicitudId)->withErrors("ERROR EL CAMPO CEDULA ESTA VACIO CODE-0001");
                            }

                            if(empty($nombre) && empty($apellido)  ){
                                return redirect('dist/solicitud/editar/'.$solicitudId)->withErrors("ERROR EL CAMPO NOMBRE O APELLIDO ESTA VACIO CODE-0002");
                            }

                            if(empty($fechaNacimiento)){
                                return redirect('dist/solicitud/editar/'.$solicitudId)->withErrors("ERROR EL CAMPO FECHA DE NACIMIENTO ESTA VACIO CODE-0003");
                            }

                            if(empty($genero)){
                                return redirect('dist/solicitud/editar/'.$solicitudId)->withErrors("ERROR EL CAMPO GENERO ESTA VACIO CODE-0004");
                            }

                            if(empty($tipoConsumidor)){
                                return redirect('dist/solicitud/editar/'.$solicitudId)->withErrors("ERROR EL CAMPO TIPO DE CONSUMIDOR ESTA VACIO CODE-0005");
                            }


                            $consumidor = new Consumidor;
                            $consumidor->cedula           = $this->request->cedula;
                            $consumidor->nombre           = $this->request->nombre;
                            $consumidor->apellido         = $this->request->apellido;
                            $consumidor->fechaNacimiento  = $this->request->fechaNacimiento;
                            $consumidor->correo           = $this->request->correo;
                            $consumidor->telefono         = $this->request->telefono;
                            $consumidor->genero           = $this->request->genero;
                            $consumidor->tipoConsumidor   = $this->request->tipoUsuario;
                            $consumidor->solicitudId      = $this->request->solicitudId;
                            $consumidor->usuarioId        = Auth::user()->id;
                            $result = $consumidor->save();
                        }else{

                            $consumidorUpdate = Consumidor::find($solicitudId);
                            $consumidorUpdate->cedula           = $this->request->cedula;
                            $consumidorUpdate->nombre           = $this->request->nombre;
                            $consumidorUpdate->apellido         = $this->request->apellido;
                            $consumidorUpdate->fechaNacimiento  = $this->request->fechaNacimiento;
                            $consumidorUpdate->correo           = $this->request->correo;
                            $consumidorUpdate->telefono         = $this->request->telefono;
                            $consumidorUpdate->genero           = $this->request->genero;
                            $consumidorUpdate->tipoConsumidor   = $this->request->tipoUsuario;
                            $result = $consumidorUpdate->save();

                        }

                    //DB::table('cubiculo')->where('solicitudId', $solicitudId)->delete();

                    //$cubiculoUpdate = Cubiculo::find($solicitudId);
                    //$cubiculoUpdate->estatus          = 'Inactivo';
                    //$result = $cubiculoUpdate->save();
                    $cubiculoUpdate = Cubiculo::where('solicitudId', $solicitudId)
                    ->update(['estatus' => 'Inactivo']);

                    //$cubiculoCount = Cubiculo::count();
                    $cubiculoCount = Cubiculo::where('estatus', 'Activo')->count();

                    //return $cubiculoCount;


                    if ($cubiculoCount <= 7) {

                        // $solicitud = DB::table('solicitud')
                        // ->where('estatus', '=', 'Activo' )
                        // ->orderBy('id', 'asc') // o 'desc' para orden descendente
                        // ->first();
                        
                        $solicitud = DB::table('solicitud')
                        ->where('estatus', '=', 'Activo')
                        ->whereNotExists(function ($query) {
                            $query->select(DB::raw(1))
                            ->from('cubiculo')
                            ->whereRaw('cubiculo.solicitudId = solicitud.id');
                        })
                        ->orderBy('id', 'asc') // o 'desc' para orden descendente
                        ->first();

                        $colaborador = DB::table('colaboradores')
                        ->where('estatus', '=', 'Activo')
                        ->whereExists(function ($query) {
                            $query->select(DB::raw(1))
                            ->from('cubiculo')
                            ->whereRaw('cubiculo.funcionarioId = colaboradores.id')
                            ->where('cubiculo.estatus', '=', 'Inactivo');
                        })
                        ->orderBy('id', 'asc') // o 'desc' para orden descendente
                        ->first();

                        //return $solicitud->codigo;

                    if(isset($solicitud)){ 
                                               //return $cubiculoCount;
                        $cubiculo = new Cubiculo;
                        $cubiculo->solicitudId      = $solicitud->id;
                        $cubiculo->funcionarioId    = $colaborador->id;
                        $cubiculo->llamado          = 0;
                        $cubiculo->estatus          = 'Activo';	
                        $cubiculo->codigo           = $solicitud->codigo;
                        $cubiculo->usuarioId       = Auth::user()->id;
                        //$solicitud->organizacionId          = 1;
                        $result = $cubiculo->save();
                        
                        }
                    }


                    }
        
                if($result != 1){
                    DB::rollBack();
        
                    return redirect('dist/solicitud/editar/'.$solicitudId)->withErrors("ERROR AL EDITAR ELEMENTOS DE STORE CEBECECO CODE-0006");
                }
        
                DB::commit();
        
                return redirect('dist/solicitud/')->with('alertSuccess', 'STORE CEBECECO HA SIDO EDITADO');
            }
        
            public function Mostrar($solicitudId){
                /*if(!$this->common->usuariopermiso('004')){
                    return redirect('dist/dashboard')->withErrors($this->common->message);
                }*/
        
                $solicitud = DB::table('solicitud')
                 ->where('solicitud.id', '=', $solicitudId)
                 ->leftjoin('departamento', 'departamento.id', '=', 'solicitud.departamentoId')
                 ->leftjoin('tipoAtencion', 'tipoAtencion.id', '=', 'solicitud.idTipoAtencion')
                 ->select('solicitud.*', 'departamento.nombre', 'tipoAtencion.descripcion')->first();
        
                if(empty($solicitud)){
                    return redirect('dist/solicitud')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0007");
                }
        
                //return $compania;
        
                 view()->share('solicitud', $solicitud);
        
                return \View::make('dist/solicitud/mostrar');
            }
            public function Desactivar(){
                /*if(!$this->common->usuariopermiso('004')){
                    return response()
                      ->json(['response' => false]);
                }*/
                
                $solicitudExiste = Departamento::where('id', $this->request->solicitudId)
                                //->where('distribuidorId', Auth::user()->distribuidorId)
                                ->first();
                if(!empty($solicitudExiste)){
        
                    $estatus = 'Inactivo';
                    if($solicitudExiste->estatus == 'Inactivo'){
                        $estatus = 'Activo';	
                    }
        
                    $affectedRows = Departamento::where('id', '=', $this->request->solicitudId)
                                    ->update(['estatus' => $estatus]);
                    
                    return response()
                      ->json(['response' => TRUE]);
                }
        
                return response()
                      ->json(['response' => false]);
            }


            public function postBuscatipoatencion(){

                $departamento = $this->request->departamento;
                
                $tipoAtencion = DB::table('tipoAtencion')
                ->where('estatus', '=', 'Activo')
                ->where('departamentoId', '=', $departamento)
                ->select('id', 'descripcion', 'codigo')
                ->get();

                $data[] = "";
            
                foreach ($tipoAtencion as $key => $value) {
                    
                    $tipoAtencionid = $value->id;
                    $tipoAtenciondescripcion = $value->descripcion;
                    $tipoAtencioncodigo = $value->codigo;
            
                    $data[] = array(
                        "detalle"=> "<option value='".$tipoAtencionid."' >".$tipoAtenciondescripcion."</option>"
                    );		  		 
                        
                }		
                    $response = array(
                        'response' => TRUE,
                        'data' => $data,
                    );
            
                    return response()
                    ->json($response);				
                        
            }


            public function postBuscamotivo(){

                $departamento = $this->request->departamento;
                
                $motivo = DB::table('motivo')
                ->where('estatus', '=', 'Activo')
                ->where('departamentoId', '=', $departamento)
                ->select('id', 'descripcion', 'codigo')
                ->get();

                $data[] = "";
            
                foreach ($motivo as $key => $value) {
                    
                    $motivoid = $value->id;
                    $motivodescripcion = $value->descripcion;
                    $motivocodigo = $value->codigo;
            
                    $data[] = array(
                        "detalle"=> "<option value='".$motivoid."' >".$motivodescripcion."</option>"
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
