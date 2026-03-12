@extends('layouts.admin') @section('css')
    <link rel="stylesheet" href="{{ asset('plugins/flatpickr/flatpickr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('plugins/flatpickr/material_blue.css') }}">
    @endsection @section('scripts')
    <script>
        const BASEURL = '{{ url('/dist/solicitud') }}';
        const token = '{{ csrf_token() }}';
    </script>

    <script src="{{ asset('plugins/flatpickr/flatpickr.min.js') }}"></script>
    <script src="{{ asset('plugins/flatpickr/es.js') }}"></script>

    <script src="{{ asset('js/comun/messagebasicModal.js') }}"></script>
    <script src="{{ asset('js/dist/solicitud/solicitud.js') }}"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            flatpickr("#fecha_nacimiento_persona", {
                dateFormat: "Y-m-d",
                maxDate: "today",
                locale: "es"
            });

        });
    </script>
    @endsection @section('content')
    <div class="row">
        @include('includes.errors') @include('includes.success')
    </div>


    <div class="col-lg-12">

        <div class="card mb-4">

            <div class="container-fluid py-4">

                <form id="editarregistro" method="POST" action="{{ url('/dist/solicitud/actualizar/' . $solicitud->id) }}"
                    enctype="multipart/form-data" autocomplete="off">

                    @csrf @method('PUT')

                    <input type="hidden" name="solicitudId" value="{{ $solicitud->id }}">


                    <div class="card shadow-sm border-0">

                        <div class="card-header bg-primary text-white text-center">

                            <h5 class="mb-0">Actualización de Dirección</h5>
                            <small class="text-light">Datos Personales y Documentales</small>

                        </div>


                        <div class="card-body">


                            {{-- ERRORES VISUALES --}}
                            <div id="formErrorBox" class="alert alert-danger d-none">
                                <div class="fw-bold mb-2">Por favor corrige lo siguiente:</div>
                                <ul id="formErrorList" class="mb-0"></ul>
                            </div>



                            {{-- ======================== DATOS PERSONALES ======================== --}}

                            <h5 class="form-label fw-bold text-primary" style="background-color:#f0f0f0;padding:5px;">
                                Datos Personales
                            </h5>


                            <div class="row mb-4">

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold text-primary">Primer Nombre</label>
                                    <input type="text" class="form-control" name="primerNombre"
                                        value="{{ $solicitud->primer_nombre }}">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold text-primary">Segundo Nombre</label>
                                    <input type="text" class="form-control" name="segundoNombre"
                                        value="{{ $solicitud->segundo_nombre }}">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold text-primary">Primer Apellido</label>
                                    <input type="text" class="form-control" name="primerApellido"
                                        value="{{ $solicitud->primer_apellido }}">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label fw-bold text-primary">Segundo Apellido</label>
                                    <input type="text" class="form-control" name="segundoApellido"
                                        value="{{ $solicitud->segundo_apellido }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-primary">Filiación</label>
                                    <input type="text" class="form-control" name="filiacion"
                                        value="{{ $solicitud->num_filiacion }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-primary">Pasaporte</label>
                                    <input type="text" class="form-control" name="pasaporte"
                                        value="{{ $solicitud->pasaporte }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-primary">Genero</label>
                                    <select class="form-select" name="genero">
                                        <option value="Masculino"
                                            {{ $solicitud->genero == 'Masculino' ? 'selected' : '' }}>
                                            Masculino
                                        </option>
                                        <option value="Femenino" {{ $solicitud->genero == 'Femenino' ? 'selected' : '' }}>
                                            Femenino
                                        </option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-primary">
                                        Fecha nacimiento
                                    </label>
                                    <input type="text" class="form-control" id="fecha_nacimiento_persona"
                                        name="fecha_nacimiento" value="{{ $solicitud->fecha_nacimiento }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-primary">Correo</label>
                                    <input type="email" class="form-control" name="correo"
                                        value="{{ $solicitud->correo }}">
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-primary">Teléfono</label>
                                    <input type="text" class="form-control" name="telefono"
                                        value="{{ $solicitud->telefono }}">
                                </div>

                                 <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-primary">
                                        Provincia
                                    </label>
                                    <select class="form-select" name="provincia" id="provincia">
                                        @foreach ($provincia as $p)
                                            <option value="{{ $p->id }}"
                                                {{ $solicitud->provincia_id == $p->id ? 'selected' : '' }}>
                                                {{ $p->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold text-primary">
                                        Distrito
                                    </label>
                                    <select class="form-select" name="distrito" id="distrito">
                                        @foreach ($distrito as $d)
                                            <option value="{{ $d->id }}"
                                                {{ $solicitud->distrito_id == $d->id ? 'selected' : '' }}>
                                                {{ $d->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                            </div>



                            {{-- ======================== DIRECCION ======================== --}}

                            <h5 class="form-label fw-bold text-primary" style="background-color:#f0f0f0;padding:5px;">
                                Dirección específica
                            </h5>


                            <div class="row g-3">


                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold text-primary">
                                        Provincia
                                    </label>

                                    <select class="form-select" name="provincia" id="provincia">

                                        @foreach ($provincia as $p)
                                            <option value="{{ $p->id }}"
                                                {{ $solicitud->provincia_id == $p->id ? 'selected' : '' }}>
                                                {{ $p->nombre }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>



                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold text-primary">
                                        Distrito
                                    </label>

                                    <select class="form-select" name="distrito" id="distrito">

                                        @foreach ($distrito as $d)
                                            <option value="{{ $d->id }}"
                                                {{ $solicitud->distrito_id == $d->id ? 'selected' : '' }}>
                                                {{ $d->nombre }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>



                                <div class="col-md-6 mb-3">

                                    <label class="form-label fw-bold text-primary">
                                        Corregimiento
                                    </label>

                                    <select class="form-select" name="corregimiento" id="corregimiento">

                                        @foreach ($corregimiento as $c)
                                            <option value="{{ $c->id }}"
                                                {{ $solicitud->corregimiento_id == $c->id ? 'selected' : '' }}>
                                                {{ $c->nombre }}
                                            </option>
                                        @endforeach

                                    </select>

                                </div>



                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-primary">
                                        Barrio / Urbanización
                                    </label>

                                    <input type="text" class="form-control" name="barrio"
                                        value="{{ $solicitud->barrio }}">

                                </div>


                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-primary">
                                        Calle / Avenida
                                    </label>

                                    <input type="text" class="form-control" name="calle"
                                        value="{{ $solicitud->calle }}">
                                </div>


                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-primary">
                                        Número de casa
                                    </label>

                                    <input type="text" class="form-control" name="numero_casa"
                                        value="{{ $solicitud->numero_casa }}">
                                </div>


                                <div class="col-md-12">
                                    <label class="form-label fw-bold text-primary">
                                        Punto de referencia
                                    </label>

                                    <input type="text" class="form-control" name="punto_referencia"
                                        value="{{ $solicitud->punto_referencia }}">
                                </div>

                            </div>



                            {{-- ======================== DOCUMENTOS ======================== --}}

                            <hr class="mb-4">
                            <h5 class="form-label fw-bold text-primary" style="background-color:#f0f0f0;padding:5px;">
                                Documentos requeridos
                            </h5>


                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-primary">
                                        Documento de domicilio
                                    </label>

                                    <input type="file" class="form-control" name="domicilio_archivo">
                                    @if (isset($archivos['domicilio']))
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary verDocumento"
                                                data-url="{{ asset('storage/' . $archivos['domicilio']->ruta) }}"
                                                data-tipo="Documento de domicilio">

                                                Ver documento actual

                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-primary">
                                        Recibo de servicio
                                    </label>
                                    <input type="file" class="form-control" name="recibo_archivo">
                                    @if (isset($archivos['recibo']))
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary verDocumento"
                                                data-url="{{ asset('storage/' . $archivos['recibo']->ruta) }}"
                                                data-tipo="Recibo de servicio">
                                                Ver recibo actual
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-primary">
                                        carnet_frente
                                    </label>
                                    <input type="file" class="form-control" name="carnet_frente">
                                    @if (isset($archivos['carnet_frente']))
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary verDocumento"
                                                data-url="{{ asset('storage/' . $archivos['carnet_frente']->ruta) }}"
                                                data-tipo="Recibo de servicio">
                                                Ver carnet_frente actual
                                            </button>
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-bold text-primary">
                                        carnet_reverso
                                    </label>
                                    <input type="file" class="form-control" name="carnet_reverso">
                                    @if (isset($archivos['carnet_reverso']))
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary verDocumento"
                                                data-url="{{ asset('storage/' . $archivos['carnet_reverso']->ruta) }}"
                                                data-tipo="Recibo de servicio">
                                                Ver carnet_reverso actual
                                            </button>
                                        </div>
                                    @endif
                                </div>


                            </div>



                            {{-- ======================== COMENTARIO ======================== --}}

                            <hr class="mb-4">
                            <h5 class="form-label fw-bold text-primary" style="background-color:#f0f0f0;padding:5px;">
                                Comentario adicional </h5>

                            <textarea class="form-control" name="comentario" rows="3">
                            {{ $solicitud->comentario }}
                        </textarea>



                        </div>



                        <div class="card-footer text-end">

                            <button id="guardarForm" type="submit" class="btn btn-primary shadow">

                                Actualizar

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


    @include('dist.solicitud.documentos')
    @include('includes.messagebasicmodal')

@endsection
