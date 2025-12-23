<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Serviciosconsulares extends Model
{
    use HasFactory;

    protected $connection = 'atlas';

    protected $table = 'servicios_consulares';
}
