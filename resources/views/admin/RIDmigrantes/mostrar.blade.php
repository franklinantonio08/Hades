@section('scripts')

<script>
	var token = '{{ csrf_token() }}';
</script>
	
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
                            {{ csrf_field() }}
                        <div class="col-lg-6 m-b-10">

                            <div class="form-floating mb-3">
                                <input class="form-control" id="primerNombre" name="primerNombre" type="text" placeholder="Primer Nombre" readonly value="{{$RIDmigrantes->primerNombre}}"/>
                                <label for="primerNombre">Primer Nombre</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="segundoNombre" name="segundoNombre" type="text" placeholder="Segundo Nombre" readonly value="{{$RIDmigrantes->segundoNombre}}"/>
                                <label for="segundoNombre">Segundo Nombre</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="primerApellido" name="primerApellido" type="text" placeholder="Primer Apellido" readonly value="{{$RIDmigrantes->primerApellido}}"/>
                                <label for="primerApellido">Primer Apellidos</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="segundoApellido" name="segundoApellido" type="text" placeholder="Segundo Apellido" readonly value="{{$RIDmigrantes->segundoApellido}}"/>
                                <label for="segundoApellido">Segundo Apellido</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="fechaNacimiento" name="fechaNacimiento" type="text" placeholder="Fecha de Nacimiento" readonly value="{{$RIDmigrantes->fechaNacimiento}}"/>
                                <label for="fechaNacimiento">Fecha de Nacimiento</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="codigo" name="codigo" type="text" placeholder="Codigo" readonly value="{{$RIDmigrantes->codigo}}"/>
                                <label for="codigo">Codigo</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="documento" name="documento" type="text" placeholder="Documento" readonly value="{{$RIDmigrantes->documento}}"/>
                                <label for="documento">Documento</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="region" name="region" type="text" placeholder="Region" readonly value="{{$RIDmigrantes->region}}"/>
                                <label for="region">Region</label>
                            </div>

                                <div class="form-floating mb-3">
                                    <input class="form-control" id="pais" name="pais" type="text" placeholder="Pais" readonly value="{{$RIDmigrantes->pais}}"/>
                                    <label for="pais">Pais de Residencia</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input class="form-control" id="nacionalidad" name="nacionalidad" type="text" placeholder="Nacionalidad" readonly value="{{$RIDmigrantes->nacionalidad}}"/>
                                    <label for="nacionalidad">Nacionalidad</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input class="form-control" id="genero" name="genero" type="text" placeholder="Genero" readonly value="{{$RIDmigrantes->genero}}"/>
                                    <label for="genero">Genero</label>
                                </div>      
                                
                                <div class="form-floating mb-3">
                                    <input class="form-control" id="tipo" name="tipo" type="text" placeholder="Tipo" readonly value="{{$RIDmigrantes->tipo}}"/>
                                    <label for="Tipo">Tipo</label>
                                </div>  

                                <div class="form-floating mb-3">
                                    <input class="form-control" id="afinidad" name="afinidad" type="text" placeholder="Afinidad" readonly value="{{$RIDmigrantes->afinidad}}"/>
                                    <label for="Afinidad">Afinidad</label>
                                </div> 
                                
                               <!-- <div class="form-floating mb-3">
                                    <textarea class="form-control" id="comentario" name="comentario" type="text" placeholder="Comentario" style="height: 10rem;" ></textarea>
                                    <label for="comentario">Comentario</label>
                                </div> -->

                                <!-- ACTION BUTTONS -->
                                    <div class="form-group row">
                                        <div class="offset-12 col-12">
                                            <a href="{{ url()->previous() }}"  class="btn btn-secondary text-white"><i class="fa fa-remove m-r-5"></i> Volver</a>
                                        </div>
                                    </div>
                                <!-- end ACTION BUTTONS -->

                               
                        </div>
                </div>
            
                <!-- Fin Formulario-->

            </div>
	    </div>    
    </div>  



</div>

@endsection



