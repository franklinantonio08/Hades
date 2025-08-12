@section('scripts')

<script>
	var token = '{{ csrf_token() }}';
</script>
	
<script type="text/javascript" src="{{ asset('../js/dist/colaboradores/colaboradores.js') }}"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

@stop

@extends('layouts.admin')

@section('content')
   
	<div class="col-lg-12">
        <div class="card mb-4">
			
            <div class="card-body p-4">
                <div class="row">
                    <div class="col">
                        <div class="card-title fs-4 fw-semibold">Colaboradores</div>
                    </div>
                </div>
			</div>

            <div class="table-responsive">

                    <!-- ACTION BUTTONS -->
                    <div class="row">

                        @include('includes/errors')
                        @include('includes/success')

                    </div>
                   
                <!-- Formulario -->

                <div class="container px-2 my-2">
                            {{ csrf_field() }}
                        <div class="col-lg-6 m-b-10">

                            <div class="form-floating mb-3">
                                <input class="form-control" id="codigo" name="codigo" type="text" placeholder="codigo" readonly value="{{$colaboradores->codigo}}"/>
                                <label for="codigo">codigo</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="cedula" name="cedula" type="text" placeholder="cedula" readonly value="{{$colaboradores->cedula}}"/>
                                <label for="cedula">Cedula</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="nombre" name="nombre" type="text" placeholder="nombre" readonly value="{{$colaboradores->nombre }} {{$colaboradores->apellido}}"/>
                                <label for="nombre">Nombre</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="correo" name="correo" type="text" placeholder="correo" readonly value="{{$colaboradores->correo}}"/>
                                <label for="correo">Correo</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="telefono" name="telefono" type="text" placeholder="telefono" readonly value="{{$colaboradores->telefono}}"/>
                                <label for="telefono">Telefono</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="tipoSangre" name="tipoSangre" type="text" placeholder="tipoSangre" readonly value="{{$colaboradores->tipoSangre}}"/>
                                <label for="tipoSangre">Tipo de Sangre</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="genero" name="genero" type="text" placeholder="genero" readonly value="{{$colaboradores->genero}}"/>
                                <label for="genero">Genero</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="tipoUsuario" name="tipoUsuario" type="text" placeholder="tipoUsuario" readonly value="{{$colaboradores->tipoUsuario}}"/>
                                <label for="tipoUsuario">Tipo de Usuario</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="departamento" name="departamento" type="text" placeholder="departamento" readonly value="{{$colaboradores->departamento}}"/>
                                <label for="departamento">Departamento</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="posicion" name="posicion" type="text" placeholder="posicion" readonly value="{{$colaboradores->posicion}}"/>
                                <label for="posicion">Posicion</label>
                            </div>

                            <div class="form-floating mb-3">
                                <input class="form-control" id="estatus" name="estatus" type="text" placeholder="estatus" readonly value="{{$colaboradores->estatus}}"/>
                                <label for="estatus">Estatus</label>
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



