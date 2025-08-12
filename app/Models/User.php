<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;


use DB;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
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

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
    ];
}
