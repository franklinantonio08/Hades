@section('scripts')

<script>
    var BASEURL = '{{ url()->current() }}';
	var token = '{{ csrf_token() }}';
</script>
	
{{-- <script type="text/javascript" src="{{ asset('../js/dist/departamento/departamento.js') }}"></script> --}}
<script src="{{ asset('../js/comun/messagebasicModal.js') }}"></script>

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"> 
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

<link href="{{ asset('../css/tabs.css') }}" rel="stylesheet">


@stop

@extends('layouts.admin')

@section('content')



<div class="row">
	<div class="col-sm-12">
		
		<!--<ul class="nav nav-tabs navtab-custom nav-justified"  id="myTab">
			<li class="active tab">
				<a id='generalesTab' style="text-align: left !important;" href="#generales" data-toggle="tab" aria-expanded="false"  >
					<span class="visible-xs"><i class="fa fa-home"></i></span>
					<span class="hidden-xs ">Datos Generales</span>
				</a>
			</li>
			<li class="tab">
				<a id='permisosTab' style="text-align: left !important;" href="#permisos" data-toggle="tab" aria-expanded="false"  >
					<span class="visible-xs"><i class="fa fa-home"></i></span>
					<span class="hidden-xs ">Permisos</span>
				</a>
			</li>
		</ul> -->


        <ul class="nav nav-tabs nav-justified card-header-tabs" id="myTab">
            
            <li class="active tab">
              <a id='generalesTab' href="#generales" class="nav-link active" data-toggle="tab" href="#" aria-current="true">Informacion Personal</a>
            </li>

            {{-- <li class="tab">
              <a id='permisosTab' href="#permisos" class="nav-link" data-toggle="tab" href="#">Direccion</a>
            </li>

            <li class="tab">
              <a id='otrosTab' href="#otros" class="nav-link" data-toggle="tab" href="#"  aria-disabled="true">Estudios Realizados</a>
            </li>

            <li class="tab">
                <a id='otrosTab' href="#otros" class="nav-link" data-toggle="tab" href="#"  aria-disabled="true">Experiencias</a>
            </li>

            <li class="tab">
                <a id='otrosTab' href="#otros" class="nav-link" data-toggle="tab" href="#"  aria-disabled="true">Informacion Bancaria</a>
            </li>

            <li class="tab">
                <a id='otrosTab' href="#otros" class="nav-link" data-toggle="tab" href="#"  aria-disabled="true">Salario</a>
            </li> --}}


          </ul>

		<div class="tab-content">
			<!-- INICIO TAB GENERALES -->
			<div class="tab-pane active" id="generales">

                <form id="nuevoregistro" name="nuevoregistro" method="POST" action="{{ url()->current('/dist/departamento/nuevo') }}" enctype="multipart/form-data" autocomplete="off">
                    {{ csrf_field() }}
                <div class="col-lg-5 m-b-6">

                    <input class="form-control" id="usuarioId" name="usuarioId" hidden type="text" placeholder="usuarioId" value="{{$usuario->usuarioId}}"/>

                        <div class="input-group mb-3">
                            <span class="input-group-text" style="width: 130px;" >Cedula</span>
                            <input class="form-control" id="cedula" name="cedula" type="text" placeholder="cedula" value="{{$usuario->cedula}}"/>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text" style="width: 130px;">Nombre</span>
                            <input class="form-control" id="nombre" name="nombre" type="text" placeholder="nombre" value="{{$usuario->nombre}}"/>

                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text" style="width: 130px;">Apellido</span>
                            <input class="form-control" id="apellido" name="apellido" type="text" placeholder="apellido" value="{{$usuario->apellido}}"/>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text" style="width: 130px;">Correo</span>
                            <input class="form-control" id="correo" name="correo" type="text" placeholder="correo" value="{{$usuario->correo}}"/>
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text" style="width: 130px;">Teléfono</span>
                            <input class="form-control" id="telefono" name="telefono" type="text" placeholder="telefono" value="{{$usuario->telefono}}"/>
                        </div>

                          <div class="input-group mb-3">
                            <label class="input-group-text" style="width: 130px;" for="inputGroupSelect01">Genero</label>
                            <select class="form-select" id="genero" name="genero">
                                <option value="" selected disabled>Seleccionar...</option>	
                                <option value="Masculino" {{ $usuario->genero === 'Masculino' ? 'selected' : '' }} >Masculino</option>										
                                <option value="Femenino" {{ $usuario->genero === 'Femenino' ? 'selected' : '' }} >Femenino</option>										
                                {{-- <option value="Discapacitado" {{ $solicitud->tipoConsumidor === 'Discapacitado' ? 'selected' : '' }} >Discapacitado</option>										
                                <option value="Jubilado" {{ $solicitud->tipoConsumidor === 'Jubilado' ? 'selected' : '' }} >Jubilado</option>	 --}}
                            </select>
                          </div>

                        {{-- <div class="input-group mb-3">
                            <label class="input-group-text" style="width: 130px;" for="departamento">Departamento</label>
                            <select class="form-select" id="departamento" name="departamento">
                                <option value="" selected disabled>Selecciona...</option>
                                @foreach ($departamento as $key => $value) 										
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>										
                                @endForeach
                            </select>
                          </div> --}}

                        <div class="input-group mb-3">
                            <label class="input-group-text" style="width: 150px;" for="inputGroupSelect01">Tipo de Sangre</label>
                            <select class="form-select" id="tipoSangre" name="tipoSangre">
                                <option value="" selected disabled>Seleccionar...</option>	
                                <option value="O+" {{ $usuario->tipoSangre === 'O+' ? 'selected' : '' }} >O+</option>										
                                <option value="O-" {{ $usuario->tipoSangre === 'O-' ? 'selected' : '' }} >O-</option>	
                                <option value="A+" {{ $usuario->tipoSangre === 'A+' ? 'selected' : '' }} >A+</option>	
                                <option value="A-" {{ $usuario->tipoSangre === 'A-' ? 'selected' : '' }} >A-</option>	
                                <option value="B+" {{ $usuario->tipoSangre === 'B+' ? 'selected' : '' }} >B+</option>	
                                <option value="B-" {{ $usuario->tipoSangre === 'B-' ? 'selected' : '' }} >B-</option>	
                                <option value="AB+" {{ $usuario->tipoSangre === 'AB+' ? 'selected' : '' }} >AB+</option>	
                            </select>
                          </div>

                          <div class="input-group mb-3">
                            <label class="input-group-text" style="width: 150px;" for="inputGroupSelect01">Tipo de Usuario</label>
                            <select class="form-select" id="tipoUsuario" name="tipoUsuario">
                                <option value="" selected disabled>Seleccionar...</option>	
                                <option value="Admin" {{ $usuario->tipoUsuario === 'Admin' ? 'selected' : '' }} >Admin</option>				
                                <option value="SuperAdmin" {{ $usuario->tipoUsuario === 'SuperAdmin' ? 'selected' : '' }} >SuperAdmin</option>				
                                <option value="Colaborador" {{ $usuario->tipoUsuario === 'Colaborador' ? 'selected' : '' }} >Colaborador</option>				
                                <option value="Recursos Humanos" {{ $usuario->tipoUsuario === 'Recursos Humanos' ? 'selected' : '' }} >Recursos Humanos"</option>				
                            </select>
                          </div>

                          <div class="input-group mb-3">
                            <label class="input-group-text" style="width: 150px;" for="inputGroupSelect01">Cubiculo</label>
                            <select class="form-select" id="cubiculo" name="cubiculo">
                                <option value="" selected disabled>Seleccionar...</option>	
                                <option value="cubico 1" {{ $usuario->cubico === 'cubico 1' ? 'selected' : '' }} >cubiculo 1</option>				
                                <option value="cubico 2" {{ $usuario->cubico === 'cubico 2' ? 'selected' : '' }} >cubiculo 2</option>				
                                <option value="cubico 3" {{ $usuario->cubico === 'cubico 3' ? 'selected' : '' }} >cubiculo 3</option>				
                                <option value="cubico 4" {{ $usuario->cubico === 'cubico 4' ? 'selected' : '' }} >cubiculo 4</option>				
                                <option value="cubico 5" {{ $usuario->cubico === 'cubico 5' ? 'selected' : '' }} >cubiculo 5</option>				
                                <option value="cubico 6" {{ $usuario->cubico === 'cubico 6' ? 'selected' : '' }} >cubiculo 6</option>				
                                <option value="cubico 7" {{ $usuario->cubico === 'cubico 7' ? 'selected' : '' }} >cubiculo 7</option>				
                            </select>
                          </div>

                          <div class="input-group mb-3">
                            <span class="input-group-text" style="width: 130px;">Contraseña</span>
                            <input class="form-control" id="contrasena" name="contrasena" type="password" placeholder="contrasena" value=""/>
                        </div>

                    <!-- 
                        <div class="form-floating mb-3">
                            <select class="form-select" id="departamento" name="departamento">
                                <option value="" selected disabled>Selecciona un departamento</option>
                                @foreach ($departamento as $key => $value) 										
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>										
                                @endForeach
                            </select>
                          
                        </div> -->
                       <!---
                        <div class="form-floating mb-3">
                            <select class="form-select" id="provincia" name="provincia">
                                <option value="" selected disabled>Selecciona un provincia</option>
                                @foreach ($provincia as $key => $value) 										
                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>										
                                @endForeach
                            </select>
                        </div>
                        
                        <div class="form-floating mb-3">
                            <div id='DivResultado_distrito'>
                            <select class="form-select" id="distrito" name="distrito">
                                <option value="" selected disabled>Selecciona un distrito</option>
                            </select>
                        </div> 
                       
                        <div class="input-group mb-3">
                            <span class="input-group-text" style="width: 150px;">Inicio de Contrato</span>
                            <input type="text" class="form-control"id="fechaInicio" name="fechaInicio" aria-label="Cedula" aria-describedby="basic-addon1">
                        </div>

                        <div class="input-group mb-3">
                            <span class="input-group-text" style="width: 150px;">Fin de Contrato</span>
                            <input type="text" class="form-control" id="fechaFin" name="fechaFin" aria-label="Cedula" aria-describedby="basic-addon1">
                        </div> -->
                        
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
			<!-- FINAL TAB GENERALES -->

			<!--INICIO PERMISOS	-->
			
			<div class="tab-pane" id="permisos">
				<div class="table-responsive">
				
				<div class="col-sm-12">
						
							<div class="col-sm-3">

						<table class="table table-striped table-responsive" id="perfilesEditar">
							<thead>
								<tr>
									<th class="btn-success">Nombre</th>
									<th class="btn-success">C&oacute;digo</th>
									<th class="btn-success"></th>
								</tr>
							</thead>
							<tbody>
								
						
									<tr class="gradeX">
										<td>{{ $usuario->nombre }}</td>
										<td>{{ $usuario->codigo }}</td>
										<td>
	
												<input type="checkbox" name="{{ $usuario->codigo }}" attr-usuarioId='{{ $usuario->id }}'  attr-codigo='{{ $usuario->codigo }}' class="permiso">
											
										</td>
									</tr>
								
							
								
								
							</tbody>
						</table>
						</div>

						<div class="col-sm-3">

						<table class="table table-striped table-responsive" id="perfilesEditar">
							<thead>
								<tr>
									<th class="btn-primary">Nombre1</th>
									<th class="btn-primary">C&oacute;digo1</th>
									<th class="btn-primary">2</th>
								</tr>
							</thead>
							<tbody>
								
                                <tr class="gradeX">
                                    <td>{{ $usuario->nombre }}</td>
                                    <td>{{ $usuario->codigo }}</td>
                                    <td>

                                            <input type="checkbox" name="{{ $usuario->codigo }}" attr-usuarioId='{{ $usuario->id }}'  attr-codigo='{{ $usuario->codigo }}' class="permiso">
                                        
                                    </td>
                                </tr>
								
								
							</tbody>
						</table>
						</div>

						<div class="col-sm-3">

						<table class="table table-striped table-responsive" id="perfilesEditar">
							<thead>
								<tr>
									<th class="btn-warning">Nombre</th>
									<th class="btn-warning">C&oacute;digo</th>
									<th class="btn-warning"></th>
								</tr>
							</thead>
							<tbody>
								
								<tr class="gradeX">
                                    <td>{{ $usuario->nombre }}</td>
                                    <td>{{ $usuario->codigo }}</td>
                                    <td>

                                            <input type="checkbox" name="{{ $usuario->codigo }}" attr-usuarioId='{{ $usuario->id }}'  attr-codigo='{{ $usuario->codigo }}' class="permiso">
                                        
                                    </td>
                                </tr>
								
								
							</tbody>
						</table>
						</div>

						<div class="col-sm-3">

						<table class="table table-striped table-responsive" id="perfilesEditar">
							<thead>
								<tr>
									<th class="btn-danger">Nombre</th>
									<th class="btn-danger">C&oacute;digo</th>
									<th class="btn-danger"></th>
								</tr>
							</thead>
							<tbody>
								
								<tr class="gradeX">
                                    <td>{{ $usuario->nombre }}</td>
                                    <td>{{ $usuario->codigo }}</td>
                                    <td>

                                            <input type="checkbox" name="{{ $usuario->codigo }}" attr-usuarioId='{{ $usuario->id }}'  attr-codigo='{{ $usuario->codigo }}' class="permiso">
                                        
                                    </td>
                                </tr>
								
							</tbody>
						</table>
						</div>

						</div>
						
						<!-- ACTION BUTTONS -->
							<div class="col-sm-12 line-top m-t-20">
								<div class="form-inline m-t-20">
									<button id="editarPermisos" name="editarPermisos" type="button" class="btn btn-success m-b-5"> <i class="fa fa-check m-r-5"></i> <span>Actualizar</span>
									</button>
									<a href="{{ url()->current() }}/dist/usuario" class="btn btn-inverse m-b-5"> <i class="fa fa-remove m-r-5"></i> <span>Cancelar</span></a>                                                          
								</div>
								
							</div>
						<!-- end ACTION BUTTONS -->
			
				</div>
				
			</div>
			<!--FINAL PERMISOS	-->
			
		
		</div>
	
		
		
		
	</div>
</div>




    

@include('includes/messagebasicmodal')
@endsection



