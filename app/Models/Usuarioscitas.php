<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Usuarioscitas extends Model
{
    use HasFactory;

    protected $connection = 'atlas';

    protected $table = 'usuarios_citas';
}
