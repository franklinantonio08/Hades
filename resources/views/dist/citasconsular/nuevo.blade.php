@extends('layouts.admin')

@section('styles')
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
@endsection

@section('scripts')
    <!-- JavaScript variables and libraries -->
    <script>
        const BASEURL = '{{ url()->current() }}';
        const token = '{{ csrf_token() }}';
    </script>



    <script src="{{ asset('plugins/moment/moment.js') }}" type="text/javascript"></script>
    <script src="{{ asset('plugins/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/comun/messagebasicModal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('js/dist/citasconsular/citasconsular.js') }}" type="text/javascript"></script>
@endsection

@section('content')
<div class="row">
    @include('includes.errors')
    @include('includes.success')
</div>

<div class="col-lg-12">
    <div class="card mb-4">
        <div class="container-fluid py-4">
            <form id="nuevoregistro" name="nuevoregistro" method="POST" action="{{ url('/dist/citasConsular/nuevo') }}" enctype="multipart/form-data" autocomplete="off">
                {{ csrf_field() }}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">Registro de Cita Consular</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md-6 mb-3">
                                <label for="pais" class="form-label fw-bold text-primary">Pais</label>
                                <select class="form-control" id="pais" name="pais">
                                    <option value="" selected disabled>Selecciona...</option>
                                    @foreach ($pais as $key => $value)
                                        <option value="{{ $value->id }}">{{ $value->pais }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="consulado" class="form-label fw-bold text-primary">Consulados</label>
                                <div id="DivResultado_consulado" >    
                                    <select class="form-control" id="consulado" name="consulado">
                                        <option value="" selected disabled>Selecciona...</option>
                                    </select>
                                </div>
                            </div>



                          

                            <!-- Servicios Consulares -->
                            <div class="col-md-6 mb-3">
                                <label for="servicios" class="form-label fw-bold text-primary">Servicios Consulares</label>
                                <div id="DivResultado_serviciosconsulares" >    
                                    <select class="form-control" id="servicios" name="servicios">
                                        <option value="" selected disabled>Selecciona...</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="motivo" class="form-label fw-bold text-primary">Motivo</label>
                                <select class="form-control" id="motivo" name="motivo">
                                    <option value="" selected disabled>Selecciona...</option>
                                    @foreach (['Negocios e inversión', 'Eco-Turismo y aventura', 'Experiencias culturales', 'Turismo médico', 'Retiro y reubicación', 'Educación e investigación', 'Asistir a seminarios', 'Ferias o Eventos', 'Otros'] as $value)
                                        <option value="{{ $value }}">{{ $value }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Comentario -->
                            <div class="col-md-12 mb-3">
                                <label for="comentario" class="form-label fw-bold text-primary">Comentario</label>
                                <textarea class="form-control" id="comentario" name="comentario" placeholder="Escribe un comentario" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="card-footer text-end">
                        <button id="submitForm" type="submit" class="btn btn-primary shadow">
                            Guardar
                        </button>
                        <a href="{{ url()->previous() }}" class="btn btn-secondary shadow">
                            Cancelar
                        </a>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>

@include('includes.messagebasicmodal')
@endsection
