<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            
            'genero'           => ['exclude_unless:tipo_usuario,solicitante','required','in:M,F'],
            'fecha_nacimiento' => ['exclude_unless:tipo_usuario,solicitante','required','date','before:today'],

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
        ];


        $data = $request->validate($rules);

        // (Opcional) Verificación fuerte contra SIM cuando es solicitante:
        if ($data['tipo_usuario'] === 'solicitante') {
            
            $sim = DB::connection('simpanama')
                ->table('dbo.SIM_FI_GENERALES')
                ->select([
                    'NUM_REG_FILIACION',
                    'NOM_PRIMER_APELL','NOM_SEGUND_APELL',
                    'NOM_PRIMER_NOMB','NOM_SEGUND_NOMB',
                    'IND_SEXO','FEC_NACIM',
                ])
                ->where('NUM_REG_FILIACION', $data['documento_numero'])
                ->first();

            if (!$sim) {
                throw ValidationException::withMessages([
                    'documento_numero' => 'No se encontró la filiación en el sistema.',
                ]);
            }

            // Comparaciones simples (puedes endurecerlas si quieres)
            $simPN = strtoupper(trim($sim->NOM_PRIMER_NOMB ?? ''));
            $simPA = strtoupper(trim($sim->NOM_PRIMER_APELL ?? ''));
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
            $sexoSim = strtoupper((string)($sim->IND_SEXO ?? ''));            
            $fechaSim = null;
            if (!empty($sim->FEC_NACIM)) {
                $ts = strtotime((string)$sim->FEC_NACIM);
                if ($ts !== false) {
                    $fechaSim = date('Y-m-d', $ts); // YYYY-MM-DD
                }
            }

            if (in_array($sexoSim, ['M','F'], true)) {
                $data['genero'] = $sexoSim;
            }
            if ($fechaSim) {
                $data['fecha_nacimiento'] = $fechaSim;
            }

            // Guardar archivo de idoneidad si aplica
            $idoneidadPath = null;
            if ($data['tipo_usuario'] === 'abogado' && $request->hasFile('idoneidad')) {
                $idoneidadPath = $request->file('idoneidad')->store('idoneidades', 'public');
            }

            $fullName = trim(
                ($data['primer_nombre'] ?? '').' '.
                ($data['segundo_nombre'] ?? '').' '.
                ($data['primer_apellido'] ?? '').' '.
                ($data['segundo_apellido'] ?? '')
            );

            $user = User::create([
                // Nombres desglosados + name
                'primer_nombre'    => $data['primer_nombre'],
                'segundo_nombre'   => $data['segundo_nombre'] ?? null,
                'primer_apellido'  => $data['primer_apellido'],
                'segundo_apellido' => $data['segundo_apellido'] ?? null,
                'name'             => $fullName,

                // Contacto
                'email'            => $data['email'],
                'telefono'         => $data['telefono'] ?? null,

                // Tipo y campos condicionales
                'tipo_usuario'     => $data['tipo_usuario'],
                'documento_tipo'   => $data['tipo_usuario'] === 'solicitante' ? ($data['documento_tipo'] ?? null) : null,
                'documento_numero' => $data['tipo_usuario'] === 'solicitante' ? ($data['documento_numero'] ?? null) : null,
                'genero'           => $data['tipo_usuario'] === 'solicitante' ? ($data['genero'] ?? null) : null,
                'fecha_nacimiento' => $data['tipo_usuario'] === 'solicitante' ? ($data['fecha_nacimiento'] ?? null) : null,
    
                'abogado_id'       => $data['tipo_usuario'] === 'abogado' ? ($data['abogado_id'] ?? null) : null,
                'firma_estudio'    => $data['tipo_usuario'] === 'abogado' ? ($data['firma_estudio'] ?? null) : null,
                'idoneidad_path'   => $idoneidadPath,

                'password'         => Hash::make($data['password']),
            ]);

            event(new Registered($user));
            Auth::login($user);

            if ($request->wantsJson()) {
                return response()->json([
                    'ok'       => true,
                    'redirect' => url(RouteServiceProvider::HOME),
                ]);
            }

            return redirect(RouteServiceProvider::HOME);


        }
    }

    public function buscarFiliacion(Request $request)
{
    $validated = $request->validate([
        'documento_tipo'   => ['required', Rule::in(['Ruex'])],
        'documento_numero' => ['required', 'string', 'max:50'],
    ]);

    $ruex = trim($validated['documento_numero']);

    $row = DB::connection('sqlsrv_sim')
        ->table('dbo.SIM_FI_GENERALES')
        ->select([
            'NUM_REG_FILIACION',
            'NOM_PRIMER_APELL',
            'NOM_SEGUND_APELL',
            'NOM_PRIMER_NOMB',
            'NOM_SEGUND_NOMB',
            'IND_SEXO',
            'FEC_NACIM',
        ])
        ->where('NUM_REG_FILIACION', $ruex)
        ->first();

    if (!$row) {
        throw ValidationException::withMessages([
            'documento_numero' => 'No se encontró la filiación en el sistema.',
        ]);
    }

    // Mapear sexo y fecha
    $sexo = strtoupper((string)($row->IND_SEXO ?? ''));
    if (!in_array($sexo, ['M','F'], true)) {
        $sexo = null;
    }

    $fecha = null;
    if (!empty($row->FEC_NACIM)) {
        $ts = strtotime((string)$row->FEC_NACIM);
        if ($ts !== false) {
            $fecha = date('Y-m-d', $ts); // YYYY-MM-DD
        }
    }

    return response()->json([
        'ok' => true,
        'data' => [
            'primer_nombre'    => $row->NOM_PRIMER_NOMB ?? null,
            'segundo_nombre'   => $row->NOM_SEGUND_NOMB ?? null,
            'primer_apellido'  => $row->NOM_PRIMER_APELL ?? null,
            'segundo_apellido' => $row->NOM_SEGUND_APELL ?? null,
            'genero'           => $sexo,
            'fecha_nacimiento' => $fecha,
        ],
    ]);
}


}
