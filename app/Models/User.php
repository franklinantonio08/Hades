<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Notifications\VerifyEmailCustom;
use App\Notifications\CustomResetPassword;
use Illuminate\Support\Facades\URL;


use DB;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    public static function usuariopermiso($codigoPermiso){

        $usuariopermiso = DB::table('usuario_permiso')
        ->where('usuario_permiso.usuarioId', '=', Auth::user()->id)
        ->where('usuario_permiso.codigo', '=', $codigoPermiso)
        ->select('usuario_permiso.valor')->first();

        if(empty($usuariopermiso)){
            return false;
        }
        if($usuariopermiso->valor != 'TRUE'){
            return false;
        }

        return true;
    }

    public function sendEmailVerificationNotification(){

        $this->notify(new VerifyEmailCustom);

    }

    public function sendPasswordResetNotification($token){
        // URL estándar del formulario de reset
        $resetUrl = route('password.reset', [
            'token' => $token,
            'email' => $this->getEmailForPasswordReset(),
        ]);

        // Envía tu notificación custom basada en vista HTML en /mails/
        $this->notify(new CustomResetPassword($resetUrl));
    }



    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'name', 
        'email',
        'password',
        'telefono',
        'tipo_usuario',
        'documento_tipo',
        'documento_numero',
        'abogado_id',
        'firma_estudio',
        'idoneidad_path',
        'genero',
        'fecha_nacimiento',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];
}
