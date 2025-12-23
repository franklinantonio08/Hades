<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;

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
    PaymentController,
    CitasconsularController,
    VisasController,
    FiliacionController,

};

use App\Http\Controllers\Admin\{
    RIDAfinidadController,
    RIDMigrantesController,
    RIDPuestocontrolController,
    RIDEstaciontemporalController,
    
 
 
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

            Route::post('/seleccion-familiar', [SolicitudController::class, 'SeleccionFamiliar'])->name('SeleccionFamiliar');

            Route::post('/validar-solicitud', [SolicitudController::class, 'ValidarSolicitud']) ->name('ValidarSolicitud'); 
            Route::post('/validar-filiacion-activa', [SolicitudController::class, 'validarFiliacionActiva']) ->name('validarFiliacionActiva'); 

            

            Route::post('/seleccion-familiar', [SolicitudController::class, 'SeleccionFamiliar'])->name('SeleccionFamiliar');

            Route::post('/validar-solicitud', [SolicitudController::class, 'ValidarSolicitud']) ->name('ValidarSolicitud'); 
            Route::post('/validar-filiacion-activa', [SolicitudController::class, 'validarFiliacionActiva']) ->name('validarFiliacionActiva'); 

            
            
            Route::get('/instruciones', [SolicitudController::class, 'instruciones']) ->name('instruciones'); 

            Route::get('/editar/{Id}', [SolicitudController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [SolicitudController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [SolicitudController::class, 'Mostrar']) ->name('Mostrar');
            Route::post('/desactivar', [SolicitudController::class, 'Desactivar']) ->name('Desactivar'); 
            
            Route::post('/nuevo/buscatipoatencion', [SolicitudController::class, 'postBuscatipoatencion']) ->name('postBuscatipoatencion');
            Route::post('/nuevo/buscamotivo', [SolicitudController::class, 'postBuscamotivo']) ->name('postBuscamotivo');

            Route::post('/buscaDistrito', [DistritoController::class, 'BuscaDistrito'])->name('BuscaDistrito');
            Route::post('/buscaCorregimiento', [CorregimientoController::class, 'BuscaCorregimiento'])->name('BuscaCorregimiento');
            Route::post('/buscafamiliar', [SolicitudController::class, 'BuscaFamiliar']) ->name('BuscaFamiliar'); 

             Route::get('/pago/{Id}', [SolicitudController::class, 'Pago']) ->name('Pago'); 

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

        Route::prefix('dist/citas_consular')->name('citas_consular.')->group(function () {
            Route::get('/', [CitasconsularController::class, 'Index'])->name('Index');
            Route::post('/', [CitasconsularController::class, 'PostIndex'])->name('PostIndex');
            Route::get('/nuevo', [CitasconsularController::class, 'Nuevo'])->name('Nuevo');
            Route::post('/nuevo', [CitasconsularController::class, 'PostNuevo'])->name('PostNuevo');
            // Route::post('/nuevo/buscaServicios', [ServiciosconsularesController::class, 'BuscaServiciosconsulares'])->name('BuscaServiciosconsulares');
            Route::post('/nuevo/buscaConsulados', [CitasconsularController::class, 'buscaConsulados'])->name('buscaConsulados');
            Route::get('/editar/{Id}', [CitasconsularController::class, 'Editar'])->name('Editar');
            Route::post('/editar/{Id}', [CitasconsularController::class, 'PostEditar'])->name('PostEditar');
            Route::get('/mostrar/{Id}', [CitasconsularController::class, 'Mostrar'])->name('Mostrar');
            Route::post('/desactivar', [CitasconsularController::class, 'Desactivar'])->name('Desactivar');
            Route::get('/generar-pdf', [CitasconsularController::class, 'generarPDF'])->name('generarPDF');
            Route::get('/mostrar-pdf/{Id}', [CitasconsularController::class, 'mostrarPDF'])->name('mostrarPDF');
        });
            

        Route::prefix('dist/citas_consular')->name('citas_consular.')->group(function () {
            Route::get('/', [DepartamentoController::class, 'Index']) ->name('Index');  
            Route::post('/', [DepartamentoController::class, 'PostIndex']) ->name('PostIndex'); 
            Route::get('/nuevo', [DepartamentoController::class, 'Nuevo']) ->name('Nuevo'); 
            Route::post('/nuevo', [DepartamentoController::class, 'PostNuevo']) ->name('PostNuevo'); 
            Route::get('/editar/{Id}', [DepartamentoController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [DepartamentoController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [DepartamentoController::class, 'Mostrar']) ->name('Mostrar');
            Route::post('/desactivar', [DepartamentoController::class, 'Desactivar']) ->name('Desactivar');
        });
               
    
        Route::prefix('dist/tipoatencion')->name('tipoatencion.')->group(function () {
            Route::get('/', [TipoatencionController::class, 'Index']) ->name('Index');  
            Route::post('/', [TipoatencionController::class, 'PostIndex']) ->name('PostIndex'); 
            Route::get('/nuevo', [TipoatencionController::class, 'Nuevo']) ->name('Nuevo'); 
            Route::post('/nuevo', [TipoatencionController::class, 'PostNuevo']) ->name('PostNuevo'); 
            Route::get('/editar/{Id}', [TipoatencionController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [TipoatencionController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [TipoatencionController::class, 'Mostrar']) ->name('Mostrar');
            Route::post('/desactivar', [TipoatencionController::class, 'Desactivar']) ->name('Desactivar');
        });

        Route::prefix('dist/motivo')->name('motivo.')->group(function () {
            Route::get('/', [MotivoController::class, 'Index']) ->name('Index');  
            Route::post('/', [MotivoController::class, 'PostIndex']) ->name('PostIndex'); 
            Route::get('/nuevo', [MotivoController::class, 'Nuevo']) ->name('Nuevo'); 
            Route::post('/nuevo', [MotivoController::class, 'PostNuevo']) ->name('PostNuevo'); 
            Route::get('/editar/{Id}', [MotivoController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [MotivoController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [MotivoController::class, 'Mostrar']) ->name('Mostrar');
            Route::post('/desactivar', [MotivoController::class, 'Desactivar']) ->name('Desactivar');
        });

        Route::prefix('dist/submotivo')->name('submotivo.')->group(function () {
            Route::get('/', [SubmotivoController::class, 'Index']) ->name('Index');  
            Route::post('/', [SubmotivoController::class, 'PostIndex']) ->name('PostIndex'); 
            Route::get('/nuevo', [SubmotivoController::class, 'Nuevo']) ->name('Nuevo'); 
            Route::post('/nuevo', [SubmotivoController::class, 'PostNuevo']) ->name('PostNuevo'); 
            Route::get('/editar/{Id}', [SubmotivoController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [SubmotivoController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [SubmotivoController::class, 'Mostrar']) ->name('Mostrar');
            Route::post('/desactivar', [SubmotivoController::class, 'Desactivar']) ->name('Desactivar');
        });

        Route::prefix('dist/posiciones')->name('posiciones.')->group(function () {
            Route::get('/', [PosicionesController::class, 'Index']) ->name('Index'); 
            Route::post('/', [PosicionesController::class, 'PostIndex']) ->name('PostIndex'); 
            Route::get('/nuevo', [PosicionesController::class, 'Nuevo']) ->name('Nuevo'); 
            Route::post('/nuevo', [PosicionesController::class, 'PostNuevo']) ->name('PostNuevo'); 
            Route::get('/editar/{Id}', [PosicionesController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [PosicionesController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [PosicionesController::class, 'Mostrar']) ->name('Mostrar');
            Route::post('/desactivar', [PosicionesController::class, 'Desactivar']) ->name('Desactivar');
        });

        Route::prefix('dist/colaboradores')->name('colaboradores.')->group(function () {
            Route::get('/', [ColaboradoresController::class, 'Index']) ->name('Index'); 
            Route::post('/', [ColaboradoresController::class, 'PostIndex']) ->name('PostIndex'); 
            Route::get('/nuevo', [ColaboradoresController::class, 'Nuevo']) ->name('Nuevo'); 
            Route::post('/nuevo', [ColaboradoresController::class, 'PostNuevo']) ->name('PostNuevo'); 
            Route::get('/editar/{Id}', [ColaboradoresController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [ColaboradoresController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [ColaboradoresController::class, 'Mostrar']) ->name('Mostrar');
            Route::post('/desactivar', [ColaboradoresController::class, 'Desactivar']) ->name('Desactivar');

            Route::post('/nuevo/buscadistrito', [ColaboradoresController::class, 'postBuscadistrito']) ->name('postBuscadistrito');
            Route::post('/nuevo/buscaposiciones', [ColaboradoresController::class, 'postBuscaposiciones']) ->name('postBuscaposiciones');
        });



        Route::prefix('dist/pais')->name('pais.')->group(function () {
            Route::get('/', [PaisController::class, 'Index']) ->name('Index'); 
            Route::post('/', [PaisController::class, 'PostIndex']) ->name('PostIndex'); 
        });
        /*Route::get('dist/organizacion/importar', [OrganizacionController::class, 'Importar']) ->name('Importar'); 
        Route::post('dist/organizacion/importar', [OrganizacionController::class, 'PostImportar']) ->name('PostImportar'); 
        */

        Route::prefix('dist/provincia')->name('provincia.')->group(function () {
            Route::get('/', [ProvinciaController::class, 'Index']) ->name('Index'); 
            Route::post('/', [ProvinciaController::class, 'PostIndex']) ->name('PostIndex'); 
        });
        
        Route::prefix('dist/distrito')->name('distrito.')->group(function () {
            Route::get('/', [DistritoController::class, 'Index']) ->name('Index'); 
            Route::post('/', [DistritoController::class, 'PostIndex']) ->name('PostIndex'); 
        });
        
        Route::prefix('dist/corregimiento')->name('corregimiento.')->group(function () {
            Route::get('/', [CorregimientoController::class, 'Index']) ->name('Index'); 
            Route::post('/', [CorregimientoController::class, 'PostIndex']) ->name('PostIndex'); 
        });



        Route::prefix('dist/corregimiento')->name('corregimiento.')->group(function () {
            Route::get('/', [CorregimientoController::class, 'Index']) ->name('Index'); 
            Route::post('/', [CorregimientoController::class, 'PostIndex']) ->name('PostIndex'); 
        });

        Route::prefix('dist/permisos')->name('permisos.')->group(function () {
            Route::get('/', [PermisosController::class, 'Index']) ->name('Index'); 
            Route::post('/', [PermisosController::class, 'PostIndex']) ->name('PostIndex'); 
        });

        Route::prefix('dist/usuarios')->name('usuarios.')->group(function () {
            Route::get('/', [UsuariosController::class, 'Index']) ->name('Index'); 
            Route::post('/', [UsuariosController::class, 'PostIndex']) ->name('PostIndex'); 
        });


        Route::prefix('dist/RIDmigrantes')->name('RIDmigrantes.')->group(function () {
            Route::get('/', [RIDMigrantesController::class, 'Index']) ->name('Index'); 
            Route::post('/', [RIDMigrantesController::class, 'PostIndex']) ->name('PostIndex'); 
            Route::get('/nuevo', [RIDMigrantesController::class, 'Nuevo']) ->name('Nuevo'); 
            Route::post('/nuevo', [RIDMigrantesController::class, 'PostNuevo']) ->name('PostNuevo'); 
            Route::get('/editar/{Id}', [RIDMigrantesController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [RIDMigrantesController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [RIDMigrantesController::class, 'Mostrar']) ->name('Mostrar');
            Route::post('/migranteinformacion', [RIDMigrantesController::class, 'Informacion']) ->name('Informacion'); 
            Route::get('/migrantereporte/{Id}', [RIDMigrantesController::class, 'Reporte']) ->name('Reporte'); 
        });

        Route::prefix('dist/RIDpuestocontrol')->name('RIDpuestocontrol.')->group(function () {
            Route::get('/', [RIDPuestocontrolController::class, 'Index']) ->name('Index'); 
            Route::post('/', [RIDPuestocontrolController::class, 'PostIndex']) ->name('PostIndex'); 
            Route::get('/nuevo', [RIDPuestocontrolController::class, 'Nuevo']) ->name('Nuevo'); 
            Route::post('/nuevo', [RIDPuestocontrolController::class, 'PostNuevo']) ->name('PostNuevo'); 
            Route::get('/editar/{Id}', [RIDPuestocontrolController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [RIDPuestocontrolController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [RIDPuestocontrolController::class, 'Mostrar']) ->name('Mostrar');
        });

        Route::prefix('dist/RIDestaciontemporal')->name('RIDestaciontemporal.')->group(function () {
            Route::get('/', [RIDEstaciontemporalController::class, 'Index']) ->name('Index'); 
            Route::post('/', [RIDEstaciontemporalController::class, 'PostIndex']) ->name('PostIndex'); 
            Route::get('/nuevo', [RIDEstaciontemporalController::class, 'Nuevo']) ->name('Nuevo'); 
            Route::post('/nuevo', [RIDEstaciontemporalController::class, 'PostNuevo']) ->name('PostNuevo'); 
            Route::get('/editar/{Id}', [RIDEstaciontemporalController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [RIDEstaciontemporalController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [RIDEstaciontemporalController::class, 'Mostrar']) ->name('Mostrar');
        });

        Route::prefix('dist/RIDafinidad')->name('RIDafinidad.')->group(function () {
            Route::get('/', [RIDAfinidadController::class, 'Index']) ->name('Index'); 
            Route::post('/', [RIDAfinidadController::class, 'PostIndex']) ->name('PostIndex'); 
            Route::get('/nuevo', [RIDAfinidadController::class, 'Nuevo']) ->name('Nuevo'); 
            Route::post('/nuevo', [RIDAfinidadController::class, 'PostNuevo']) ->name('PostNuevo'); 
            Route::get('/editar/{Id}', [RIDAfinidadController::class, 'Editar']) ->name('Editar');
            Route::post('/editar/{Id}', [RIDAfinidadController::class, 'PostEditar']) ->name('PostEditar'); 
            Route::get('/mostrar/{Id}', [RIDAfinidadController::class, 'Mostrar']) ->name('Mostrar');
        });

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
