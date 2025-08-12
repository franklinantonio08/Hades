@section('scripts')

<script>
    var BASEURL = '{{ url()->current() }}';
	var token = '{{ csrf_token() }}';
</script>
	
{{-- <script type="text/javascript" src="{{ asset('../js/dist/departamento/departamento.js') }}"></script>  --}}
<script type="text/javascript" src="{{ asset('../js/dist/solicitud/solicitud.js') }}"></script>
<script src="{{ asset('../js/comun/messagebasicModal.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    // Inicializar Flatpickr
    flatpickr("#fechaNacimiento", {
        dateFormat: "Y-m-d",
        maxDate: "today", // Establecer la fecha máxima como hoy
        locale: {
            firstDayOfWeek: 1, // Establecer el primer día de la semana como lunes
            weekdays: {
                shorthand: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
                longhand: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
            },
            months: {
                shorthand: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                longhand: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            },
        }
    });
</script>


<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">


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
			
           <!-- <div class="card-body p-4">
                <div class="row">
                    <div class="col">
                        <div class="card-title fs-4 fw-semibold">Solicitud</div>
                    </div>
                </div>
			</div> -->

            <div class="table-responsive">

                
                <!-- Formulario -->

                <div class="container-fluid px-2 my-2">
                    <form id="editarregistro" name="editarregistro" autocomplete="off"  method="POST" action="{{ url()->current('/dist/solicitud/nuevo') }}" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            
                            <input type="hidden" id="solicitudId" name="solicitudId" value="{{$solicitud->id}}" class="form-control text-right" >

                <div class="row">


                        <div class="col-lg-5 m-b-6">

                            <div class="col">
                                <hr style="border-color: gray;"> 
                                <div class="card-title fs-5 fw-semibold">Solicitud</div>
                                <hr style="border-color: gray;"> 
                            </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 150px;" >Tipo de Atencion</span>
                                    <input type="text" class="form-control" id="tipoAtencion" name="tipoAtencion" placeholder="" value="{{$solicitud->descripcion}}">
                                    <input type="hidden" id="IdTipoAtencion" name="IdTipoAtencion" value="{{$solicitud->IdTipoAtencion}}" class="form-control text-right" >
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 150px;" >Departamento</span>
                                    <input type="text" class="form-control" id="departamento" name="departamento" placeholder="" value="{{$solicitud->departamentoNombre}}">
                                    <input type="hidden" id="departamentoId" name="departamentoId" value="{{$solicitud->departamentoId}}" class="form-control text-right" >
                                </div>

                                <div class="input-group mb-3">
                                    <label class="input-group-text" style="width: 150px;" for="inputGroupSelect01">Estatus</label>
                                    <select class="form-select" id="estatus" name="estatus">
                                        <option value="Activo" {{ $solicitud->estatus === 'Activo' ? 'selected' : '' }}>Activo</option>
                                        <option value="Resuelto" {{ $solicitud->estatus === 'Resuelto' ? 'selected' : '' }}>Resuelto</option>
                                    </select>
                                </div>
                                
                                
                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="comentario" name="comentario" type="text" placeholder="Comentario" style="height: 10rem;" ></textarea>
                                    <label for="comentario">Comentario</label>
                                </div>

                            </div>

                        <div class="col-lg-5 m-b-6">

                            <div class="col">
                                <hr style="border-color: gray;"> 
                                <div class="card-title fs-5 fw-semibold">Datos del Consumidor</div>
                                <hr style="border-color: gray;"> 
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 150px;" >Cedula</span>
                                <input type="text" class="form-control" id="cedula" name="cedula" placeholder="" value="{{$solicitud->cedula}}">

                            </div>
    
                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 150px;">Nombre</span>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="" value="{{$solicitud->nombre}}">
                            </div>
    
                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 150px;">Apellido</span>
                                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="" value="{{$solicitud->apellido}}">
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 150px;">Fecha de Nacimiento</span>
                                <input type="text" class="form-control" id="fechaNacimiento" name="fechaNacimiento" placeholder="Selecciona una fecha" value="{{$solicitud->fechaNacimiento}}">
                            </div>

                            <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 150px;">Correo</span>
                                <input type="text" class="form-control" id="correo" name="correo" placeholder="@example.com" value="{{$solicitud->correo}}">
                            </div>
    
                              <div class="input-group mb-3">
                                <span class="input-group-text" style="width: 150px;">Teléfono</span>
                                <input type="text" class="form-control" id="telefono" name="telefono" placeholder="" value="{{$solicitud->cedula}}">
                            </div>

                              <div class="input-group mb-3">
                                <label class="input-group-text" style="width: 150px;" for="inputGroupSelect01">Genero</label>
                                <select class="form-select" id="genero" name="genero">
                                    <option value="Masculino" {{ $solicitud->genero === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="Femenino" {{ $solicitud->genero === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                </select>
                            </div>


                              <div class="input-group mb-3">
                                <label class="input-group-text" style="width: 150px;" for="inputGroupSelect01">Tipo de Usuario</label>
                                <select class="form-select" id="tipoUsuario" name="tipoUsuario">
                                    <option value="" selected disabled>Seleccionar...</option>	
                                    <option value="Normal" {{ $solicitud->tipoConsumidor === 'Normal' ? 'selected' : '' }} >Normal</option>										
                                    <option value="Embarazada" {{ $solicitud->tipoConsumidor === 'Embarazada' ? 'selected' : '' }} >Embarazada</option>										
                                    <option value="Discapacitado" {{ $solicitud->tipoConsumidor === 'Discapacitado' ? 'selected' : '' }} >Discapacitado</option>										
                                    <option value="Jubilado" {{ $solicitud->tipoConsumidor === 'Jubilado' ? 'selected' : '' }} >Jubilado</option>	
                                </select>
                              </div>

                        </div>
                    </div>


                                <!-- ACTION BUTTONS -->
                                <div class="form-group row">
                                    <div class="offset-12 col-12">
                                        <button id="submitForm" name="submitForm" type="submit" class="btn btn-primary text-white"><i class="fa fa-check m-r-5"></i> Guardar</button>
                                        <a href="{{ url()->previous() }}"  class="btn btn-danger text-white"><i class="fa fa-remove m-r-5"></i> Cancelar</a>
                                    </div>
                                </div>
                            <!-- end ACTION BUTTONS -->
                    </form>
                </div>
            
                <!-- Fin Formulario-->

            </div>
	    </div>    
    </div>  



</div>

@include('includes/messagebasicmodal')
@endsection



