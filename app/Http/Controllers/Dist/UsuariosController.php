<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use DB;
use Excel;

class UsuariosController extends Controller
{
    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }
}
