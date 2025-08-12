@section('scripts')

<script>
	var token = '{{ csrf_token() }}';
</script>
	
<script type="text/javascript" src="{{ asset('../js/admin/RIDmigrantes/RIDmigrantes.js') }}"></script>
<script src="{{ asset('../js/comun/messagebasicModal.js') }}"></script>

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
                            
                            <input type="hidden" id="migranteId" name="migranteId" value="{{$RIDmigrantes->id}}" class="form-control text-right" placeholder="">

                        <div class="col-lg-6 m-b-10">

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;" >Primer Nombre</span>
                                    <input type="text" class="form-control" id="primerNombre" name="primerNombre" placeholder="" value="{{$RIDmigrantes->primerNombre}}">
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;" >Segundo Nombre</span>
                                    <input type="text" class="form-control" id="segundoNombre" name="segundoNombre" placeholder="" value="{{$RIDmigrantes->segundoNombre}}">
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;" >Primer Apellido</span>
                                    <input type="text" class="form-control" id="primerApellido" name="primerApellido" placeholder="" value="{{$RIDmigrantes->primerApellido}}">
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;" >Segundo Apellido</span>
                                    <input type="text" class="form-control" id="segundoApellido" name="segundoApellido" placeholder="" value="{{$RIDmigrantes->segundoApellido}}">
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;">Fecha de Nacimiento</span>
                                    <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" value="{{ $RIDmigrantes->fechaNacimiento }}">
                                </div>                             
                               
                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;" >Documento</span>
                                    <input type="text" class="form-control" id="documento" name="documento" placeholder="" value="{{$RIDmigrantes->documento}}">
                                </div>
                               

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;">Género</span>
                                    <select class="form-control" id="genero" name="genero">
                                        <option value="Masculino" {{ $RIDmigrantes->genero == 'Masculino' ? 'selected' : '' }}>Masculino</option>
                                        <option value="Femenino" {{ $RIDmigrantes->genero == 'Femenino' ? 'selected' : '' }}>Femenino</option>
                                    </select>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;">Afinidad</span>
                                    <select class="form-control" id="afinidad" name="afinidad">
                                        <option value="{{ $RIDmigrantes->afinidadId }}" selected>{{ $RIDmigrantes->afinidad }}</option>
                                        @foreach ($afinidades as $afinidad)
                                            @if ($afinidad->id != $RIDmigrantes->afinidadId)
                                                <option value="{{ $afinidad->id }}">{{ $afinidad->descripcion }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;">Nacionalidad</span>
                                    <select class="form-control" id="nacionalidad" name="nacionalidad">
                                        <option value="{{ $RIDmigrantes->nacionalidadId }}" selected>{{ $RIDmigrantes->nacionalidad }}</option>
                                        @foreach ($paises as $pais)
                                            @if ($pais->id != $RIDmigrantes->nacionalidadId)
                                                <option value="{{ $pais->id }}">{{ $pais->pais }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>

                                
                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;">Pais de Residencia</span>
                                    <select class="form-control" id="pais" name="pais">
                                        <option value="{{ $RIDmigrantes->paisId }}" selected>{{ $RIDmigrantes->pais }}</option>
                                        @foreach ($paises as $pais)
                                            @if ($pais->id != $RIDmigrantes->paisId)
                                                <option value="{{ $pais->id }}">{{ $pais->pais }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                

                                
                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;">Puesto de Control</span>
                                    <select class="form-control" id="puestoControl" name="puestoControl">
                                        <option value="{{ $RIDmigrantes->puestoId }}" selected>{{ $RIDmigrantes->puestoControl }}</option>
                                        @foreach ($puestosControl as $puesto)
                                            @if ($puesto->id != $RIDmigrantes->puestoId)
                                                <option value="{{ $puesto->id }}">{{ $puesto->descripcion }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>


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
@endsection



