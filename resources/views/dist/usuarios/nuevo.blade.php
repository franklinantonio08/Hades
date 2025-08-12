@section('scripts')

<script>
    var BASEURL = '{{ url()->current() }}';
	var token = '{{ csrf_token() }}';
</script>

<script src="{{ asset('../js/comun/messagebasicModal.js') }}"></script>
<script type="text/javascript" src="{{ asset('../js/admin/RIDmigrantes/RIDmigrantes.js') }}"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

@stop

@extends('layouts.admin')

@section('content')

<!-- ACTION BUTTONS -->
<div class="row">

    @include('includes/errors')
    @include('includes/success')

</div>
   
	<div class="col-lg-12">
        <div class="card mb-4">
			
            <div class="card-body p-4">
                <div class="row">
                    <div class="col">
                        <div class="card-title fs-4 fw-semibold">Registro de Migrantes</div>
                    </div>
                </div>
			</div>

            <div class="table-responsive">

                <!-- Formulario -->

                <div class="container-fluid px-2 my-2">
                    <form id="nuevoregistro" name="nuevoregistro" method="POST" action="{{ url()->current('/admin/RIDmigrantes/nuevo') }}" enctype="multipart/form-data" autocomplete="off">
                            {{ csrf_field() }}
                        <div class="col-lg-6 m-b-10">

                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 180px;" >Primer Nombre</span>
                                <input type="text" class="form-control" id="primerNombre" name="primerNombre" placeholder="">
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 180px;" >Segundo Nombre</span>
                                <input type="text" class="form-control" id="segundoNombre" name="segundoNombre" placeholder="">
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 180px;" >Primer Apellido</span>
                                <input type="text" class="form-control" id="primerApellido" name="primerApellido" placeholder="">
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 180px;" >Segundo Apellido</span>
                                <input type="text" class="form-control" id="segundoApellido" name="segundoApellido" placeholder="">
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 180px;" >Fecha de Nacimiento</span>
                                <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" placeholder="">
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 180px;" >Documento</span>
                                <input type="text" class="form-control" id="documento" name="documento" placeholder="">
                            </div>

                            <div class="input-group mb-3">
                                <label class="input-group-text" style="width: 180px;" for="genero">Genero</label>
                                <select class="form-select" id="genero" name="genero">
                                    <option value="" selected disabled>Selecciona...</option>
                                    @foreach (['Masculino', 'Femenino'] as $value)										
                                    <option value="{{ $value }}">{{ $value }}</option>										
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group mb-3">
                                <label class="input-group-text" style="width: 180px;" for="afinidad">Afinidad</label>
                                <select class="form-select" id="afinidad" name="afinidad">
                                    <option value="" selected disabled>Selecciona...</option>
                                    @foreach ($RIDAfinidad as $key => $value) 								
                                    <option value="{{ $value->id }}">{{ $value->descripcion }}</option>											
                                    @endforeach
                                </select>
                            </div>

                            <div class="input-group mb-3">
                                <label class="input-group-text" style="width: 180px;" for="nacionalidad">Nacionalidad</label>
                                <select class="form-select" id="nacionalidad" name="nacionalidad" >
                                    <option value="" selected disabled>Selecciona...</option>
                                    @foreach ($RIDPaises as $key => $value) 										
                                    <option value="{{ $value->id }}">{{ $value->nacionalidad }}</option>										
                                    @endForeach
                                </select>
                              </div>
                            

                            <div class="input-group mb-3">
                                <label class="input-group-text" style="width: 180px;" for="pais">Pais de Residencia</label>
                                <select class="form-select" id="pais" name="pais">
                                    <option value="" selected disabled>Selecciona...</option>
                                    @foreach ($RIDPaises as $key => $value) 										
                                    <option value="{{ $value->id }}">{{ $value->pais }}</option>										
                                    @endForeach
                                </select>
                            </div>

                            <div class="input-group mb-3">
                                <label class="input-group-text" style="width: 180px;" for="puestoControl">Puesto de Control</label>
                                <select class="form-select" id="puestoControl" name="puestoControl">
                                    <option value="" selected disabled>Selecciona...</option>
                                    @foreach ($RIDPuestocontrol as $key => $value) 										
                                    <option value="{{ $value->id }}">{{ $value->descripcion }}</option>										
                                    @endForeach
                                </select>
                            </div>

                            <div class="input-group mb-3">
                                <label class="input-group-text" style="width: 180px;" for="estacionTemporal">Estaciones Temporales</label>
                                <select class="form-select" id="estacionTemporal" name="estacionTemporal">
                                    <option value="" selected disabled>Selecciona...</option>
                                    @foreach ($RIDEstaciontemporal as $key => $value) 										
                                    <option value="{{ $value->id }}">{{ $value->descripcion }}</option>										
                                    @endForeach
                                </select>
                            </div>

                            {{-- <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 180px;">Familiares <span class="text-danger">&nbsp;*</span></span>
                                <input type="text" id="familiaresNombre" name="familiaresNombre" class="form-control" placeholder="Buscar..." readonly>
                                <input type="hidden" id="estacionTemporal" name="estacionTemporal"  class="form-control" value="">
                                <span class="input-group-btn">
                                    <button type="button" id="agregarFamiliar" name="agregarFamiliar" class="btn waves-effect btn-warning" data-toggle="modal" data-target="#agregarFamiliarModal"><i class="fa fa-plus"></i></button>
                                </span>
                            </div> --}}
                                
                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 180px;">Familiares <span class="text-danger">&nbsp;*</span></span>
                                <input type="text" id="familiaresNombre" name="familiaresNombre" class="form-control" placeholder="Buscar..." readonly>
                                <span class="input-group-btn">
                                    <button type="button" id="agregarFamiliar" name="agregarFamiliar" class="btn waves-effect btn-warning"><i class="fa fa-plus"></i></button>
                                </span>
                            </div>
                            
                            <div id="familiaresList"></div> <!-- Contenedor para los familiares agregados -->
                            
                           
                            <div class="form-floating mb-3">
                                <textarea class="form-control" id="comentario" name="comentario" type="text" placeholder="Comentario" style="height: 10rem;" ></textarea>
                                <label for="comentario">Comentario</label>
                            </div>

                        <!-- ACTION BUTTONS -->
                            <div class="form-group row">
                                <div class="offset-12 col-12">
                                    <button id="submitForm" name="submitForm" type="submit" class="btn btn-primary text-white"><i class="fa fa-check m-r-5"></i> Guardar</button>
                                    <a href="{{ url()->previous() }}"  class="btn btn-danger text-white"><i class="fa fa-remove m-r-5"></i> Cancelar</a>
                                </div>
                            </div>
                        <!-- end ACTION BUTTONS -->

                               
                        </div>
                    </form>
                </div>
            
                <!-- Fin Formulario-->

            </div>
	    </div>    
    </div>  



</div>

@include('includes/messagebasicmodal')
@include('includes/agregarfamiliar')
@endsection



