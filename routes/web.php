<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;

/*use App\Http\Controllers\Dist\DepartamentoController;
use App\Http\Controllers\Dist\TipoatencionController;
use App\Http\Controllers\Dist\PosicionesController;
use App\Http\Controllers\Dist\ColaboradoresController;
use App\Http\Controllers\Dist\DashboardController;
use App\Http\Controllers\Dist\SolicitudController; */

use App\Http\Controllers\Dist\{
    DashboardController,
    SolicitudController,
    DepartamentoController,
    TipoatencionController,
    PosicionesController,
    ColaboradoresController,
    MotivoController,
    SubmotivoController,

    PaisController,
    ProvinciaController,
    DistritoController,
    CorregimientoController,

    PermisosController,
    UsuariosController,
    PaymentController
};

use App\Http\Controllers\Admin\{
    RIDAfinidadController,
    RIDMigrantesController,
    RIDPuestocontrolController,
    RIDEstaciontemporalController
 
};



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
    Route::get('/', function () {
        return view('welcome');
    }); 


*/



Route::middleware('guest')->group(function () {

    Route::get('/', [AuthenticatedSessionController::class, 'create'])->name('login');

    Route::post('/registro/consulta-filiacion', [RegisteredUserController::class, 'buscarFiliacion'])->name('registro.consultaFiliacion');

    });


    Route::middleware('auth')->group(function () {    

        //dashboard

        // Route::get('/dashboard', function () {
        //     return view('dashboard');
        // })->middleware(['auth'])->name('dashboard');


        Route::get('dashboard', [DashboardController::class, 'Dashboard']) ->name('Dashboard');  
        Route::post('dashboard', [DashboardController::class, 'TotalMigrantes']) ->name('TotalMigrantes');  
        Route::post('dashboard/migrantes-mensual', [DashboardController::class, 'TotalMigrantesMensual']) ->name('TotalMigrantesMensual');  
         Route::post('dashboard/migrantes-semanal', [DashboardController::class, 'TotalMigrantesSemanal']) ->name('TotalMigrantesSemanal');  


        Route::get('dist/dashboard', [DashboardController::class, 'Index']) ->name('Index');  
        Route::get('dist/dashboard/listado', [DashboardController::class, 'PostIndex']) ->name('PostIndex');  

        // solicitud
        Route::prefix('dist/solicitud')->name('solicitud.')->group(function () {

            Route::get('/', [SolicitudController::class, 'Index']) ->name('Index'); 
            Route::post('/', [SolicitudController::class, 'PostIndex']) ->name('PostIndex');
            // Route::get('dist/missolicitudes/{Id}', [SolicitudController::class, 'Missolicitudes']) ->name('Missolicitudes'); 
            // Route::post('dist/missolicitudes/{Id}', [SolicitudController::class, 'PostMissolicitudes']) ->name('PostMissolicitudes'); 

            Route::get('/nuevo', [SolicitudController::class, 'Nuevo']) ->name('Nuevo'); 
            Route::post('/nuevo', [SolicitudController::class, 'PostNuevo']) ->name('PostNuevo'); 

            Route::post('/validar-solicitud', [SolicitudController::class, 'ValidarSolicitud']) ->name('ValidarSolicitud'); 
            
            Route::get('/instruciones', [SolicitudController::class, 'instruciones']) ->name('instruciones'); 

            Route::get('/editar/{Id}', [SolicitudController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [SolicitudController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [SolicitudController::class, 'Mostrar']) ->name('Mostrar');
            Route::post('/desactivar', [SolicitudController::class, 'Desactivar']) ->name('Desactivar'); 
            
            Route::post('/nuevo/buscatipoatencion', [SolicitudController::class, 'postBuscatipoatencion']) ->name('postBuscatipoatencion');
            Route::post('/nuevo/buscamotivo', [SolicitudController::class, 'postBuscamotivo']) ->name('postBuscamotivo');

            Route::post('/buscaDistrito', [DistritoController::class, 'BuscaDistrito'])->name('BuscaDistrito');
            Route::post('/buscaCorregimiento', [CorregimientoController::class, 'BuscaCorregimiento'])->name('BuscaCorregimiento');


        });

        Route::prefix('payment')->name('payment.')->group(function () {
            Route::get('/tokenize', [PaymentController::class, 'showTokenizationForm'])->name('tokenize');
            Route::post('/widget-callback', [PaymentController::class, 'handleWidgetCallback'])->name('handleWidgetCallback');
            Route::post('/process', [PaymentController::class, 'processPayment'])->name('process');
            Route::get('/success', [PaymentController::class, 'paymentSuccess'])->name('success');
            Route::get('/minimal', [PaymentController::class, 'showTokenizationFormMinimal']);
            Route::get('/status', [PaymentController::class, 'checkServiceStatus'])->name('status');
            Route::get('error',   [PaymentController::class, 'paymentError'])->name('error');  // nueva

            
        });
            

        // Departamento
        Route::get('dist/departamento', [DepartamentoController::class, 'Index']) ->name('Index');  
        Route::post('dist/departamento', [DepartamentoController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('dist/departamento/nuevo', [DepartamentoController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/departamento/nuevo', [DepartamentoController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/departamento/editar/{Id}', [DepartamentoController::class, 'Editar']) ->name('Editar');
        Route::post('dist/departamento/editar/{Id}', [DepartamentoController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/departamento/mostrar/{Id}', [DepartamentoController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/departamento/desactivar', [DepartamentoController::class, 'Desactivar']) ->name('Desactivar');
    
        // Tipo de Atencion
        Route::get('dist/tipoatencion', [TipoatencionController::class, 'Index']) ->name('Index');  
        Route::post('dist/tipoatencion', [TipoatencionController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('dist/tipoatencion/nuevo', [TipoatencionController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/tipoatencion/nuevo', [TipoatencionController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/tipoatencion/editar/{Id}', [TipoatencionController::class, 'Editar']) ->name('Editar');
        Route::post('dist/tipoatencion/editar/{Id}', [TipoatencionController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/tipoatencion/mostrar/{Id}', [TipoatencionController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/tipoatencion/desactivar', [TipoatencionController::class, 'Desactivar']) ->name('Desactivar');

        // Motivo
        Route::get('dist/motivo', [MotivoController::class, 'Index']) ->name('Index');  
        Route::post('dist/motivo', [MotivoController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('dist/motivo/nuevo', [MotivoController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/motivo/nuevo', [MotivoController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/motivo/editar/{Id}', [MotivoController::class, 'Editar']) ->name('Editar');
        Route::post('dist/motivo/editar/{Id}', [MotivoController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/motivo/mostrar/{Id}', [MotivoController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/motivo/desactivar', [MotivoController::class, 'Desactivar']) ->name('Desactivar');

        // SubMotivo
        Route::get('dist/submotivo', [SubmotivoController::class, 'Index']) ->name('Index');  
        Route::post('dist/submotivo', [SubmotivoController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('dist/submotivo/nuevo', [SubmotivoController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/submotivo/nuevo', [SubmotivoController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/submotivo/editar/{Id}', [SubmotivoController::class, 'Editar']) ->name('Editar');
        Route::post('dist/submotivo/editar/{Id}', [SubmotivoController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/submotivo/mostrar/{Id}', [SubmotivoController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/submotivo/desactivar', [SubmotivoController::class, 'Desactivar']) ->name('Desactivar');

         //posiciones
         Route::get('dist/posiciones', [PosicionesController::class, 'Index']) ->name('Index'); 
         Route::post('dist/posiciones', [PosicionesController::class, 'PostIndex']) ->name('PostIndex'); 
         Route::get('dist/posiciones/nuevo', [PosicionesController::class, 'Nuevo']) ->name('Nuevo'); 
         Route::post('dist/posiciones/nuevo', [PosicionesController::class, 'PostNuevo']) ->name('PostNuevo'); 
         Route::get('dist/posiciones/editar/{Id}', [PosicionesController::class, 'Editar']) ->name('Editar');
         Route::post('dist/posiciones/editar/{Id}', [PosicionesController::class, 'PostEditar']) ->name('PostEditar'); 
         Route::get('dist/posiciones/mostrar/{Id}', [PosicionesController::class, 'Mostrar']) ->name('Mostrar');
         Route::post('dist/posiciones/desactivar', [PosicionesController::class, 'Desactivar']) ->name('Desactivar');

        //Colaboradores
        Route::get('dist/colaboradores', [ColaboradoresController::class, 'Index']) ->name('Index'); 
        Route::post('dist/colaboradores', [ColaboradoresController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('dist/colaboradores/nuevo', [ColaboradoresController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('dist/colaboradores/nuevo', [ColaboradoresController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('dist/colaboradores/editar/{Id}', [ColaboradoresController::class, 'Editar']) ->name('Editar');
        Route::post('dist/colaboradores/editar/{Id}', [ColaboradoresController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('dist/colaboradores/mostrar/{Id}', [ColaboradoresController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('dist/colaboradores/desactivar', [ColaboradoresController::class, 'Desactivar']) ->name('Desactivar');

        Route::post('dist/colaboradores/nuevo/buscadistrito', [ColaboradoresController::class, 'postBuscadistrito']) ->name('postBuscadistrito');
        Route::post('dist/colaboradores/nuevo/buscaposiciones', [ColaboradoresController::class, 'postBuscaposiciones']) ->name('postBuscaposiciones');



        //PAIS
        Route::get('dist/pais', [PaisController::class, 'Index']) ->name('Index'); 
        Route::post('dist/pais', [PaisController::class, 'PostIndex']) ->name('PostIndex'); 
        /*Route::get('dist/organizacion/importar', [OrganizacionController::class, 'Importar']) ->name('Importar'); 
        Route::post('dist/organizacion/importar', [OrganizacionController::class, 'PostImportar']) ->name('PostImportar'); 
        */

        // PROVINCIA
        Route::get('dist/provincia', [ProvinciaController::class, 'Index']) ->name('Index'); 
        Route::post('dist/provincia', [ProvinciaController::class, 'PostIndex']) ->name('PostIndex'); 
        
        //DISTRITO
        Route::get('dist/distrito', [DistritoController::class, 'Index']) ->name('Index'); 
        Route::post('dist/distrito', [DistritoController::class, 'PostIndex']) ->name('PostIndex'); 
        
        //CORREGIMIENTO
        Route::get('dist/corregimiento', [CorregimientoController::class, 'Index']) ->name('Index'); 
        Route::post('dist/corregimiento', [CorregimientoController::class, 'PostIndex']) ->name('PostIndex'); 



        //CORREGIMIENTO
        Route::get('dist/corregimiento', [CorregimientoController::class, 'Index']) ->name('Index'); 
        Route::post('dist/corregimiento', [CorregimientoController::class, 'PostIndex']) ->name('PostIndex'); 

        //PERMISOS
        Route::get('dist/permisos', [PermisosController::class, 'Index']) ->name('Index'); 
        Route::post('dist/permisos', [PermisosController::class, 'PostIndex']) ->name('PostIndex'); 

        //USUARIOS
        Route::get('dist/usuarios', [UsuariosController::class, 'Index']) ->name('Index'); 
        Route::post('dist/usuarios', [UsuariosController::class, 'PostIndex']) ->name('PostIndex'); 


        //RID MIGRANTES
        Route::get('admin/RIDmigrantes', [RIDMigrantesController::class, 'Index']) ->name('Index'); 
        Route::post('admin/RIDmigrantes', [RIDMigrantesController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('admin/RIDmigrantes/nuevo', [RIDMigrantesController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('admin/RIDmigrantes/nuevo', [RIDMigrantesController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('admin/RIDmigrantes/editar/{Id}', [RIDMigrantesController::class, 'Editar']) ->name('Editar');
        Route::post('admin/RIDmigrantes/editar/{Id}', [RIDMigrantesController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('admin/RIDmigrantes/mostrar/{Id}', [RIDMigrantesController::class, 'Mostrar']) ->name('Mostrar');
        Route::post('admin/RIDmigrantes/migranteinformacion', [RIDMigrantesController::class, 'Informacion']) ->name('Informacion'); 
        Route::get('admin/RIDmigrantes/migrantereporte/{Id}', [RIDMigrantesController::class, 'Reporte']) ->name('Reporte'); 

        //RID PUESTO DE CONTROL
        Route::get('admin/RIDpuestocontrol', [RIDPuestocontrolController::class, 'Index']) ->name('Index'); 
        Route::post('admin/RIDpuestocontrol', [RIDPuestocontrolController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('admin/RIDpuestocontrol/nuevo', [RIDPuestocontrolController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('admin/RIDpuestocontrol/nuevo', [RIDPuestocontrolController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('admin/RIDpuestocontrol/editar/{Id}', [RIDPuestocontrolController::class, 'Editar']) ->name('Editar');
        Route::post('admin/RIDpuestocontrol/editar/{Id}', [RIDPuestocontrolController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('admin/RIDpuestocontrol/mostrar/{Id}', [RIDPuestocontrolController::class, 'Mostrar']) ->name('Mostrar');

        //RID PUESTO DE CONTROL
        Route::get('admin/RIDestaciontemporal', [RIDEstaciontemporalController::class, 'Index']) ->name('Index'); 
        Route::post('admin/RIDestaciontemporal', [RIDEstaciontemporalController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('admin/RIDestaciontemporal/nuevo', [RIDEstaciontemporalController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('admin/RIDestaciontemporal/nuevo', [RIDEstaciontemporalController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('admin/RIDestaciontemporal/editar/{Id}', [RIDEstaciontemporalController::class, 'Editar']) ->name('Editar');
        Route::post('admin/RIDestaciontemporal/editar/{Id}', [RIDEstaciontemporalController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('admin/RIDestaciontemporal/mostrar/{Id}', [RIDEstaciontemporalController::class, 'Mostrar']) ->name('Mostrar');

        //RID AFINIDAD
        Route::get('admin/RIDafinidad', [RIDAfinidadController::class, 'Index']) ->name('Index'); 
        Route::post('admin/RIDafinidad', [RIDAfinidadController::class, 'PostIndex']) ->name('PostIndex'); 
        Route::get('admin/RIDafinidad/nuevo', [RIDAfinidadController::class, 'Nuevo']) ->name('Nuevo'); 
        Route::post('admin/RIDafinidad/nuevo', [RIDAfinidadController::class, 'PostNuevo']) ->name('PostNuevo'); 
        Route::get('admin/RIDafinidad/editar/{Id}', [RIDAfinidadController::class, 'Editar']) ->name('Editar');
        Route::post('admin/RIDafinidad/editar/{Id}', [RIDAfinidadController::class, 'PostEditar']) ->name('PostEditar'); 
        Route::get('admin/RIDafinidad/mostrar/{Id}', [RIDAfinidadController::class, 'Mostrar']) ->name('Mostrar');

        //Pagos
       // Route::get('/tokenize', [PaymentController::class, 'showTokenizationForm'])->name('tokenize');
        //Route::post('/widget-callback', [PaymentController::class, 'handleWidgetCallback'])->name('handleWidgetCallback');
        //Route::post('/process', [PaymentController::class, 'processPayment'])->name('process');
        //Route::get('/success', [PaymentController::class, 'paymentSuccess'])->name('success');
      
    });

    


// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth'])->name('dashboard');

require __DIR__.'/auth.php';
