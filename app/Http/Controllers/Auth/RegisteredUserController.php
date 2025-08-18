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
    public function store(Request $request)
    {
        
        // $request->validate([
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //     'password' => ['required', 'confirmed', Rules\Password::defaults()],
        // ]);

        $rules = [
            // Nombres / apellidos
            'primer_nombre'    => ['required','string','max:100'],
            'segundo_nombre'   => ['nullable','string','max:100'],
            'primer_apellido'  => ['required','string','max:100'],
            'segundo_apellido' => ['nullable','string','max:100'],

            // Contacto
            'email'            => ['required','string','email','max:255','unique:users,email'],
            'telefono'         => ['nullable','string','max:25'],

            // Tipo de usuario
            'tipo_usuario'     => ['required', Rule::in(['abogado','solicitante'])],

            // Solicitante (según tu UI, solo Ruex)
            'documento_tipo'   => [Rule::requiredIf(fn()=> $request->tipo_usuario === 'solicitante'), Rule::in(['Ruex'])],
            'documento_numero' => [Rule::requiredIf(fn()=> $request->tipo_usuario === 'solicitante'), 'string','max:50'],

            // Abogado
            'abogado_id'       => [Rule::requiredIf(fn()=> $request->tipo_usuario === 'abogado'), 'string','max:50'],
            'firma_estudio'    => ['nullable','string','max:120'],
            'idoneidad'        => [Rule::requiredIf(fn()=> $request->tipo_usuario === 'abogado'),'file','mimes:pdf,jpg,jpeg,png','max:5120'],

            // Password
            'password'         => ['required','confirmed', Rules\Password::defaults()],
        ];

        $data = $request->validate($rules);

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
            'abogado_id'       => $data['tipo_usuario'] === 'abogado' ? ($data['abogado_id'] ?? null) : null,
            'firma_estudio'    => $data['tipo_usuario'] === 'abogado' ? ($data['firma_estudio'] ?? null) : null,
            'idoneidad_path'   => $idoneidadPath,

            'password'         => Hash::make($data['password']),
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);




        // $user = User::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password),
        // ]);

        // event(new Registered($user));

        // Auth::login($user);

        // return redirect(RouteServiceProvider::HOME);
    }
}
