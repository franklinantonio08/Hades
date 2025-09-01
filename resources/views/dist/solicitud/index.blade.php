@extends('layouts.admin')

@section('content')
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-body p-4">
                <!-- Título en una fila separada -->
                <div class="row mb-3">
                    <div class="col-sm-12">
                        <h4 class="card-title fw-semibold">Listado de Solicitudes</h4>
                    </div>
                </div>

                @if (Auth::user()->usuariopermiso('035'))    

                    <div class="row">                                        
                        <div class="col-sm-6">
                            
                            <div class="row">

                                <div class="col-6">
                                    <div class="text-dark px-3 py-3 mb-3 rounded shadow-sm border border-info d-flex align-items-center"
                                        style="background: linear-gradient(to right, #ffffff, #cff4fc);">
                                        <div class="bg-info text-white rounded p-3 d-flex align-items-center justify-content-center me-3"
                                            style="width: 60px; height: 60px;">
                                            <i class="bi bi-person-square fs-4"></i>
                                        </div>     
                                        <div class="w-100 text-center">
                                            <small class="d-block fw-semibold">Total Solicitudes </small>
                                            <div class="fs-5 fw-bold" id="totalYearlyAmount">  {{ number_format($resumen['Total']->cantidad_infractores ?? 0 ) }}</div>
                                            {{-- <small class="text-muted">Cantidad: <span id="totalYearlyCount">{{ $resumen['Año actual']->cantidad_multas ?? 0 }}</span></small> --}}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="text-dark px-3 py-3 mb-3 rounded shadow-sm border border-primary d-flex align-items-center"
                                        style="background: linear-gradient(to right, #ffffff, #f8d7da);">
                                        <div class="bg-primary text-white rounded p-3 d-flex align-items-center justify-content-center me-3"
                                            style="width: 60px; height: 60px;">
                                            <i class="bi bi-person-square fs-4"></i>
                                        </div>
                                        <div class="w-100 text-center">
                                            <small class="d-block fw-semibold">Total Año Actual</small>
                                            <div class="fs-5 fw-bold" id="totalMonthly"> {{ number_format($resumen['Año actual']->cantidad_infractores ?? 0) }}</div>
                                            {{-- <small class="text-muted">Cantidad: <span id="totalMonthlyCount">{{ $resumen['Mes actual']->cantidad_multas ?? 0 }}</span></small> --}}
                                        </div>
                                    </div>
                                </div>                       
                                
                            </div>
                            <div id="migrantes-semanal-weekly"></div> 
                        </div>                

                        <div class="col-sm-6">

                            <div class="row">

                                <div class="col-6">
                                    <div class="text-dark px-3 py-3 mb-3 rounded shadow-sm border border-primary d-flex align-items-center"
                                        style="background: linear-gradient(to right, #ffffff, #fff3cd);">
                                        <div class="bg-primary text-white rounded p-3 d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                            <i class="bi bi-person-square fs-4"></i>
                                        </div>        
                                        <div class="w-100 text-center">
                                            <small class="d-block fw-semibold">Total Mes Actual</small>
                                            <div class="fs-5 fw-bold" id="totalLastWeek"> {{ number_format($resumen['Mes actual']->cantidad_infractores ?? 0 ) }}</div>
                                            {{-- <small class="text-muted">Cantidad: <span id="totalLastWeekCount">{{ $resumen['Semana anterior']->cantidad_multas ?? 0 }} </span></small> --}}
                                        </div>
                                    </div>
                                </div>       

                                <div class="col-6">
                                    <div class="text-dark px-3 py-3 mb-3 rounded shadow-sm border border-success d-flex align-items-center"
                                        style="background: linear-gradient(to right, #ffffff, #d1e7dd);">
                                        <div class="bg-success text-white rounded p-3 d-flex align-items-center justify-content-center me-3"
                                            style="width: 60px; height: 60px;">
                                            <i class="bi bi-person-square fs-4"></i>
                                        </div>    
                                        <div class="w-100 text-center">
                                            <small class="d-block fw-semibold">Total Semana Actual</small>
                                            <div class="fs-5 fw-bold" id="totalCurrentWeek">  {{ number_format($resumen['Semana actual']->cantidad_infractores ?? 0 ) }}</div>
                                            {{-- <small class="text-muted">Cantidad: <span id="totalCurrentWeekCount">{{ $resumen['Semana actual']->cantidad_multas ?? 0 }} </span></small> --}}
                                        </div>
                                    </div>
                                </div>

                            </div>                    
                            <div class="card-body" id="migrantes-edad">
                            </div>
                        </div> 
                    </div>

                @endif

               <div class="row align-items-end flex-wrap gap-3 mb-4">
                    <!-- Rango de Fechas -->
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted">Rango de Fechas</label>
                        <div id="reportrange" class="form-control d-flex align-items-center shadow-sm border border-success-subtle rounded">
                            <i class="bi bi-calendar-event me-2 text-success fs-5"></i>
                            <span class="flex-grow-1">Seleccione la fecha</span>
                            <i class="bi bi-caret-down-fill ms-auto text-muted"></i>
                        </div>
                    </div>

                    <!-- Estado -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted">Estado</label>
                        <select id="estadoFiltro" name="estadoFiltro" class="form-select shadow-sm">
                            <option value="">Todos</option>
                            <option value="Pendiente">Pendiente</option>
                            <option value="Aprobado">Aprobado</option>
                            <option value="Rechazado">Rechazado</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                    </div>

                    {{-- <!-- Acciones -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted">Acciones</label>
                        <select class="form-select shadow-sm" id="accionesId" name="accionesId">
                            <option value="">Todos</option>
                            @foreach ($acciones as $key => $value)
                                <option value="{{ $value->id }}">{{ $value->descripcion }}</option>
                            @endforeach
                        </select>
                    </div> --}}

                    <!-- Nuevo -->
                    {{-- <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted invisible">Nuevo</label>
                        <a href="{{ url()->current() }}/nuevo" class="btn btn-primary w-100 shadow-sm fw-semibold text-white"">
                            <i class="bi bi-file-earmark-plus me-1"></i> Nuevo Registro
                        </a>
                    </div> --}}

                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted invisible">Nuevo</label>
                        <button type="button" id="nuevoRegistroBtn" 
                                class="btn btn-primary w-100 shadow-sm fw-semibold text-white">
                            <i class="bi bi-file-earmark-plus me-1"></i> Nuevo Registro
                        </button>
                    </div>


                    <!-- Reporte -->
                    @if (Auth::user()->usuariopermiso('035'))
                    <div class="col-md-2">
                        <label class="form-label fw-semibold text-muted invisible">Reporte</label>
                        <button id="reporteButton" class="btn btn-success w-100 shadow-sm descargarButton">
                            <i class="bi bi-download me-1"></i> Generar Reporte
                        </button>
                    </div>
                    @endif
                </div>

                <!-- Buscar alineado a la derecha -->
                <div class="row justify-content-between align-items-end mb-4">
                    <div class="col-md-3">
                        <div class="input-group shadow-sm">
                            <input type="text" id="search" name="search" class="form-control" placeholder="Buscar...">
                            <button type="button" id="searchButton" class="btn btn-primary fw-semibold text-white">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                </div>






                <!-- Mensajes de Error y Éxito -->
                <div class="row">
                    @include('includes.errors')
                    @include('includes.success')
                </div>

                <!-- Tabla de Migrantes -->
                <div class="row">
                    <div class="table-responsive">
                        <table class="table table-bordered data-table" id="solicitud">
                            <thead>
                                <tr>
                                    <th class="bg-primary fs-8 fw-semibold text-white">#</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Nombre</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Ruex</th>                                    
                                    <th class="bg-primary fs-8 fw-semibold text-white">Codigo</th>                                    
                                    <th class="bg-primary fs-8 fw-semibold text-white">Dirección</th>
                                    {{-- <th class="bg-primary fs-8 fw-semibold text-white">Operativo</th> --}}
                                    {{-- <th class="bg-primary fs-8 fw-semibold text-white">Provincia</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Funcionario</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Aprobado por</th> --}}
                                    <th class="bg-primary fs-8 fw-semibold text-white">Estado</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Acción<i class="fa fa-ellipsis-h"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="gradeX">
                                    <td colspan="7" class="text-center">No hay datos disponibles</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalImagenes" tabindex="-1" aria-labelledby="modalImagenesLabel">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Galería de Imágenes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <iframe id="iframeImagenes" src="" style="width: 100%; height: 75vh; border: none;"></iframe>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalSolicitudActiva" tabindex="-1" aria-labelledby="modalSolicitudActivaLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title">Solicitud Activa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        Ya tienes una solicitud activa. Debes finalizarla antes de crear una nueva.
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>



    @include('includes.confirmacionmodal')
    @include('includes.messagebasicmodal')
    @include('includes.loader')
    @include('includes.download')
    @include('dist.solicitud.mostrar')
    @include('dist.solicitud.buscar')
@endsection

@section('scripts')
    <script>
        const BASEURL = '{{ url()->current() }}';
        const token = '{{ csrf_token() }}';
    </script>

    <!-- JS Específicos -->
   
    <script src="{{ asset('js/comun/confirmacionModal.js') }}"></script>
    <script src="{{ asset('js/comun/messagebasicModal.js') }}"></script>


     <script src="{{ asset('js/dist/solicitud/solicitud.js') }}"></script>

    <!-- Plugins -->
    <script src="{{ asset('plugins/moment/moment.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
@endsection
