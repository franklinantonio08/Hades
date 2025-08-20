@extends('layouts.admin')

@section('styles')
    <!-- Stylesheets -->
    <link rel="stylesheet" href="{{ asset('plugins/bootstrap-daterangepicker/daterangepicker.css') }}">

@endsection

@section('scripts')
    <!-- JavaScript variables and libraries -->
    <script>
        // const BASEURL = '{{ url()->current() }}';
        const token = '{{ csrf_token() }}';

        const BASEURL = '{{ url('/dist/solicitud') }}';
        
    </script>

    <script src="{{ asset('plugins/moment/moment.js') }}" ></script>
    <script src="{{ asset('plugins/bootstrap-daterangepicker/daterangepicker.js') }}" ></script>
    <script src="{{ asset('js/comun/messagebasicModal.js') }}" ></script>
    <script src="{{ asset('js/dist/solicitud/solicitud.js') }}" ></script>

    {{-- <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Cargar el contenido de instruciones.blade.php vía AJAX
            fetch("{{ url('/dist/solicitud/instruciones') }}")
                .then(response => response.text())
                .then(html => {
                    document.getElementById("instruccionesContent").innerHTML = html;
                    // Mostrar el modal automáticamente
                    var modal = new bootstrap.Modal(document.getElementById('modalInstrucciones'));
                    modal.show();
                })
                .catch(error => console.error('Error cargando instrucciones:', error));
        });
    </script> --}}


@endsection

@section('content')
<div class="row">
    @include('includes.errors')
    @include('includes.success')
</div>

<div class="col-lg-12">
    <div class="card mb-4">    
        <div class="container-fluid py-4">

            <form id="nuevoregistro" name="nuevoregistro" method="POST" action="{{ url('/dist/solicitud/nuevo') }}" enctype="multipart/form-data" autocomplete="off">
                @csrf

                <div class="card shadow-sm border-0">
                    <div class="card-header bg-primary text-white text-center">
                        <h5 class="mb-0">Actualizacion de Dirección</h5>
                        <small class="text-light">Datos Personales y Documentales </small>
                    </div>

                    <div class="card-body">

                        {{-- <h5 class="text-primary fw-bold mb-3">Datos Personales</h5> --}}
                        <h5 class="form-label fw-bold text-primary" style="background-color: #f0f0f0; padding: 5px;">Datos Personales</h5>
                        <div class="row mb-4">

                            <div class="col-md-8">
                                <div class="row">
                        
                                    <input type="text" id="" name="afiliacion" hidden readonly value="">
                                   
                                    <div class="col-md-3 mb-3">
                                        <label for="primerNombre" class="form-label fw-bold text-primary">Primer Nombre</label>
                                        <div id="div_nombre" ></div>
                                        <input type="text" class="form-control" id="primerNombre" name="primerNombre" placeholder="Ingrese su primer nombre" readonly value="{{ $Usuario->primer_nombre }}">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="segundoNombre" class="form-label fw-bold text-primary">Segundo Nombre</label>
                                        <div id="div_nombre" ></div>
                                        <input type="text" class="form-control" id="segundoNombre" name="segundoNombre" placeholder="Ingrese su segundo nombre" readonly value="{{ $Usuario->segundo_nombre }}">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="primerApellido" class="form-label fw-bold text-primary">Primer Apellido</label>
                                        <div id="div_apellido" ></div>
                                        <input type="text" class="form-control" id="primerApellido" name="primerApellido" placeholder="Ingrese su primer apellido" readonly value="{{ $Usuario->primer_apellido }}">
                                    </div>

                                    <div class="col-md-3 mb-3">
                                        <label for="segundoApellido" class="form-label fw-bold text-primary">Segundo Apellido</label>
                                        <div id="div_apellido" ></div>
                                        <input type="text" class="form-control" id="segundoApellido" name="segundoApellido" placeholder="Ingrese su segundo apellido" readonly value="{{ $Usuario->segundo_apellido }}">
                                    </div>                

                                    <div class="col-md-6 mb-3">
                                        <label for="correo" class="form-label fw-bold text-primary">Correo Electrónico</label>
                                        <div id="div_correo" ></div>
                                        <input type="email" class="form-control" id="correo" name="correo" placeholder="Ingrese su correo electrónico" readonly value="{{ $Usuario->email }}">
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="pasaporte" class="form-label fw-bold text-primary">Pasaporte</label>
                                        <div id="div_pasaporte" ></div>
                                        <input type="text" class="form-control" id="pasaporte" name="pasaporte" placeholder="Ingrese su pasaporte" readonly value="{{ $Usuario->documento_numero }}">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="paisNacionalidad" class="form-label fw-bold text-primary">Nacionalidad</label>
                                        <div id="div_paisNacionalidad"></div>
                                        <input type="hidden" name="pais_nacionalidad_id" value="">
                                        <input type="text" class="form-control" id="paisNacionalidad" name="paisNacionalidad" readonly value="">
                                    </div>
                                
                                    <div class="col-md-6 mb-3">
                                        <label for="paisNacimiento" class="form-label fw-bold text-primary">País de Nacimiento</label>
                                        <div id="div_paisNacimiento"></div>
                                        <input type="hidden" name="pais_nacimiento_id" value="">
                                        <input type="text" class="form-control" id="paisNacimiento" name="paisNacimiento" readonly value="">
                                    </div>

                                </div>
                            </div>
                                
                            @if(!empty($afiliacion->foto_url))
                                <input type="hidden" name="foto_url" value="{{ basename($afiliacion->foto_url) }}">
                            @endif

                            <div class="col-md-4 d-flex justify-content-center align-items-center">
                                @if(!empty($afiliacion->foto_url))
                                    <div class="p-2 bg-light rounded d-flex justify-content-center align-items-center" 
                                        style="box-shadow: 0 4px 12px rgba(0,0,0,0.2); width: 210px; height: 270px;">
                                        <img src="{{ $afiliacion->foto_url }}" 
                                            alt="Foto biométrica" 
                                            id="fotoBiometrica" 
                                            name="fotoBiometrica" 
                                            class="img-fluid rounded" 
                                            style="max-width: 100%; max-height: 100%; object-fit: contain; border: 3px solid #fff;">
                                    </div>
                                @else
                                    <div class="bg-light text-muted d-flex align-items-center justify-content-center rounded shadow-sm" 
                                        style="width: 200px; height: 260px; border: 2px dashed #ccc;">
                                        Sin Foto
                                    </div>
                                @endif
                            </div>

                            

                            <hr class="mb-4">
                            <h5 class="form-label fw-bold text-primary" style="background-color: #f0f0f0; padding: 5px;">Documento y Estatus</h5>
                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label for="tipoCarnet" class="form-label fw-bold text-primary">Tipo de Carnet</label>
                                    <div id="div_tipoCarnet" ></div>
                                    <input type="text" class="form-control" id="tipoCarnet" name="tipoCarnet" placeholder="Ingrese su tipo de carnet" readonly value="">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="numCarnet" class="form-label fw-bold text-primary">Numero de Carnet</label>
                                    <div id="div_numCarnet" ></div>
                                    <input type="text" class="form-control" id="numCarnet" name="numCarnet" placeholder="Ingrese su numero de carnet" readonly value="">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="tipoStatus" class="form-label fw-bold text-primary">Tipo de Estatus</label>
                                    <div id="div_tipoStatus" ></div>
                                    <input type="text" class="form-control" id="tipoStatus" name="tipoStatus" readonly value="">
                                </div>                                  

                                <div class="col-md-6 mb-3">
                                    <label for="fecNacimiento" class="form-label fw-bold text-primary">Fecha de Nacimiento</label>
                                    <div id="div_fecNacimiento"></div>
                                    <input type="text" class="form-control" id="fecNacimiento" name="fecNacimiento" placeholder="Ingrese su número de carnet" readonly value="">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="fecpma" class="form-label fw-bold text-primary">Fecha de Llegada a Panamá</label>
                                    <div id="div_fecpma"></div>
                                    <input type="text" class="form-control" id="fecpma" name="fecpma" placeholder="Ingrese su número de carnet" readonly value="">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="fecVencimiento" class="form-label fw-bold text-primary">Fecha de Vencimiento del Documento</label>
                                    <div id="div_fecVencimiento"></div>
                                    <input type="text" class="form-control" id="fecVencimiento" name="fecVencimiento" placeholder="Ingrese su número de carnet" readonly value="">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="fecExpedicion" class="form-label fw-bold text-primary">Fecha de Expedicion del Documento</label>
                                    <div id="div_fecExpedicion"></div>
                                    <input type="text" class="form-control" id="fecExpedicion" name="fecExpedicion" placeholder="Ingrese su número de carnet" readonly value="">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="provincia" class="form-label fw-bold text-primary">Provincia</label>
                                    <select class="form-select" id="provincia" name="provincia">
                                        <option value="" selected disabled>Selecciona...</option>
                                        @foreach ($provincia as $item)
                                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="distrito" class="form-label fw-bold text-primary">Distrito</label>
                                    <select class="form-select" id="distrito" name="distrito">
                                        <option value="" selected disabled>Selecciona una provincia primero</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="corregimiento" class="form-label fw-bold text-primary">Corregimiento</label>
                                    <select class="form-select" id="corregimiento" name="corregimiento">
                                        <option value="" selected disabled>Selecciona un distrito primero</option>
                                    </select>
                                </div>
                            </div>

                            


                            <hr class="mb-4">
                            {{-- <h5 class="text-primary fw-bold mb-3">Comentario Adicional</h5> --}}
                            <h5 class="form-label fw-bold text-primary" style="background-color: #f0f0f0; padding: 5px;">Comentario Adicional</h5>
                            <textarea class="form-control" name="comentario" rows="3" placeholder="Escribe un comentario"></textarea>

                            {{-- <div class="col-md-12 mb-3">
                                <label for="comentario" class="form-label fw-bold text-primary">Comentario</label>
                                <textarea class="form-control" id="comentario" name="comentario" placeholder="Escribe un comentario" rows="3"></textarea>
                            </div>                        --}}

                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="card-footer text-end">
                        <button id="calcularForm" type="button" class="btn btn-primary shadow">
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




<style>
    /* Cuando está seleccionado: cambia a bg-primary y texto blanco */
    .btn-check:checked + .btn {
        background-color: var(--bs-primary);
        color: var(--bs-white);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.5);
    }

    .btn-check:checked + .btn .tipo-texto {
        color: var(--bs-white) !important;
    }

    .btn-check:checked + .btn .check-icon {
        display: inline-block !important;
        color: var(--bs-white);
    }
</style>

{{-- @include('dist.solicitud.monto') --}}
@include('includes.messagebasicmodal')
@include('dist.solicitud.instruciones')
{{-- @include('includes.operativoslist') --}}
@endsection

