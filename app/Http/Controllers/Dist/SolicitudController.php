<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

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

use App\Models\Afinidad;

use App\Helpers\CommonHelper;

use DB;
use Excel;

class SolicitudController extends Controller
{
    private $request;
    private $common;

    public function __construct(Request $request){

        $this->request = $request;
        $this->common = New CommonHelper();
    }

    public function Index(){

        $afinidad = Afinidad::where('estatus', 'Activo')
            ->select('id', 'descripcion')
            ->get();

        if ($afinidad->isEmpty()) {
            return back()->withErrors("ERROR ESTATUS OPERATIVO ESTA VACIO CODE-0002");
        }

        view()->share('afinidad', $afinidad);
        
        return view('dist.solicitud.index');

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

           
            $detalle = '
                <div class="dropdown text-center">
                    <button class="btn btn-primary bg-gradient border-0 dropdown-toggle p-1"
                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow">';

                // Ver siempre
                $detalle .= '
                        <li>
                            <a class="dropdown-item mostrar text-dark" href="#" attr-id="'.$value->id.'">
                                <i class="bi bi-eye me-2 text-dark"></i> Ver
                            </a>
                        </li>';

                // Editar si está Por corregir
                if ($value->estatus == 'Por corregir') {
                    $detalle .= '
                        <li>
                            <a class="dropdown-item text-warning" href="/dist/multas/editar/'.$value->id.'">
                                <i class="bi bi-pencil me-2 text-warning"></i> Editar
                            </a>
                        </li>';
                }

                // debe pagar si está Aprobada - con pago
                if ($value->estatus == 'Aprobada - con pago') {
                    $detalle .= '
                        <li>
                            <a class="dropdown-item text-success" href="/payment/tokenize/">
                                <i class="bi bi-currency-dollar me-2 text-success"></i> Por Pagar
                            </a>
                        </li>';
                }
                  
                                
                $detalle .= '
                    </ul>
                </div>';


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

        return view('dist.solicitud.solicitud');

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

        $Usuario = User::find(Auth::id());

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

         // NUEVO: leer token efímero (si viene) para prefill
        $prefillTitularNF  = null; // Ruex seleccionado
        $prefillAfinidadId = null; // Afinidad seleccionada

        if ($token = request()->query('sel')) {
            $payload = Cache::pull("sel:$token"); // single-use
            if (!$payload) {
                return back()->withErrors('El enlace de selección expiró. Vuelve a buscar.');
            }
            $prefillTitularNF  = $payload['nf']          ?? null;
            $prefillAfinidadId = $payload['afinidad_id'] ?? null;
        }

        // (Opcional) lista para mostrar nombre de la afinidad elegida
        $afinidad = Afinidad::where('estatus', 'Activo')
            ->where('id', $prefillAfinidadId)
            ->select('id','descripcion')
            ->first();


        return view('dist.solicitud.nuevo', compact(
            'Usuario',
            'provincia',
            'distrito',
            'corregimiento',
            'prefillTitularNF',
            'prefillAfinidadId',
            'afinidad'
        ));
    }
        
    // public function postNuevo(){

    //     $request = $this->request;


    //     $validated = $request->validate([
    //         // Ubicación
    //         'provincia'         => 'required|integer',
    //         'distrito'          => 'required|integer',
    //         'corregimiento'     => 'required|integer',
    //         'barrio'            => 'required|string|max:191',
    //         'calle'             => 'required|string|max:191',
    //         'punto_referencia'  => 'required|string|max:255',

    //         // Tipo de vivienda (condicional vía JS)
    //         'numero_casa'       => 'nullable|string|max:50',
    //         'nombre_edificio'   => 'nullable|string|max:191',
    //         'piso'              => 'nullable|string|max:10',
    //         'apartamento'       => 'nullable|string|max:50',
    //         'nombre_hotel'      => 'nullable|string|max:191',

    //         // Prueba de domicilio (siempre)
    //         'domicilio_opcion'  => 'required|in:escritura,arrendamiento,responsabilidad,juez_paz,reserva_hotel',
    //         'domicilio_archivo' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',

    //         // Recibo (si NO es reserva de hotel)
    //         'recibo_tipo'               => 'nullable|in:propio,tercero',
    //         'recibo_archivo'            => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    //         'recibo_notariado_archivo'  => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
    //         'recibo_cedula_titular'     => 'nullable|array',
    //         'recibo_cedula_titular.*'   => 'file|mimes:pdf,jpg,jpeg,png|max:5120',

    //         // Carnet (siempre)
    //         'carnet_frente'     => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
    //         'carnet_reverso'    => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',

    //         // Selfies (tu JS las manda como selfies[])
    //         'selfies'           => 'nullable|array',
    //         'selfies.*'         => 'file|mimes:jpg,jpeg,png|max:4096',

    //         // Otros
    //         'inversionista'     => 'required|in:Si,No',
    //         'comentario'        => 'nullable|string',
    //     ]);

    //     return DB::transaction(function () use ($request, $validated) {

    //         // 2) CREAR SOLICITUD
    //         $solicitud = SolicitudCambioResidencia::create([
    //             'codigo'           => $this->common->generaCodigoSCR(),
    //             'usuario_id'       => auth()->id(),
    //             'inversionista'    => $request->input('inversionista', 'No'),

    //             'provincia_id'     => $validated['provincia'],
    //             'distrito_id'      => $validated['distrito'],
    //             'corregimiento_id' => $validated['corregimiento'],
    //             'barrio'           => $validated['barrio'],
    //             'calle'            => $validated['calle'],
    //             'numero_casa'      => $request->input('numero_casa'),
    //             'nombre_edificio'  => $request->input('nombre_edificio'),
    //             'piso'             => $request->input('piso'),
    //             'apartamento'      => $request->input('apartamento'),
    //             'nombre_hotel'     => $request->input('nombre_hotel'),
    //             'punto_referencia' => $validated['punto_referencia'],

    //             'domicilio_opcion' => $validated['domicilio_opcion'],
    //             'recibo_tipo'      => $request->input('recibo_tipo'), // puede ser null si hotel

    //             'comentario'       => $request->input('comentario'),
    //             'estatus'          => 'Recibida',
    //     ]);

    //     $solicitud->estados()->create([
    //         'estatus'    => 'Recibida',
    //         'comentario' => 'Solicitud registrada por el usuario.',
    //         'usuario_id' => auth()->id(),
    //         'created_at' => now(),
    //     ]);

    //     $store = function ($uploadedFile, string $tipo) use ($solicitud) {
    //         $path = $uploadedFile->store("solicitudes_cambio/{$solicitud->id}", 'public');
    //         $solicitud->archivos()->create([
    //             'tipo'            => $tipo,
    //             'ruta'            => $path,
    //             'nombre_original' => $uploadedFile->getClientOriginalName(),
    //             'mime'            => $uploadedFile->getMimeType(),
    //             'tamano'          => $uploadedFile->getSize(),
    //             'usuario_id'      => auth()->id(),
    //             'estatus'         => 'Activo',
    //         ]);
    //     };


    //     $store($request->file('domicilio_archivo'), 'domicilio');

    //     // Recibo (si NO es hotel)
    //     if ($validated['domicilio_opcion'] !== 'reserva_hotel') {
    //         if ($request->hasFile('recibo_archivo')) {
    //             $store($request->file('recibo_archivo'), 'recibo');
    //         }
    //         if ($request->input('recibo_tipo') === 'tercero') {
    //             if ($request->hasFile('recibo_notariado_archivo')) {
    //                 $store($request->file('recibo_notariado_archivo'), 'recibo_notariado');
    //             }
    //             if ($request->hasFile('recibo_cedula_titular')) {
    //                 foreach ($request->file('recibo_cedula_titular') as $ced) {
    //                     $store($ced, 'cedula_titular');
    //                 }
    //             }
    //         }
    //     }

    //      // Carnet (siempre)
    //     $store($request->file('carnet_frente'),  'carnet_frente');
    //     $store($request->file('carnet_reverso'), 'carnet_reverso');

    //     // Selfies (opcionales, si tu JS ya las adjunta)
    //     if ($request->hasFile('selfies')) {
    //         foreach ($request->file('selfies') as $file) {
    //             $base = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    //             $tipo = str_starts_with($base, 'frente')    ? 'selfie_frente' :
    //                     (str_starts_with($base, 'izquierda') ? 'selfie_izquierda' :
    //                     (str_starts_with($base, 'derecha')   ? 'selfie_derecha' :
    //                     (str_starts_with($base, 'arriba')    ? 'selfie_arriba' :
    //                     (str_starts_with($base, 'abajo')     ? 'selfie_abajo' : 'selfie_frente'))));
    //             $store($file, $tipo);
    //         }
    //     }

    //      // 5) RESPUESTA
    //         return response()->json([
    //             'ok'      => true,
    //             'id'      => $solicitud->id,
    //             'codigo'  => $solicitud->codigo,
    //             'message' => 'Solicitud registrada correctamente.',
    //             // 'redirect' => route('dist.solicitud.show', $solicitud->id),
    //         ]);
    //     });

    // }

    public function postNuevo(){

        $user = Auth::user();
        $r    = $this->request; // atajo local

        // 0) Determinar titular según tipo de usuario
        if ($user->tipo_usuario === 'solicitante') {
            $titularNF = trim((string)$user->documento_numero);
            if ($titularNF === '') {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Debes registrar tu N° de filiación (Ruex) en tu perfil antes de iniciar la solicitud.'
                ], 422);
            }
        } else { // abogado
            $titularNF = trim((string)$r->input('titular_num_filiacion'));
            if ($titularNF === '') {
                return response()->json([
                    'ok'      => false,
                    'message' => 'Debes seleccionar el titular (N° de filiación).'
                ], 422);
            }
        }

        // 1) Validación de inputs
        $validated = $r->validate([
            // Ubicación
            'provincia'         => 'required|integer',
            'distrito'          => 'required|integer',
            'corregimiento'     => 'required|integer',
            'barrio'            => 'required|string|max:191',
            'calle'             => 'required|string|max:191',
            'punto_referencia'  => 'required|string|max:255',

            // Tipo de vivienda
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

            // Selfies (opcionales, si tu JS las manda como selfies[])
            'selfies'           => 'nullable|array',
            'selfies.*'         => 'file|mimes:jpg,jpeg,png|max:4096',

            // Otros
            'inversionista'     => 'required|in:Si,No',
            'comentario'        => 'nullable|string',
        ]);

        // 2) Regla: nadie puede tener otra solicitud activa (≠ Rechazada/Cancelada)
        //    Usamos tu CommonHelper (asegúrate de que el método sea PUBLIC).
        if ($this->common->algunaPersonaTieneSolicitudActiva([$titularNF])) {
            return response()->json([
                'ok'      => false,
                'message' => 'La persona titular ya tiene una solicitud activa.'
            ], 409);
        }

        // 3) CREAR Solicitud + Estado inicial + Persona TITULAR + Archivos
        return DB::transaction(function () use ($validated, $titularNF) {

            // 3.1 Solicitud
            $solicitud = SolicitudCambioResidencia::create([
                'codigo'           => $this->common->generaCodigoSCR(),
                'usuario_id'       => auth()->id(),
                'inversionista'    => $this->request->input('inversionista', 'No'),

                'provincia_id'     => $validated['provincia'],
                'distrito_id'      => $validated['distrito'],
                'corregimiento_id' => $validated['corregimiento'],
                'barrio'           => $validated['barrio'],
                'calle'            => $validated['calle'],
                'numero_casa'      => $this->request->input('numero_casa'),
                'nombre_edificio'  => $this->request->input('nombre_edificio'),
                'piso'             => $this->request->input('piso'),
                'apartamento'      => $this->request->input('apartamento'),
                'nombre_hotel'     => $this->request->input('nombre_hotel'),
                'punto_referencia' => $validated['punto_referencia'],

                'domicilio_opcion' => $validated['domicilio_opcion'],
                'recibo_tipo'      => $this->request->input('recibo_tipo'),

                'comentario'       => $this->request->input('comentario'),
                'estatus'          => 'Recibida',
            ]);

            // 3.2 Estado inicial
            $solicitud->estados()->create([
                'estatus'    => 'Recibida',
                'comentario' => 'Solicitud registrada por el usuario.',
                'usuario_id' => auth()->id(),
                'created_at' => now(),
            ]);

            // 3.3 Persona TITULAR
            $titular = $solicitud->personas()->create([
                'es_titular'    => 1,
                'parentesco'    => 'titular',
                'num_filiacion' => $titularNF,
            ]);

            // 3.4 Helper para subir archivos (con persona_id opcional)
            $store = function ($uploadedFile, string $tipo, ?int $personaId = null) use ($solicitud) {
                $path = $uploadedFile->store("solicitudes_cambio/{$solicitud->id}", 'public');
                $solicitud->archivos()->create([
                    'persona_id'      => $personaId,
                    'tipo'            => $tipo,
                    'ruta'            => $path,
                    'nombre_original' => $uploadedFile->getClientOriginalName(),
                    'mime'            => $uploadedFile->getMimeType(),
                    'tamano'          => $uploadedFile->getSize(),
                    'usuario_id'      => auth()->id(),
                    'estatus'         => 'Activo',
                ]);
            };

            // 3.5 Archivos: Domicilio (general)
            $store($this->request->file('domicilio_archivo'), 'domicilio', null);

            // 3.6 Archivos: Recibo (generales) si NO es hotel
            if ($validated['domicilio_opcion'] !== 'reserva_hotel') {
                if ($this->request->hasFile('recibo_archivo')) {
                    $store($this->request->file('recibo_archivo'), 'recibo', null);
                }
                if ($this->request->input('recibo_tipo') === 'tercero') {
                    if ($this->request->hasFile('recibo_notariado_archivo')) {
                        $store($this->request->file('recibo_notariado_archivo'), 'recibo_notariado', null);
                    }
                    if ($this->request->hasFile('recibo_cedula_titular')) {
                        foreach ($this->request->file('recibo_cedula_titular') as $ced) {
                            $store($ced, 'cedula_titular', null);
                        }
                    }
                }
            }

            // 3.7 Archivos: Carnet (del TITULAR) → personales con persona_id
            $store($this->request->file('carnet_frente'),  'carnet_frente',  $titular->id);
            $store($this->request->file('carnet_reverso'), 'carnet_reverso', $titular->id);

            // 3.8 Archivos: Selfies (opcionales del TITULAR) → personales con persona_id
            if ($this->request->hasFile('selfies')) {
                foreach ($this->request->file('selfies') as $file) {
                    $name = mb_strtolower(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    $tipo = str_contains($name, 'izquierda') ? 'selfie_izquierda'
                        : (str_contains($name, 'derecha')   ? 'selfie_derecha'
                        : (str_contains($name, 'arriba')    ? 'selfie_arriba'
                        : (str_contains($name, 'abajo')     ? 'selfie_abajo'
                        : 'selfie_frente')));
                    $store($file, $tipo, $titular->id);
                }
            }

            // 4) Respuesta
            return response()->json([
                'ok'      => true,
                'id'      => $solicitud->id,
                'codigo'  => $solicitud->codigo,
                'message' => 'Solicitud registrada correctamente.',
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

        $solicitud = DB::table('solicitudes_cambio_residencia')
            ->where('solicitudes_cambio_residencia.id', '=', $solicitudId)
            ->leftjoin('solicitudes_cambio_personas', 'solicitudes_cambio_personas.solicitud_id', '=', 'solicitudes_cambio_residencia.id')

            ->leftjoin('users', 'users.id', '=', 'solicitudes_cambio_residencia.usuario_id')       
            ->leftjoin('provincia', 'provincia.id', '=', 'solicitudes_cambio_residencia.provincia_id')
            ->leftjoin('distrito', 'distrito.id', '=', 'solicitudes_cambio_residencia.distrito_id')
            ->leftjoin('corregimiento', 'corregimiento.id', '=', 'solicitudes_cambio_residencia.corregimiento_id')

            ->select(
                'solicitudes_cambio_residencia.*',
                DB::raw("CONCAT(users.primer_nombre, ' ', users.primer_apellido) AS nombre_completo"),
                'solicitudes_cambio_personas.num_filiacion',
                'provincia.nombre as provincia',
                'distrito.nombre as distrito',
                'corregimiento.nombre as corregimiento',
             )->first();

        if(empty($solicitud)){
            return redirect('dist/solicitud')->withErrors("ERROR STORE CEBECECO NO EXISTE CODE-0007");
        }
       
        return response()->json($solicitud);
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

    public function ValidarSolicitud(){

        $user = Auth::user();

        // Si es solicitante, debe tener Ruex y no estar activo como TITULAR
        if ($user->tipo_usuario === 'solicitante') {
            $nf = trim((string)$user->documento_numero);

            if ($nf === '') {
                return response()->json([
                    'ok' => false,
                    'tieneActiva' => null,
                    'error' => 'SIN_RUEX',
                    'message' => 'Debes registrar tu N° de filiación (Ruex) en tu perfil antes de iniciar la solicitud.'
                ]);
            }

            $tieneActiva = DB::table('solicitudes_cambio_personas as p')
                ->join('solicitudes_cambio_residencia as s', 's.id', '=', 'p.solicitud_id')
                ->where('p.num_filiacion', $nf)
                ->where('p.es_titular', 1)
                ->whereNotIn('s.estatus', ['Rechazada','Cancelada'])
                ->exists();

            return response()->json([
                'ok' => true,
                'tieneActiva' => $tieneActiva
            ]);
        }

        // Abogado: no bloqueamos aquí; se valida al elegir titular en el modal
        return response()->json([
            'ok' => true,
            'tieneActiva' => false
        ]);

    }

    public function validarFiliacionActiva() {

        $this->request->validate([
            'num_filiacion' => 'required|string|max:50'
        ]);

        $nf = trim((string)$this->request->input('num_filiacion'));

        $tieneActiva = DB::table('solicitudes_cambio_personas as p')
            ->join('solicitudes_cambio_residencia as s', 's.id', '=', 'p.solicitud_id')
            ->where('p.num_filiacion', $nf)
            ->whereNotIn('s.estatus', ['Rechazada','Cancelada'])
            ->exists();

        return response()->json([
            'ok' => true,
            'tieneActiva' => $tieneActiva
        ]);
    }


    public function BuscaFamiliar(){

         $data = $this->request->validate([
        'nombre'           => ['nullable', 'string', 'max:100'],
        'apellido'         => ['nullable', 'string', 'max:100'],
        'ruex'             => ['nullable', 'regex:/^[0-9]{1,15}$/'],
        'genero'           => ['nullable', 'in:Masculino,Femenino'],
        'fecha_nacimiento' => ['nullable', 'date'],
        ]);

        if (
            empty($data['nombre']) &&
            empty($data['apellido']) &&
            empty($data['ruex']) &&
            empty($data['genero']) &&
            empty($data['fecha_nacimiento'])
        ) {
            return response()->json([
                'ok'   => true,
                'data' => [],
                'msg'  => 'Debe indicar al menos un criterio de búsqueda.',
                'empty'=> true
            ]);
        }

        $q = DB::connection('simpanama')
            ->table('dbo.SIM_FI_GENERALES AS SFG')
            ->leftJoin('SIM_GE_PAIS AS SGP', 'SGP.COD_PAIS', '=', 'SFG.COD_NACION_ACTUAL')
            ->select([
                'SFG.NUM_REG_FILIACION',
                'SFG.NOM_PRIMER_APELL',
                'SFG.NOM_SEGUND_APELL',
                'SFG.NOM_PRIMER_NOMB',
                'SFG.NOM_SEGUND_NOMB',
                'SFG.IND_SEXO',
                'SFG.FEC_NACIM',
                'SGP.NOM_NACIONALIDAD',
            ]);


        if (!empty($data['ruex'])) {
            $q->where('NUM_REG_FILIACION', (int) $data['ruex']);
        }

        if (!empty($data['nombre'])) {
            $nombre = trim($data['nombre']);
            $q->where(function ($w) use ($nombre) {
                $w->where('NOM_PRIMER_NOMB', 'like', "%{$nombre}%")
                ->orWhere('NOM_SEGUND_NOMB', 'like', "%{$nombre}%");
            });
        }

        if (!empty($data['apellido'])) {
            $apellido = trim($data['apellido']);
            $q->where(function ($w) use ($apellido) {
                $w->where('NOM_PRIMER_APELL', 'like', "%{$apellido}%")
                ->orWhere('NOM_SEGUND_APELL', 'like', "%{$apellido}%");
            });
        }

        if (!empty($data['genero'])) {
            $g = $data['genero'] === 'Masculino' ? ['M', 'Masculino'] : ['F', 'Femenino'];
            $q->whereIn('IND_SEXO', $g);
        }

        if (!empty($data['fecha_nacimiento'])) {
            $q->whereDate('FEC_NACIM', $data['fecha_nacimiento']);
        }

        $result = $q->limit(100)->get();

        $dataOut = $result->map(function ($r) {
            $nombres   = trim((mb_convert_case(strtolower($r->NOM_PRIMER_NOMB), MB_CASE_TITLE, "UTF-8") ?? '') . ' ' . (mb_convert_case(strtolower($r->NOM_SEGUND_NOMB), MB_CASE_TITLE, "UTF-8") ?? ''));
            $apellidos = trim((mb_convert_case(strtolower($r->NOM_PRIMER_APELL), MB_CASE_TITLE, "UTF-8") ?? '') . ' ' . (mb_convert_case(strtolower($r->NOM_SEGUND_APELL), MB_CASE_TITLE, "UTF-8") ?? ''));
            $nombreCompleto = trim($nombres . ' ' . $apellidos);

            $genero = $r->IND_SEXO;
            if ($genero === 'M') $genero = 'Masculino';
            if ($genero === 'F') $genero = 'Femenino';

            return [
                'nombre'           => $nombreCompleto ?: '—',
                'documento'        => $r->NUM_REG_FILIACION ?? '—',
                'genero'           => $genero ?: '—',
                'nacionalidad'     => $r->NOM_NACIONALIDAD?: '—', // puedes añadir si tu tabla la trae
                'fecha_nacimiento' => $r->FEC_NACIM 
                    ? \Illuminate\Support\Carbon::parse($r->FEC_NACIM)->format('Y-m-d') 
                    : '—',
            ];
        })->values();

        return response()->json([
            'ok'   => true,
            'data' => $dataOut,
        ]);
    }

    public function SeleccionFamiliar(){

        // Resolver el nombre real de la tabla de Afinidad para evitar errores de pluralización
        $tablaAfinidad = (new Afinidad)->getTable();

        $validated = $this->request->validate([
            'documento'   => ['required', 'regex:/^[0-9]{1,15}$/'],          // Ruex
            'afinidad_id' => ['required', 'integer', "exists:{$tablaAfinidad},id"],
        ], [
            'documento.required' => 'Debe indicar el N° de filiación.',
            'documento.regex'    => 'El N° de filiación no es válido.',
            'afinidad_id.*'      => 'Debe seleccionar una afinidad válida.',
        ]);

        // Token de un solo uso (TTL 15 min)
        $token = (string) Str::uuid();

        Cache::put("sel:{$token}", [
            'nf'          => $validated['documento'],          // Ruex
            'afinidad_id' => (int) $validated['afinidad_id'],  // Afinidad elegida
        ], now()->addMinutes(15));

        // Redirige a /dist/solicitud/nuevo con el token
        $redirect = route('solicitud.Nuevo', ['sel' => $token]);

        return response()->json(['ok' => true, 'redirect' => $redirect]);
    }



}
