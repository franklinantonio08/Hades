<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use DB;
use Illuminate\Support\Facades\Auth;


class Usuario extends Model
{
    use HasFactory;

    protected $table = 'users';
    
}
