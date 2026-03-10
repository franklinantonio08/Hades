<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // return view('auth.tipo-acceso');
        return view('auth.register');
    }

    public function tipoAcceso()
    {
        return view('auth.tipo-acceso');
    }
    
    public function prestadorServicio()
    {
        return view('auth.prestador-servicio');
    }

    public function informacionPersonal()
    {
        return view('auth.informacion-personal');
    }


    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)   {

        $rules = [
            // Nombres / apellidos
            'primer_nombre'    => ['required','string','max:100'],
            'segundo_nombre'   => ['nullable','string','max:100'],
            'primer_apellido'  => ['required','string','max:100'],
            'segundo_apellido' => ['nullable','string','max:100'],

            // Contacto
            'email'            => ['required','string','email','max:255','unique:users,email'],
            'telefono'         => ['nullable','string','max:25'],

            // Tipo
            'tipo_usuario'     => ['required', Rule::in(['abogado','solicitante'])],

            // ------ Solicitante ------
            // Este campo *sólo* existe para solicitante; si no, se excluye de la validación
            'documento_tipo'   => [
                'exclude_unless:tipo_usuario,solicitante',
                'required',
                Rule::in(['Ruex']),
            ],
            'documento_numero' => [
                'exclude_unless:tipo_usuario,solicitante',
                'required',
                'string',
                'max:50',
            ],
            
            'genero'           => ['exclude_unless:tipo_usuario,solicitante','required','in:Masculino,Femenino'],
            'fecha_nacimiento' => ['exclude_unless:tipo_usuario,solicitante','required','date_format:Y-m-d','before:today'],

            // ------ Abogado ------
            'abogado_id'       => [
                'exclude_unless:tipo_usuario,abogado',
                'required',
                'string',
                'max:50',
            ],
            'firma_estudio'    => [
                'exclude_unless:tipo_usuario,abogado',
                'nullable',
                'string',
                'max:120',
            ],
            'idoneidad'        => [
                'exclude_unless:tipo_usuario,abogado',
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120',
            ],

            // Password
            'password'         => ['required','confirmed', Rules\Password::defaults()],
            'acepta_terminos' => ['required', 'accepted'],
        ];


        // $data = $request->validate($rules);

        $messages = [
            'acepta_terminos.accepted' => 'Debe aceptar la Declaración y Términos del RUEX para continuar.',
        ];

        $data = $request->validate($rules, $messages);

        $pasaporte = null;

        // (Opcional) Verificación fuerte contra SIM cuando es solicitante:
        if ($data['tipo_usuario'] === 'solicitante') {
            
            $sim = DB::connection('DATAMIND')
                ->table('dbo.RUEX_INFO')
                ->select([
                    'num_filiacion',
                    'primerApellido','segundoApellido',
                    'primerNombre','segundoNombre',
                    'genero','fecha_nacimiento',
                    'cod_pais_nacionalidad','cod_pais_nacimiento',
                    'pasaporte', 'telefono'
                ])
                ->where('num_filiacion', $data['documento_numero'])
                ->first();


            if (!$sim) {
                throw ValidationException::withMessages([
                    'documento_numero' => 'No se encontró la filiación en el sistema.',
                ]);
            }

                $codNac   = strtoupper(trim($sim->cod_pais_nacionalidad));
                $codNacim = strtoupper(trim($sim->cod_pais_nacimiento));

                $paisNacionalidad = null;
                $paisNacimiento   = null;

                $codigos = collect([$codNac, $codNacim])->filter()->unique()->values();

                $paises = DB::table('paises')
                    ->whereIn('cod_pais', $codigos)
                    ->where('estatus','Activo')
                    ->get()
                    ->keyBy('cod_pais');

                $paisNacionalidad = $paises[$codNac] ?? null;
                $paisNacimiento   = $paises[$codNacim] ?? null;


                $data['primer_nombre'] = $sim->primerNombre;
                $data['segundo_nombre'] = $sim->segundoNombre;
                $data['primer_apellido'] = $sim->primerApellido;
                $data['segundo_apellido'] = $sim->segundoApellido;

                $data['genero'] = ucfirst(strtolower(trim($sim->genero)));

                $data['telefono'] = trim((string)($sim->telefono ?? '')) ?: null;

                $data['fecha_nacimiento'] = date('Y-m-d', strtotime($sim->fecha_nacimiento));

                $data['paisId'] = $paisNacimiento->id ?? null;
                $data['nacionalidadId'] = $paisNacionalidad->id ?? null;

                $pasaporte = trim((string)($sim->pasaporte ?? '')) ?: null;

            // Comparaciones simples (puedes endurecerlas si quieres)
            $simPN = strtoupper(trim($sim->primerNombre ?? ''));
            $simPA = strtoupper(trim($sim->primerApellido ?? ''));
            $inPN  = strtoupper(trim($data['primer_nombre'] ?? ''));
            $inPA  = strtoupper(trim($data['primer_apellido'] ?? ''));

            if ($simPN && $inPN && $simPN !== $inPN) {
                throw ValidationException::withMessages([
                    'primer_nombre' => 'El primer nombre no coincide con el registro de filiación.',
                ]);
            }
            if ($simPA && $inPA && $simPA !== $inPA) {
                throw ValidationException::withMessages([
                    'primer_apellido' => 'El primer apellido no coincide con el registro de filiación.',
                ]);
            }

            // Si quieres, pisas género/fecha con SIM (o sólo si vinieron vacíos)   

            $fechaSim = null;
            if (!empty($sim->fecha_nacimiento)) {
                $ts = strtotime((string)$sim->fecha_nacimiento);
                if ($ts !== false) {
                    $fechaSim = date('Y-m-d', $ts); // YYYY-MM-DD
                }
            }
            if ($fechaSim) {
                $data['fecha_nacimiento'] = $fechaSim;
            }
        }

            // Guardar archivo de idoneidad si aplica
            $idoneidadPath = null;
            if ($data['tipo_usuario'] === 'abogado' && $request->hasFile('idoneidad')) {
                $idoneidadPath = $request->file('idoneidad')->store('idoneidades', 'public');
            }         

            $primer_nombre    = $data['primer_nombre'];
            $segundo_nombre   = $data['segundo_nombre'] ?? null;
            $primer_apellido  = $data['primer_apellido'];
            $segundo_apellido = $data['segundo_apellido'] ?? null;

            $fullName = trim(
                            ($data['primer_nombre'] ?? '') . ' ' .
                            ($data['segundo_nombre'] ?? '') . ' ' .
                            ($data['primer_apellido'] ?? '') . ' ' .
                            ($data['segundo_apellido'] ?? '')
                        );

            $user = User::create([
                // Nombres desglosados + name
                'primer_nombre'    => $primer_nombre,
                'segundo_nombre'   => $segundo_nombre,
                'primer_apellido'  => $primer_apellido,
                'segundo_apellido' => $segundo_apellido,
                'name'             => $fullName,

                'pasaporte'        => $pasaporte,

                'genero'           => $data['genero'] ?? null,
                'fecha_nacimiento' => $data['fecha_nacimiento'] ?? null,

                'paisId'           => $data['paisId'] ?? null,
                'nacionalidadId'   => $data['nacionalidadId'] ?? null,

                'email'            => $data['email'],
                'telefono'         => $data['telefono'] ?? null,

                'tipo_usuario'     => $data['tipo_usuario'],

                'documento_tipo'   => $data['tipo_usuario'] === 'solicitante' ? ($data['documento_tipo'] ?? null) : null,
                'documento_numero' => $data['tipo_usuario'] === 'solicitante' ? ($data['documento_numero'] ?? null) : null,

    
                'abogado_id'       => $data['tipo_usuario'] === 'abogado' ? ($data['abogado_id'] ?? null) : null,
                'firma_estudio'    => $data['tipo_usuario'] === 'abogado' ? ($data['firma_estudio'] ?? null) : null,
                'idoneidad_path'   => $idoneidadPath,

                'password'         => Hash::make($data['password']),
            ]);

            event(new Registered($user));

       
            return redirect()->route('login')->with('status', 'Cuenta creada. Revisa tu correo para verificar tu dirección.');

  
    }

    public function buscarFiliacion(Request $request) {
            
        $validated = $request->validate([
            'documento_tipo'   => ['required', Rule::in(['Ruex'])],
            'documento_numero' => ['required', 'string', 'max:50'],
        ]);

        $ruex = trim($validated['documento_numero']);

        $row = DB::connection('DATAMIND')
            ->table('dbo.RUEX_INFO')
            ->select([
                'num_filiacion',
                'primerApellido',
                'segundoApellido',
                'primerNombre',
                'segundoNombre',
                'genero',
                'fecha_nacimiento',
                'cod_pais_nacionalidad',
                'cod_pais_nacimiento',
                'pasaporte',
                'telefono',
            ])
            ->where('num_filiacion', $ruex)
            ->first();

        if (!$row) {
            throw ValidationException::withMessages([
                'documento_numero' => 'No se encontró la filiación en el sistema.',
            ]);
        }

        // Mapear sexo y fecha
        $sexo = ucfirst(strtolower(trim((string)($row->genero ?? ''))));

        if (!in_array($sexo, ['Masculino','Femenino'], true)) {
            $sexo = null;
        }

        $fecha = null;

        if (!empty($row->fecha_nacimiento)) {
            $ts = strtotime((string)$row->fecha_nacimiento);
            if ($ts !== false) {
                $fecha = date('Y-m-d', $ts); // YYYY-MM-DD
            }
        }

        // $codNac = trim((string)($row->cod_pais_nacionalidad ?? ''));
        // $codNacim = trim((string)($row->cod_pais_nacimiento ?? ''));

        $codNac   = strtoupper(trim((string)($row->cod_pais_nacionalidad ?? '')));
        $codNacim = strtoupper(trim((string)($row->cod_pais_nacimiento ?? '')));

        $paisNacionalidad = null;
        $paisNacimiento   = null;

        $codigos = collect([$codNac, $codNacim])->filter()->unique()->values();

        $paises = DB::table('paises')
            ->whereIn('cod_pais', $codigos)
            ->where('estatus','Activo')
            ->get()
            ->keyBy('cod_pais');

        $paisNacionalidad = $paises[$codNac] ?? null;
        $paisNacimiento   = $paises[$codNacim] ?? null;

        return response()->json([
            'ok' => true,
            'data' => [
                'primer_nombre'         => $row->primerNombre ?? null,
                'segundo_nombre'        => $row->segundoNombre ?? null,
                'primer_apellido'       => $row->primerApellido ?? null,
                'segundo_apellido'      => $row->segundoApellido ?? null,
                'genero'                => $sexo,
                'fecha_nacimiento'      => $fecha,
                'pais_nacionalidad_id'  => $paises[$codNac]->id ?? null,
                'pais_nacimiento_id'    => $paises[$codNacim]->id ?? null,
                'pasaporte'             => $row->pasaporte ?? null,
                'telefono'              => $row->telefono ?? null,
            ],
        ]);
        
    }

}
