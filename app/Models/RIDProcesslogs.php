<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RIDProcesslogs extends Model
{
    use HasFactory;

    protected $table = 'rid_processlogs';


    protected $fillable = [
        'log_status',
        'message',
        'process_name',
        'extra_info',
    ];
    
    public $timestamps = false;
    
}
