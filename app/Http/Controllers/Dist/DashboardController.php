<?php

namespace App\Http\Controllers\Dist;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;

use App\Models\Dashboard;
use App\Models\Cubiculo;
use App\Models\Solicitud;
use App\Models\RIDMigrantes;

use DB;
use Excel;

class DashboardController extends Controller
{
    //


    private $request;
    private $common;

    public function __construct(Request $request){
        $this->request = $request;
    }

    public function Dashboard(){

        $year = date('Y'); // Obtiene el año actual

        // $totalSolicitudes = DB::table('solicitud')
        // ->whereYear('fechaAtencion', $year)
        // ->count();

        // view()->share('totalSolicitudes', $totalSolicitudes);	

        $resultados = DB::table('rid_migrante')
            ->selectRaw('COUNT(*) as totalMigrantes,
                         DATE_FORMAT(MIN(created_at), "%M") as primeraMigranteMes,
                         DATE_FORMAT(MAX(created_at), "%M") as ultimaMigranteMes')
            ->whereYear('created_at', $year)
            ->first();
        

        $totalMigrantes = $resultados->totalMigrantes;
        $primeraSolicitud = $resultados->primeraMigranteMes;
        $ultimaSolicitud = $resultados->ultimaMigranteMes;

       

       //return \view('dashboard');

       return view('layouts.app', [
        'year' => $year,
        'totalMigrantes' => $totalMigrantes,
        'primeraSolicitud' => $primeraSolicitud,
        'ultimaSolicitud' => $ultimaSolicitud,
    ]);

    }

    public function Index(){

        $cubiculo = DB::table('cubiculo')
        ->where('cubiculo.estatus', '=', 'Activo')
        //->where('organizacionId', '=', '1')
        ->leftjoin('solicitud', 'solicitud.id', '=', 'cubiculo.solicitudId')
        ->leftjoin('colaboradores', 'colaboradores.id', '=', 'cubiculo.funcionarioId')
        ->select('cubiculo.id', 'cubiculo.codigo', 'cubiculo.llamado',
       // DB::raw("SUBSTRING(departamento.codigo, 1, 1) as codigo_departamento"),
         DB::raw('SUBSTRING(colaboradores.cubico,8,1) as posicion'))
        ->get();
        //return  $solicitud;

		if(empty($cubiculo)){
    		return redirect('dist/dashboard')->withErrors("ERROR LA PROVINCIA ESTA VACIA CODE-0226");
    	}	
		view()->share('cubiculo', $cubiculo);	


        return \view('dist/dashboard/index');
    }

    public function PostIndex(){


        $cubiculo = DB::table('cubiculo')
        ->where('cubiculo.estatus', '=', 'Activo')
        ->where('cubiculo.llamado', '<=', '3')
        //->leftjoin('solicitud', 'solicitud.id', '=', 'cubiculo.solicitudId')
        //->leftjoin('colaboradores', 'colaboradores.id', '=', 'cubiculo.funcionarioId')
        //->select('cubiculo.id', 'cubiculo.codigo', 'cubiculo.llamado',
       // DB::raw("SUBSTRING(departamento.codigo, 1, 1) as codigo_departamento"),
         //DB::raw('(ROW_NUMBER() OVER (ORDER BY cubiculo.id)) as posicion'))
         //DB::raw('SUBSTRING(colaboradores.cubico,8,1) as posicion'))
        ->get();

        foreach ($cubiculo as $key => $value) {
            $cubiculoId = $value->id;
            $cubiculollamado = $value->llamado + 1;
    
            // Actualiza el campo llamado en la tabla cubiculo
            DB::table('cubiculo')
                ->where('id', $cubiculoId)
                ->update(['llamado' => $cubiculollamado]);
        }

        $cubiculo = DB::table('cubiculo')
        ->where('cubiculo.estatus', '=', 'Activo')
       // ->where('cubiculo.llamado', '<', '3')
        ->leftjoin('solicitud', 'solicitud.id', '=', 'cubiculo.solicitudId')
        ->leftjoin('colaboradores', 'colaboradores.id', '=', 'cubiculo.funcionarioId')
        ->select('cubiculo.id', 'cubiculo.codigo', 'cubiculo.llamado',
       // DB::raw("SUBSTRING(departamento.codigo, 1, 1) as codigo_departamento"),
         //DB::raw('(ROW_NUMBER() OVER (ORDER BY cubiculo.id)) as posicion'))
         DB::raw('SUBSTRING(colaboradores.cubico,8,1) as posicion')
         )
        ->get();

       // return $cubiculo;

        return view('dist/dashboard/listado', compact('cubiculo'));


    }

    public function TotalMigrantes(Request $request)
    {
        $currentYear = date('Y');

        $results = RIDMigrantes::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTHNAME(created_at) as month_name'),
                DB::raw('COUNT(*) as total_migrants')
            )
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'), DB::raw('MONTHNAME(created_at)'))
            ->orderBy(DB::raw('YEAR(created_at)'), 'asc') // Asegúrate de usar 'asc' o 'desc'
            ->orderBy(DB::raw('MONTH(created_at)'), 'asc') // Asegúrate de usar 'asc' o 'desc'
            ->get();

        // Depuración: ver los resultados obtenidos
        //return response()->json($results); 

        $labels = [];
        $data = [];

        foreach ($results as $result) {
            $labels[] = $result->month_name;
            $data[] = $result->total_migrants;
        }

        $responseData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Migrantes',
                    'backgroundColor' => 'rgba(0, 123, 255, .1)',
                    'borderColor' => '#007bff',
                    'borderWidth' => 3,
                    'data' => $data,
                    'fill' => true
                ]
            ]
        ];

        return response()->json($responseData);
    }
  



public function TotalMigrantesMensual()
{
    $results = DB::table('rid_migrante')
        ->select(
            DB::raw('YEAR(created_at) as year'),
            DB::raw('MONTH(created_at) as month'),
            DB::raw('MONTHNAME(created_at) as month_name'),
            DB::raw('COUNT(*) as total_migrants')
        )
        ->whereYear('created_at', 2024)
        ->orWhereYear('created_at', 2023)
        ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'), DB::raw('MONTHNAME(created_at)'))
        ->get();

    $data = [
        'labels' => [],
        'datasets' => [
            [
                'label' => '2023',
                'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                'borderColor' => 'rgba(75, 192, 192, 1)',
                'borderWidth' => 1,
                'data' => []
            ],
            [
                'label' => '2024',
                'backgroundColor' => 'rgba(153, 102, 255, 0.2)',
                'borderColor' => 'rgba(153, 102, 255, 1)',
                'borderWidth' => 1,
                'data' => []
            ]
        ]
    ];

    foreach ($results as $result) {
        if (!in_array($result->month_name, $data['labels'])) {
            $data['labels'][] = $result->month_name;
        }

        if ($result->year == 2023) {
            $data['datasets'][0]['data'][] = $result->total_migrants;
        } else {
            $data['datasets'][1]['data'][] = $result->total_migrants;
        }
    }

    return response()->json($data);
}

public function TotalMigrantesSemanal()
{
    $currentYear = date('Y');
    $currentMonth = date('m');
    $currentWeekStart = date('Y-m-d H:i:s', strtotime('monday this week'));
    $currentWeekEnd = date('Y-m-d H:i:s', strtotime('sunday this week 23:59:59'));
    $lastWeekStart = date('Y-m-d H:i:s', strtotime('monday last week'));
    $lastWeekEnd = date('Y-m-d H:i:s', strtotime('sunday last week 23:59:59'));

    // Total de migrantes del último año por día de la semana
    $yearlyResults = DB::table('rid_migrante')
        ->select(
            DB::raw('DAYOFWEEK(created_at) as day_of_week'),
            DB::raw('DAYNAME(created_at) as day_name'),
            DB::raw('COUNT(*) as total_migrants')
        )
        ->whereYear('created_at', $currentYear)
        ->groupBy(DB::raw('DAYOFWEEK(created_at)'), DB::raw('DAYNAME(created_at)'))
        ->get();

    // Total de migrantes de la semana pasada por día de la semana
    $weeklyResults = DB::table('rid_migrante')
        ->select(
            DB::raw('DAYOFWEEK(created_at) as day_of_week'),
            DB::raw('DAYNAME(created_at) as day_name'),
            DB::raw('COUNT(*) as total_migrants')
        )
        ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
        ->groupBy(DB::raw('DAYOFWEEK(created_at)'), DB::raw('DAYNAME(created_at)'))
        ->get();

    // Totales adicionales
    $totalYearly = DB::table('rid_migrante')
        ->whereYear('created_at', $currentYear)
        ->count();

    $totalMonthly = DB::table('rid_migrante')
        ->whereYear('created_at', $currentYear)
        ->whereMonth('created_at', $currentMonth)
        ->count();

    $totalLastWeek = DB::table('rid_migrante')
        ->whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])
        ->count();

    $totalCurrentWeek = DB::table('rid_migrante')
        ->whereBetween('created_at', [$currentWeekStart, $currentWeekEnd])
        ->count();

    // Total por género
    $genderTotals = DB::table('rid_migrante')
        ->select('genero', DB::raw('COUNT(*) as total'))
        ->whereYear('created_at', $currentYear)
        ->groupBy('genero')
        ->get();

    $totalMasculino = $genderTotals->where('genero', 'Masculino')->first()->total ?? 0;
    $totalFemenino = $genderTotals->where('genero', 'Femenino')->first()->total ?? 0;
    $totalMigrantes = $totalMasculino + $totalFemenino;

    $porcentajeMasculino = $totalMigrantes ? ($totalMasculino / $totalMigrantes) * 100 : 0;
    $porcentajeFemenino = $totalMigrantes ? ($totalFemenino / $totalMigrantes) * 100 : 0;

     // Calcular grupos de edad
    
    $ageGroups = DB::table('rid_migrante')
    ->select(DB::raw("
        CASE
            WHEN TIMESTAMPDIFF(YEAR, fechaNacimiento, CURDATE()) BETWEEN 0 AND 6 THEN '0-6'
            WHEN TIMESTAMPDIFF(YEAR, fechaNacimiento, CURDATE()) BETWEEN 7 AND 17 THEN '7-17'
            WHEN TIMESTAMPDIFF(YEAR, fechaNacimiento, CURDATE()) BETWEEN 18 AND 27 THEN '18-27'
            WHEN TIMESTAMPDIFF(YEAR, fechaNacimiento, CURDATE()) BETWEEN 28 AND 37 THEN '28-37'
            WHEN TIMESTAMPDIFF(YEAR, fechaNacimiento, CURDATE()) BETWEEN 38 AND 47 THEN '38-47'
            WHEN TIMESTAMPDIFF(YEAR, fechaNacimiento, CURDATE()) BETWEEN 48 AND 57 THEN '48-57'
            WHEN TIMESTAMPDIFF(YEAR, fechaNacimiento, CURDATE()) BETWEEN 58 AND 84 THEN '58-84'
            ELSE '85+'
        END as age_group,
        COUNT(*) as total_migrants
    "))
    ->whereYear('created_at', $currentYear)
    ->groupBy('age_group')
    ->orderByRaw("
        FIELD(age_group, '0-6', '7-17', '18-27', '28-37', '38-47', '48-57', '58-84', '85+')
    ")
    ->get();

    return response()->json([
        'yearly' => $yearlyResults,
        'weekly' => $weeklyResults,
        'totals' => [
            'totalYearly' => $totalYearly,
            'totalMonthly' => $totalMonthly,
            'totalLastWeek' => $totalLastWeek,
            'totalCurrentWeek' => $totalCurrentWeek,
        ],
        'gender' => [
            'totalMasculino' => $totalMasculino,
            'totalFemenino' => $totalFemenino,
            'porcentajeMasculino' => $porcentajeMasculino,
            'porcentajeFemenino' => $porcentajeFemenino,
        ],
        'ageGroups' => $ageGroups,
    ]);
}


    
}
