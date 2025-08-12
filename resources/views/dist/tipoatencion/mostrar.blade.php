@section('scripts')

<script>
	var token = '{{ csrf_token() }}';
</script>
	
<script type="text/javascript" src="{{ asset('../js/dist/tipoatencion/tipoatencion.js') }}"></script>

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
                        <div class="card-title fs-4 fw-semibold">Tipo de Atención</div>
                    </div>
                </div>
			</div>

            <div class="table-responsive">

                
                   
                <!-- Formulario -->

                <div class="container-fluid px-2 my-2">
                            {{ csrf_field() }}
                        <div class="col-lg-6 m-b-10">

                                <div class="form-floating mb-3">
                                    <input class="form-control" id="nombre" name="nombre" type="text" placeholder="Nombre" readonly value="{{$tipoatencion->descripcion}}"/>
                                    <label for="nombre">Nombre</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input class="form-control" id="departamento" name="departamento" type="text" placeholder="Departamento" readonly value="{{$tipoatencion->departamento}}"/>
                                    <label for="departamento">Departamento</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input class="form-control" id="codigo" name="codigo" type="text" placeholder="Codigo" readonly value="{{$tipoatencion->codigo}}"/>
                                    <label for="codigo">Codigo</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input class="form-control" id="prioridad" name="prioridad" type="text" placeholder="Codigo" readonly value="{{$tipoatencion->prioridad}}"/>
                                    <label for="prioridad">Prioridad</label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input class="form-control" id="estatus" name="estatus" type="text" placeholder="Estatus" readonly value="{{$tipoatencion->estatus}}"/>
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



