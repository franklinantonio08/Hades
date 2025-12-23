@extends('layouts.admin')

@section('content')
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-body p-4">
                <!-- Título en una fila separada -->
                <div class="row mb-3">
                    <div class="col-sm-12">
                        <h4 class="card-title fw-semibold">Lista de Citas</h4>
                    </div>
                </div>

                <!-- Controles en la siguiente fila -->
                <div class="row mb-3 align-items-center">
                    <div class="col-sm-4 d-flex align-items-center">
                        <label for="reportrange" class="btn bg-success fs-8 fw-semibold text-white me-2">Fecha</label>
                        <div id="reportrange" class="form-control d-flex align-items-center">
                            <i class="ion-calendar me-2"></i>
                            <span>Seleccione la fecha</span>
                            <i class="bi bi-caret-down-fill ms-auto"></i>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <a href="{{ url()->current() }}/nuevo" class="btn bg-primary fs-8 fw-semibold text-white">
                            <i class="bi bi-file-earmark-plus"></i> <span>Crear Nuevo</span>
                        </a>
                    </div>

                    @if (Auth::user()->usuariopermiso('006'))
                    <div class="col-sm-2">
                        <button id="reporteButton" class="btn bg-success fs-8 fw-semibold text-white descargarButton">
                            <i class="bi bi-download"></i> <span>Generar Reporte</span>
                        </button>
                    </div>
                    @endif

                    <div class="col-sm-3 offset-sm-1">
                        <div class="input-group">
                            <input type="text" id="search" name="search" class="form-control" placeholder="Buscar...">
                            <button type="button" id="searchButton" name="searchButton" class="btn btn-warning">
                                <svg class="icon me-2">
                                    <use xlink:href="{{ asset('../vendors/@coreui/icons/svg/free.svg#cil-search') }}"></use>
                                </svg>
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
                        <table class="table table-bordered data-table" id="citas">
                            <thead>
                                <tr>
                                    <th class="bg-primary fs-8 fw-semibold text-white">#</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Pais</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Consulados</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Servicios</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Usuario</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Codigo</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Fecha de Solicitud</th>                                    
                                    <th class="bg-primary fs-8 fw-semibold text-white">Fecha de Cita</th>                                    
                                    <th class="bg-primary fs-8 fw-semibold text-white">Estatus</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Acción<i class="fa fa-ellipsis-h"></i></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="gradeX">
                                    <td colspan="10" class="text-center">No hay datos disponibles</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('includes.confirmacionmodal')
    @include('includes.messagebasicmodal')
    @include('includes.loader')
    @include('includes.download')
    @include('dist.citasConsular.mostrar')
    @include('dist.citasConsular.imprimir')
@endsection

@section('scripts')
    <script>
        const BASEURL = '{{ url()->current() }}';
        const token = '{{ csrf_token() }}';
    </script>

    <!-- JS Específicos -->
    <script src="{{ asset('../js/dist/citasconsular/citasconsular.js') }}" type="text/javascript"></script>
    <script src="{{ asset('../js/comun/confirmacionModal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('../js/comun/messagebasicModal.js') }}" type="text/javascript"></script>

    <!-- Plugins -->
    <script src="{{ asset('../plugins/moment/moment.js') }}" type="text/javascript"></script>
    <script src="{{ asset('../plugins/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
@endsection
