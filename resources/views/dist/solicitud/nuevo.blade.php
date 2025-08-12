@section('scripts')

<script>
    var BASEURL = '{{ url()->current() }}';
	var token = '{{ csrf_token() }}';
</script>

<script src="{{ asset('../js/comun/messagebasicModal.js') }}"></script>
<script type="text/javascript" src="{{ asset('../js/dist/solicitud/solicitud.js') }}"></script>

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
			
         

            <div class="table-responsive">
                   
                <!-- Formulario -->

                <div class="container-fluid px-2 my-2">
                    
                            {{ csrf_field() }}
                        <div class="col-lg-12 mb-6">

                            <!-- Información Adicional -->
                            <div class="alert alert-info" role="alert">
                                <p><strong>Seleccione una ubicación y asegúrese de tener los documentos e información que necesitará.</strong></p>
                                <button id="nuevaSolicitudForm" name="nuevaSolicitudForm" type="submit" class="btn btn-primary text-white"><i class="fa fa-check m-r-5"></i> INICIAR UNA APLICACIÓN</button>
                                <br>                                
                                <strong>Se le pedirá su ID de aplicación y responder una pregunta de seguridad.</strong></p>
                                <button id="recuperaSolicitudForm" name="recuperaSolicitudForm" type="submit" class="btn btn-secondary text-white"><i class="fa fa-check m-r-5"></i> RECUPERAR UNA APLICACIÓN</button>
                                <br>
                                <p><strong>Información Adicional:</strong></p>
                                <ul>
                                    <li>Anote el ID de la aplicación que se muestra en la esquina superior derecha de la página. Si cierra la ventana del navegador, necesitará su ID para acceder a su aplicación nuevamente.</li>
                                    <li>Guarde su aplicación frecuentemente. El sistema se cerrará después de 20 minutos de inactividad y perderá toda la información no guardada.</li>
                                    <li>Lea más sobre visas de EE. UU. en travel.state.gov.</li>
                                    <li>Visite el sitio web de la Embajada o Consulado de EE. UU.</li>
                                </ul>
                            </div>

                            <!--Declaración de Confidencialidad -->
                            <div class="alert alert-secondary mt-3" role="alert">
                                <p><strong>Declaración de Confidencialidad:</strong></p>
                                <p>La carga pública para esta recopilación de información se estima en un promedio de 90 minutos por respuesta, incluyendo el tiempo requerido para buscar fuentes de datos existentes, reunir la documentación necesaria, proporcionar la información y/o documentos requeridos, y revisar la recopilación final. Usted no está obligado a proporcionar esta información a menos que esta recopilación muestre un número de control OMB válido. Si tiene comentarios sobre la precisión de esta estimación de carga y/o recomendaciones para reducirla, por favor envíelos a: <a href="mailto:PRA_BurdenComments@state.gov">PRA_BurdenComments@state.gov</a>.</p>
                                <p>La información solicitada en este formulario se pide de conformidad con la Sección 222 de la Ley de Inmigración y Nacionalidad. La Sección 222(f) de la INA establece que los registros del Departamento de Estado y de las oficinas diplomáticas y consulares de los Estados Unidos relacionados con la emisión y denegación de visas o permisos para ingresar a los Estados Unidos se considerarán confidenciales y se utilizarán únicamente para la formulación, enmienda, administración o aplicación de las leyes de inmigración, nacionalidad y otras leyes de los Estados Unidos. Se pueden hacer copias certificadas de dichos registros disponibles para un tribunal, siempre que el tribunal certifique que la información contenida en dichos registros es necesaria en un caso pendiente ante el tribunal.</p>
                            </div>

                            

                        </div>
                        
                    {{-- </form> --}}
                </div>

            </div>
	    </div>    
    </div>  

</div>

@include('includes/messagebasicmodal')
@endsection
