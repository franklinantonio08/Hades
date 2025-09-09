<div class="modal fade" id="modalRegistro" tabindex="-1" aria-labelledby="modalRegistroLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title d-flex align-items-center gap-2" id="modalRegistroLabel">
                    <i class="bi bi-people-search"></i> Buscar Familiar
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>

            {{-- <form id="formBuscar" autocomplete="off"> --}}

                <input type="hidden" id="csrfToken" name="_token" value="{{ csrf_token() }}">

                <div class="modal-body">
                    <p id="modalRegistroDesc" class="text-muted small mb-3">
                        Completa los criterios y presiona <strong>Buscar</strong>.
                    </p>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nombre" class="form-label text-primary fw-bold">Nombres</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ej.: Carlos Alberto" maxlength="100">
                            <div class="invalid-feedback">Ingresa un nombre válido.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="apellido" class="form-label text-primary fw-bold">Apellidos</label>
                            <input type="text" class="form-control" id="apellido" name="apellido" placeholder="Ej.: Pérez Gómez" maxlength="100">
                            <div class="invalid-feedback">Ingresa un apellido válido.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="ruex" class="form-label text-primary fw-bold">N° Ruex</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-hash"></i></span>
                                <input type="text" class="form-control" id="ruex" name="ruex" placeholder="Ej.: 102332" inputmode="numeric" pattern="^[0-9]{1,15}$" aria-describedby="ruexHelp">
                            </div>
                            <div id="ruexHelp" class="form-text">Solo números, hasta 15 dígitos.</div>
                            <div class="invalid-feedback">El Ruex debe contener solo números.</div>
                        </div>

                        @php
                        $esAbogado = isset($tipo_usuario) && $tipo_usuario === 'abogado';
                        @endphp

                        <div class="col-md-6">
                        <label class="form-label text-primary fw-bold">Afinidad</label>

                        <select class="form-select shadow-sm" id="afinidadId" name="afinidadId" {{ $esAbogado ? 'disabled' : '' }}>
                            @unless($esAbogado)
                            <option value="">Todos</option>
                            @endunless

                            @foreach ($afinidad as $value)
                            <option value="{{ $value->id }}"
                                {{ (string)$afinidad_preseleccionada === (string)$value->id ? 'selected' : '' }}>
                                {{ $value->descripcion }}
                            </option>
                            @endforeach
                        </select>

                        @if($esAbogado)
                            {{-- Si está disabled, enviamos un hidden para que el valor viaje en el submit --}}
                            <input type="hidden" name="afinidadId" value="1">
                        @endif
                        </div>


                        <div class="col-md-6">
                            <label for="genero" class="form-label text-primary fw-bold">Género</label>
                            <select class="form-select" id="genero" name="genero">
                                <option value="" selected>Todos</option>
                                <option value="Masculino">Masculino</option>
                                <option value="Femenino">Femenino</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="fecha_nacimiento" class="form-label text-primary fw-bold">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" max="{{ date('Y-m-d') }}">
                            {{-- <div class="form-text">Opcional.</div> --}}
                        </div>
                    </div>
                </div>

                <div id="DivResultado_busqueda" class="px-3 pb-3 d-none">
                    <hr class="mt-0">
                    <h5 class="text-primary fw-bold mb-3">Resultados de Búsqueda</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="tablaResultados">
                        <thead class="table-primary text-center">
                            <tr>
                            <th class="bg-primary text-white">#</th>
                            <th class="bg-primary text-white">Nombre</th>
                            <th class="bg-primary text-white">Documento</th>
                            <th class="bg-primary text-white">Género</th>
                            <th class="bg-primary text-white">Nacionalidad</th>
                            <th class="bg-primary text-white">Fecha Nacimiento</th>
                            <th class="bg-primary text-white">Detalle</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        </table>
                    </div>
                </div>

                <div class="modal-footer bg-light d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <div id="buscarSpinner" class="spinner-border spinner-border-sm text-primary d-none" role="status" aria-hidden="true"></div>
                        <span id="buscarEstado" class="text-muted small d-none">Buscando…</span>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" id="btnLimpiar" class="btn btn-outline-secondary">
                            <i class="bi bi-eraser"></i> Limpiar
                        </button>
                        <button type="submit" id="btnBuscar" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </button>
                    </div>
                </div>

            {{-- </form> --}}
        </div>
    </div>
</div>
