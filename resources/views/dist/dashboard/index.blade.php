@section('scripts')

<script>
    var BASEURL = '{{ url()->current() }}';
	var token = '{{ csrf_token() }}';
</script>

<script src="{{ asset('../js/comun/messagebasicModal.js') }}"></script>
<script type="text/javascript" src="{{ asset('../js/dist/dashboard/dashboard.js') }}"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

@stop

@extends('layouts.dashboard')

@section('content')
   
 <!-- ACTION BUTTONS -->
 <div class="row">

    @include('includes/errors')
    @include('includes/success')

</div>

	<div class="col-lg-12">
        <div class="card mb-4">
			
                   
                <!-- Formulario -->

                <div class="container-fluid px-2 my-2">
                    <form id="nuevoregistro" name="nuevoregistro" method="POST" action="{{ url()->current('/dist/departamento/nuevo') }}" enctype="multipart/form-data" autocomplete="off">
                            {{ csrf_field() }}
                            <div class="row justify-content-center">

                                <div class="cubiculo col-lg-4 m-b-5 mx-auto">

                                    @foreach ($cubiculo as $key => $value)
                                        @if ($value->posicion <= 7)
                                            <div class="input-group mb-3">
                                                <span class="btn btn-primary text-white btn-lg" style="width: 50%;">MÓDULO {{ $value->posicion }}</span>
                                                <input type="text" class="form-control form-control-lg" id="turno{{ $value->id }}" name="{{ $value->llamado }}" value="{{ $value->codigo }}" >
                                            </div>
                                        @endif
                                    @endforeach
                                     
                                </div>

                                <div class="col-lg-7 m-b-6">
                                    <!-- Contenido de Publicidad -->
                                    <div class="publicidad p-3">
                                        <!-- Inserta aquí tu reproductor de video publicitario con autoplay y loop -->
                                        <video controls autoplay loop id="videoPublicidad" style="width: 100%;">
                                            <source src="{{ asset('../js/dist/departamento/acodeco.mp4') }}" type="video/mp4">
                                            Tu navegador no soporta el elemento de video.
                                        </video>
                                    </div>
                    
                                    <script>
                                        // Obtén el elemento de video
                                        var videoPublicidad = document.getElementById("videoPublicidad");
                                    
                                        // Agrega un evento para reiniciar la reproducción cuando el video termina
                                        videoPublicidad.addEventListener("ended", function() {
                                            videoPublicidad.play();
                                        });
                                    </script>
                                </div>

                                
                            </div>
                    </form>
                </div>

                
            
                <!-- Fin Formulario-->

           
	    </div>   

       


        
        
    </div>  



</div>

@include('includes/messagebasicmodal')
@endsection



