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
                            <h5 class="form-label fw-bold text-primary" style="background-color:#f0f0f0;padding:5px;">Inversionista Calificado</h5>

                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-primary d-block">¿Eres Inversionista Calificado?</label>
                                    <div class="btn-group w-100" role="group" aria-label="Tipo de vivienda">
                                        <input type="radio" class="btn-check" name="inversionista" id="inversionista_si" value="Si" >
                                        <label class="btn btn-outline-primary" for="inversionista_si">Si</label>

                                        <input type="radio" class="btn-check" name="inversionista" id="inversionista_no" value="No" checked>
                                        <label class="btn btn-outline-primary" for="inversionista_no">No</label>

                                    </div>
                                </div>

                            <div class="row g-3">
                            </div>

                            <hr class="mb-4">
                            <h5 class="form-label fw-bold text-primary" style="background-color:#f0f0f0;padding:5px;">Dirección específica</h5>

                            <div class="row g-3" id="direccionEspecifica">

                                <div class="col-md-6 mb-3">
                                    <label for="provincia" class="form-label fw-bold text-primary">Provincia</label>
                                    <select class="form-select" id="provincia" name="provincia">
                                        <option value="" selected disabled>Selecciona...</option>
                                        @foreach ($provincia as $item)
                                            <option value="{{ $item->id }}">{{ $item->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- <div class="col-md-6 mb-3">
                                    <label for="distrito" class="form-label fw-bold text-primary">Distrito</label>
                                    <select class="form-select" id="distrito" name="distrito">
                                        <option value="" selected disabled>Selecciona una provincia primero</option>
                                    </select>
                                </div> --}}

                                <div class="col-md-6 mb-3">
                                    <label for="distrito" class="form-label fw-bold text-primary">Distrito</label>
                                    <select class="form-select @error('distrito') is-invalid @enderror"
                                            id="distrito" name="distrito" required>
                                        @if (old('distrito'))
                                        <option value="{{ old('distrito') }}" selected>Seleccionado previamente</option>
                                        @else
                                        <option value="" disabled selected>Selecciona una provincia primero</option>
                                        @endif
                                    </select>
                                    @error('distrito') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                {{-- <div class="col-md-6 mb-3">
                                    <label for="corregimiento" class="form-label fw-bold text-primary">Corregimiento</label>
                                    <select class="form-select" id="corregimiento" name="corregimiento">
                                        <option value="" selected disabled>Selecciona un distrito primero</option>
                                    </select>
                                </div> --}}

                                
                            <div class="col-md-6 mb-3">
                                <label for="corregimiento" class="form-label fw-bold text-primary">Corregimiento</label>
                                <select class="form-select @error('corregimiento') is-invalid @enderror"
                                        id="corregimiento" name="corregimiento" required>
                                    @if (old('corregimiento'))
                                    <option value="{{ old('corregimiento') }}" selected>Seleccionado previamente</option>
                                    @else
                                    <option value="" disabled selected>Selecciona un distrito primero</option>
                                    @endif
                                </select>
                                @error('corregimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                                <!-- Tipo de vivienda -->
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-primary d-block">Tipo de vivienda</label>
                                    <div class="btn-group w-100" role="group" aria-label="Tipo de vivienda">
                                    <input type="radio" class="btn-check" name="tipo_vivienda" id="tv_casa" value="Casa" checked>
                                    <label class="btn btn-outline-primary" for="tv_casa">Casa</label>

                                    <input type="radio" class="btn-check" name="tipo_vivienda" id="tv_edificio" value="Edificio">
                                    <label class="btn btn-outline-primary" for="tv_edificio">Edificio / PH</label>

                                    <input type="radio" class="btn-check" name="tipo_vivienda" id="tv_hotel" value="Edificio">
                                    <label class="btn btn-outline-primary" for="tv_hotel">Hotel</label>

                                    </div>
                                </div>

                                <!-- Comunes -->
                                <div class="col-md-4">
                                    <label for="barrio" class="form-label fw-bold text-primary">Barrio / Urbanización</label>
                                    <input type="text" class="form-control" id="barrio" name="barrio" placeholder="Ej.: Villa Zaita" value="{{ old('barrio') }}">
                                </div>

                                <div class="col-md-4">
                                    <label for="calle" class="form-label fw-bold text-primary">Calle / Avenida</label>
                                    <input type="text" class="form-control" id="calle" name="calle" placeholder="Ej.: Calle 3ra, Av. Centenario" value="{{ old('calle') }}">
                                </div>

                                <!-- Solo CASA -->
                                <div class="col-md-4" id="grupoCasa">
                                    <label for="numero_casa" class="form-label fw-bold text-primary">N° de casa</label>
                                    <input type="text" class="form-control" id="numero_casa" name="numero_casa" placeholder="Ej.: 123" inputmode="numeric" value="{{ old('numero_casa') }}">
                                </div>

                                <!-- Solo EDIFICIO -->
                                <div class="col-md-4 d-none" id="grupoEdificio_nombre">
                                    <label for="nombre_edificio" class="form-label fw-bold text-primary">Nombre del edificio / PH</label>
                                    <input type="text" class="form-control" id="nombre_edificio" name="nombre_edificio" placeholder="Ej.: PH Torres del Sol" value="{{ old('nombre_edificio') }}">
                                </div>

                                <div class="col-md-2 d-none" id="grupoEdificio_piso">
                                    <label for="piso" class="form-label fw-bold text-primary">Piso</label>
                                    <input type="number" class="form-control" id="piso" name="piso" min="0" max="200" placeholder="Ej.: 7" value="{{ old('piso') }}">
                                </div>

                                <div class="col-md-2 d-none" id="grupoEdificio_apto">
                                    <label for="apartamento" class="form-label fw-bold text-primary">Apartamento</label>
                                    <input type="text" class="form-control" id="apartamento" name="apartamento" placeholder="Ej.: 7B" value="{{ old('apartamento') }}">
                                </div>

                                <!-- Solo Hotel -->
                                <div class="col-md-4 d-none" id="grupoHotel">
                                    <label for="nombre_hotel" class="form-label fw-bold text-primary">Nombre de Hotel</label>
                                    <input type="text" class="form-control" id="nombre_hotel" name="nombre_hotel" placeholder="Ej.: Hotel XYZ" value="{{ old('nombre_hotel') }}">
                                </div>

                                <!-- Extra útiles -->
                                <div class="col-md-12">
                                    <label for="punto_referencia" class="form-label fw-bold text-primary">Punto de referencia</label>
                                    <input type="text" class="form-control" id="punto_referencia" name="punto_referencia" placeholder="Ej.: Frente al minisúper XYZ / Cerca de la parada" value="{{ old('punto_referencia') }}">
                                </div>

                            </div>

                            <hr class="mb-4">
                            <h5 class="form-label fw-bold text-primary" style="background-color:#f0f0f0;padding:5px;">Documentos requeridos</h5>

                            <div class="row" id="docsRequeridos">

                                {{-- 1) PRUEBA DE DOMICILIO (elegir UNA opción) --}}
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold text-primary d-block"> Prueba de domicilio (elija UNA)</label>
                                    <div class="btn-group flex-wrap w-100 gap-2" role="group" aria-label="Prueba de domicilio">
                                    <input type="radio" class="btn-check" name="domicilio_opcion" id="dom_escritura" value="escritura" checked>
                                    <label class="btn btn-outline-primary" for="dom_escritura">Escritura pública (notariada)</label>

                                    <input type="radio" class="btn-check" name="domicilio_opcion" id="dom_arrendamiento" value="arrendamiento">
                                    <label class="btn btn-outline-primary" for="dom_arrendamiento">Contrato de arrendamiento (notariado)</label>

                                    <input type="radio" class="btn-check" name="domicilio_opcion" id="dom_responsabilidad" value="responsabilidad">
                                    <label class="btn btn-outline-primary" for="dom_responsabilidad">Carta de responsabilidad (notariada)</label>

                                    <input type="radio" class="btn-check" name="domicilio_opcion" id="dom_juezpaz" value="juez_paz">
                                    <label class="btn btn-outline-primary" for="dom_juezpaz">Certificación del Juez de Paz</label>

                                    <input type="radio" class="btn-check" name="domicilio_opcion" id="dom_reservahotel" value="reserva_hotel">
                                    <label class="btn btn-outline-primary" for="dom_reservahotel">Reserva de Hotel</label>

                                    </div>
                                    <small id="domicilioNota" class="text-muted d-block mt-2">Requiere notaría.</small>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="domicilio_archivo" class="form-label fw-bold text-primary">Adjuntar documento de domicilio</label>
                                    <input class="form-control" type="file" id="domicilio_archivo" name="domicilio_archivo" accept=".pdf,.jpg,.jpeg,.png" required>
                                    <div class="form-text">Formatos permitidos: PDF, JPG o PNG. Documento vigente y legible.</div>
                                </div>

                                {{-- 2) RECIBO DE SERVICIO --}}
                                <div id="grupoRecibo">
                                    <div class="col-12 mb-2">
                                        <label class="form-label fw-bold text-primary d-block"> Recibo de servicio</label>
                                        <div class="btn-group flex-wrap w-100 gap-2" role="group" aria-label="Tipo de recibo">
                                        <input type="radio" class="btn-check" name="recibo_tipo" id="recibo_mio" value="propio" checked>
                                        <label class="btn btn-outline-primary" for="recibo_mio">A mi nombre</label>

                                        <input type="radio" class="btn-check" name="recibo_tipo" id="recibo_tercero" value="tercero">
                                        <label class="btn btn-outline-primary" for="recibo_tercero">A nombre de tercero</label>
                                        </div>
                                        <small class="text-muted d-block mt-2">Recibo de luz, agua, teléfono o internet.</small>
                                    </div>

                                    <div class="col-md-6 mb-4">
                                        <label for="recibo_archivo" class="form-label fw-bold text-primary">Adjuntar recibo</label>
                                        <input class="form-control" type="file" id="recibo_archivo" name="recibo_archivo" accept=".pdf,.jpg,.jpeg,.png" required>
                                        <div class="form-text">No requiere notaría si está a su nombre.</div>
                                    </div>

                                    {{-- Campos adicionales solo si es de tercero --}}
                                    <div class="col-md-6 mb-4 d-none" id="recibo_notariado_group">
                                        <label for="recibo_notariado_archivo" class="form-label fw-bold text-primary">Comprobante notariado del recibo</label>
                                        <input class="form-control" type="file" id="recibo_notariado_archivo" name="recibo_notariado_archivo" accept=".pdf,.jpg,.jpeg,.png">
                                        <div class="form-text">Obligatorio si el recibo está a nombre de otra persona.</div>
                                    </div>

                                    <div class="col-md-6 mb-4 d-none" id="cedula_titular_group">
                                        <label for="recibo_cedula_titular" class="form-label fw-bold text-primary">Cédula del titular del recibo (frente y reverso)</label>
                                        <input class="form-control" type="file" id="recibo_cedula_titular" name="recibo_cedula_titular[]" accept=".pdf,.jpg,.jpeg,.png" multiple>
                                        <div class="form-text">Cargue ambos lados (puede adjuntar 2 archivos o un PDF).</div>
                                    </div>
                                </div>

                                {{-- 3) CARNET MIGRATORIO --}}
                                <div class="col-12 mt-2">
                                    <label class="form-label fw-bold text-primary d-block"> Imagen del carnet migratorio</label>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="carnet_frente" class="form-label fw-bold text-primary">Anverso</label>
                                    <input class="form-control" type="file" id="carnet_frente" name="carnet_frente" accept=".jpg,.jpeg,.png,.pdf" required>
                                </div>

                                <div class="col-md-6 mb-4">
                                    <label for="carnet_reverso" class="form-label fw-bold text-primary">Reverso</label>
                                    <input class="form-control" type="file" id="carnet_reverso" name="carnet_reverso" accept=".jpg,.jpeg,.png,.pdf" required>
                                </div>

                            </div>

                            <hr class="mb-4">
                            <h5 class="form-label fw-bold text-primary" style="background-color: #f0f0f0; padding: 5px;">Comentario Adicional</h5>
                            <textarea class="form-control" name="comentario" rows="3" placeholder="Escribe un comentario"></textarea>

                        </div>
                    </div>

                    <!-- Botones -->
                    <div class="card-footer text-end">
                        <button id="guardarForm" type="button" class="btn btn-primary shadow">
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

