@extends('layouts.admin')

@section('content')
    <div class="col-lg-12">
        <div class="card mb-4">
            <div class="card-body p-4">
                <!-- Título en una fila separada -->
                <div class="row mb-3">
                    <div class="col-sm-12">
                        <h4 class="card-title fw-semibold">Lista de Migrantes</h4>
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

                    <div class="col-sm-2">
                        <button id="reporteButton" class="btn bg-success fs-8 fw-semibold text-white descargarButton">
                            <i class="bi bi-download"></i> <span>Generar Reporte</span>
                        </button>
                    </div>

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
                        <table class="table table-bordered data-table" id="migrantes">
                            <thead>
                                <tr>
                                    <th class="bg-primary fs-8 fw-semibold text-white">#</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Nombre</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Documento</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Genero</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Tipo</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Pais</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Region</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Puesto de Control</th>
                                    <th class="bg-primary fs-8 fw-semibold text-white">Afinidad</th>
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
@endsection

@section('scripts')
    <script>
        const BASEURL = '{{ url()->current() }}';
        const token = '{{ csrf_token() }}';
    </script>

    <!-- JS Específicos -->
    <script src="{{ asset('../js/admin/RIDMigrantes/RIDMigrantes.js') }}" type="text/javascript"></script>
    <script src="{{ asset('../js/comun/confirmacionModal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('../js/comun/messagebasicModal.js') }}" type="text/javascript"></script>

    <!-- Plugins -->
    <script src="{{ asset('../plugins/moment/moment.js') }}" type="text/javascript"></script>
    <script src="{{ asset('../plugins/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
@endsection
