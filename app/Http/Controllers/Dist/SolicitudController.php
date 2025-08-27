<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use App\Models\Solicitud;

use App\Models\User;

use App\Models\Paises;
use App\Models\Provincia;
use App\Models\Distrito;
use App\Models\Corregimiento;

use App\Models\Multas;
use App\Models\MultasArchivos;
use App\Models\MultasMonto;
use App\Models\MultasTipo;

use App\Models\SolicitudCambioArchivos;
use App\Models\SolicitudCambioEstados;
use App\Models\SolicitudCambioResidencia;

use App\Helpers\CommonHelper;

use DB;
use Excel;

class SolicitudController extends Controller
{
    //
    private $request;
    private $common;

    public function __construct(Request $request){

        $this->request = $request;
        $this->common = New CommonHelper();
    }

    public function Index(){
        
        return view('dist.solicitud.index');

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
        
                $query = DB::table('solicitudes_cambio_residencia')
                ->leftjoin('users', 'users.id', '=', 'solicitudes_cambio_residencia.usuario_id')
                ->leftjoin('provincia', 'provincia.id', '=', 'solicitudes_cambio_residencia.provincia_id')
                ->leftjoin('distrito', 'distrito.id', '=', 'solicitudes_cambio_residencia.distrito_id')
                ->leftjoin('corregimiento', 'corregimiento.id', '=', 'solicitudes_cambio_residencia.corregimiento_id')
                ->where('solicitudes_cambio_residencia.usuario_id', Auth::id())
                ->select(
                        'solicitudes_cambio_residencia.*',
                        'users.documento_numero as filiacion',
                        // DB::raw("CONCAT(SUBSTRING(unidad_solicitante.descripcion, 1, 20), '...') as unidad"),
                        // DB::raw("CONCAT(SUBSTRING(motivo_operativo.descripcion, 1, 20), '...') as motivo"),  
                         DB::raw("CONCAT(users.primer_nombre, ' ', users.primer_apellido) AS nombre_completo"),
                        // DB::raw("CONCAT(aprobado.name, ' ', aprobado.lastName) AS aprob"),
                        // 'pais.pais as pais',              
                        // 'nacionalidad.nacionalidad as nacionalidad',
                         'provincia.nombre as provincia',
                         'distrito.nombre as distrito',
                         'corregimiento.nombre as corregimiento',
                        // 'infractor.primerNombre',
                        // 'infractor.primerApellido',
                        // 'infractor.documento',
                        // 'infractor.genero',
                        // 'infractores_operativos.estatus',
                        DB::raw("ROW_NUMBER() OVER (ORDER BY solicitudes_cambio_residencia.id) AS cuenta")      

                    )
                ->orderBy($orderBy, $order);

        
                if(isset($request['searchInput']) && trim($request['searchInput']) != ""){
                    $query->where(
                        function ($query) use ($request) {
                            $query->orWhere('users.primer_nombre', 'like', '%'.trim($request['searchInput']).'%');
                            $query->orWhere('users.primer_apellido', 'like', '%'.trim($request['searchInput']).'%');
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
                        "DT_RowId" => $value->id,
                        "id" => $value->cuenta,
                        "nombre"=> $value->nombre_completo,
                        "ruex"=> $value->filiacion,
                        "codigo"=> $value->calle,
                        "direccion"=> $value->provincia .', '. $value->distrito .', '. $value->corregimiento,
                        "estatus"     =>  $value->estatus,
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

                $Usuario = User::find(Auth::user()->id)->first();  

                if(empty($Usuario)){
                    return redirect('dist/solicitud/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0001");
                }
                
                $provincia = Provincia::where('estatus', 'Activo')->get();

                if(empty($provincia)){
                    return redirect('dist/solicitud/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0002");
                }

                $distrito = Distrito::where('estatus', 'Activo')->get();

                if(empty($distrito)){
                    return redirect('dist/solicitud/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0002");
                }

                $corregimiento = Corregimiento::where('estatus', 'Activo')->get();

                if(empty($corregimiento)){
                    return redirect('dist/solicitud/nuevo')->withErrors("ERROR AL GUARDAR STORE CEBECECO CODE-0002");
                }


                return view('dist.solicitud.nuevo', compact('Usuario', 'provincia', 'distrito', 'corregimiento'));
            }
        
    public function postNuevo(){

        $request = $this->request;


        $validated = $request->validate([
            // Ubicación
            'provincia'         => 'required|integer',
            'distrito'          => 'required|integer',
            'corregimiento'     => 'required|integer',
            'barrio'            => 'required|string|max:191',
            'calle'             => 'required|string|max:191',
            'punto_referencia'  => 'required|string|max:255',

            // Tipo de vivienda (condicional vía JS)
            'numero_casa'       => 'nullable|string|max:50',
            'nombre_edificio'   => 'nullable|string|max:191',
            'piso'              => 'nullable|string|max:10',
            'apartamento'       => 'nullable|string|max:50',
            'nombre_hotel'      => 'nullable|string|max:191',

            // Prueba de domicilio (siempre)
            'domicilio_opcion'  => 'required|in:escritura,arrendamiento,responsabilidad,juez_paz,reserva_hotel',
            'domicilio_archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',

            // Recibo (si NO es reserva de hotel)
            'recibo_tipo'               => 'nullable|in:propio,tercero',
            'recibo_archivo'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'recibo_notariado_archivo'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'recibo_cedula_titular'     => 'nullable|array',
            'recibo_cedula_titular.*'   => 'file|mimes:pdf,jpg,jpeg,png|max:5120',

            // Carnet (siempre)
            'carnet_frente'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'carnet_reverso'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',

            // Selfies (tu JS las manda como selfies[])
            'selfies'           => 'nullable|array',
            'selfies.*'         => 'file|mimes:jpg,jpeg,png|max:4096',

            // Otros
            'inversionista'     => 'required|in:Si,No',
            'comentario'        => 'nullable|string',
        ]);

        return DB::transaction(function () use ($request, $validated) {

            // 2) CREAR SOLICITUD
            $solicitud = SolicitudCambioResidencia::create([
                'codigo'           => $this->common->generaCodigoSCR(),
                'usuario_id'       => auth()->id(),
                'inversionista'    => $request->input('inversionista', 'No'),

                'provincia_id'     => $validated['provincia'],
                'distrito_id'      => $validated['distrito'],
                'corregimiento_id' => $validated['corregimiento'],
                'barrio'           => $validated['barrio'],
                'calle'            => $validated['calle'],
                'numero_casa'      => $request->input('numero_casa'),
                'nombre_edificio'  => $request->input('nombre_edificio'),
                'piso'             => $request->input('piso'),
                'apartamento'      => $request->input('apartamento'),
                'nombre_hotel'     => $request->input('nombre_hotel'),
                'punto_referencia' => $validated['punto_referencia'],

                'domicilio_opcion' => $validated['domicilio_opcion'],
                'recibo_tipo'      => $request->input('recibo_tipo'), // puede ser null si hotel

                'comentario'       => $request->input('comentario'),
                'estatus'          => 'Recibida',
        ]);

        $solicitud->estados()->create([
            'estatus'    => 'Recibida',
            'comentario' => 'Solicitud registrada por el usuario.',
            'usuario_id' => auth()->id(),
            'created_at' => now(),
        ]);

        $store = function ($uploadedFile, string $tipo) use ($solicitud) {
            $path = $uploadedFile->store("solicitudes_cambio/{$solicitud->id}", 'public');
            $solicitud->archivos()->create([
                'tipo'            => $tipo,
                'ruta'            => $path,
                'nombre_original' => $uploadedFile->getClientOriginalName(),
                'mime'            => $uploadedFile->getMimeType(),
                'tamano'          => $uploadedFile->getSize(),
                'usuario_id'      => auth()->id(),
                'estatus'         => 'Activo',
            ]);
        };


        $store($request->file('domicilio_archivo'), 'domicilio');

        // Recibo (si NO es hotel)
        if ($validated['domicilio_opcion'] !== 'reserva_hotel') {
            if ($request->hasFile('recibo_archivo')) {
                $store($request->file('recibo_archivo'), 'recibo');
            }
            if ($request->input('recibo_tipo') === 'tercero') {
                if ($request->hasFile('recibo_notariado_archivo')) {
                    $store($request->file('recibo_notariado_archivo'), 'recibo_notariado');
                }
                if ($request->hasFile('recibo_cedula_titular')) {
                    foreach ($request->file('recibo_cedula_titular') as $ced) {
                        $store($ced, 'cedula_titular');
                    }
                }
            }
        }

         // Carnet (siempre)
        $store($request->file('carnet_frente'),  'carnet_frente');
        $store($request->file('carnet_reverso'), 'carnet_reverso');

        // Selfies (opcionales, si tu JS ya las adjunta)
        if ($request->hasFile('selfies')) {
            foreach ($request->file('selfies') as $file) {
                $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $tipo = str_starts_with($base, 'frente')    ? 'selfie_frente' :
                        (str_starts_with($base, 'izquierda') ? 'selfie_izquierda' :
                        (str_starts_with($base, 'derecha')   ? 'selfie_derecha' :
                        (str_starts_with($base, 'arriba')    ? 'selfie_arriba' :
                        (str_starts_with($base, 'abajo')     ? 'selfie_abajo' : 'selfie_frente'))));
                $store($file, $tipo);
            }
        }

         // 5) RESPUESTA
            return response()->json([
                'ok'      => true,
                'id'      => $solicitud->id,
                'codigo'  => $solicitud->codigo,
                'message' => 'Solicitud registrada correctamente.',
                // 'redirect' => route('dist.solicitud.show', $solicitud->id),
            ]);
        });

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
