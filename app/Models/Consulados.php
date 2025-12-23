<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consulados extends Model
{
    use HasFactory;

    protected $connection = 'atlas';

    protected $table = 'consulado';
}
