<div class="modal fade bs-example-modal-lg" id="agregarFamiliarModal" tabindex="-1" role="dialog" aria-labelledby="messageTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h4 class="modal-title" id="messageTitle">Agregar Familiar</h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="agregarFamiliarForm">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="modalPrimerNombre">Primer Nombre</label>
                            <input type="text" class="form-control" id="modalPrimerNombre" name="modalPrimerNombre" placeholder="Primer Nombre">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="modalSegundoNombre">Segundo Nombre</label>
                            <input type="text" class="form-control" id="modalSegundoNombre" name="modalSegundoNombre" placeholder="Segundo Nombre">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="modalPrimerApellido">Primer Apellido</label>
                            <input type="text" class="form-control" id="modalPrimerApellido" name="modalPrimerApellido" placeholder="Primer Apellido">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="modalSegundoApellido">Segundo Apellido</label>
                            <input type="text" class="form-control" id="modalSegundoApellido" name="modalSegundoApellido" placeholder="Segundo Apellido">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="modalFechaNacimiento">Fecha de Nacimiento</label>
                            <input type="date" class="form-control" id="modalFechaNacimiento" name="modalFechaNacimiento">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="modalDocumento">Documento</label>
                            <input type="text" class="form-control" id="modalDocumento" name="modalDocumento" placeholder="Documento">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="modalGenero">Género</label>
                            <select class="form-control" id="modalGenero" name="modalGenero">
                                <option value="" selected disabled>Selecciona...</option>
                                @foreach (['Masculino', 'Femenino'] as $value)
                                    <option value="{{ $value }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="modalAfinidad">Afinidad</label>
                            <select class="form-control" id="modalAfinidad" name="modalAfinidad">
                                <option value="" selected disabled>Selecciona...</option>
                                @foreach ($RIDAfinidad as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="modalNacionalidad">Nacionalidad</label>
                            <select class="form-control" id="modalNacionalidad" name="modalNacionalidad">
                                <option value="" selected disabled>Selecciona...</option>
                                @foreach ($RIDPaises as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->nacionalidad }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="modalPais">País de Residencia</label>
                            <select class="form-control" id="modalPais" name="modalPais">
                                <option value="" selected disabled>Selecciona...</option>
                                @foreach ($RIDPaises as $key => $value)
                                    <option value="{{ $value->id }}">{{ $value->pais }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <button type="button" id="guardarFamiliarBtn" class="btn btn-primary">Guardar</button>
            </div>
        </div>
    </div>
</div>
