@section('scripts')

<script>
	var BASEURL = '{{ url()->current() }}';
	var token = '{{ csrf_token() }}';
</script>
		
<script type="text/javascript" src="{{ asset('../js/admin/RIDPuntocontrol/RIDPuntocontrol.js') }}"></script>
<script type="text/javascript" src="{{ asset('../js/comun/confirmacionModal.js') }}"></script>
<script type="text/javascript" src="{{ asset('../js/comun/messagebasicModal.js') }}"></script>

@stop

@extends('layouts.admin')

@section('content')				

	<div class="col-lg-12">

		<div class="card mb-4">

			<div class="card-body p-4">

				<div class="row">				
					<div class="col-sm-3">
						<div class="card-title fs-4 fw-semibold">Lista de Comunidades Receptoras / Estaciones Temporales</div>
					</div>

					<div class="col-sm-2">
						<div class="form-group">
							<div class="input-group">
								<a href="{{ url()->current() }}/nuevo" class="btn bg-primary fs-8 fw-semibold text-white m-b-5"> <i class="bi bi-file-earmark-plus"></i> <span>Crear Nuevo</span></a>
							</div>		
						</div>			
					</div>
				
					{{-- <div class="col-sm-2">
						<div class="form-group">
							<div class="input-group">
								<a href="#" type="button" id="reporteButton" name="reporteButton" class="btn bg-success fs-8 fw-semibold text-white m-b-5"> <i class="bi bi-download"></i> <span>Generar Reporte</span></a>
							</div>		
						</div>			
					</div> --}}

					<div class="col-sm-6">
						<div class="col-sm-6 m-b-10">
							<div class="input-group">
								<input type="text" id="search" name="search" class="form-control" placeholder="Buscar...">
								<span class="input-group-btn">
									<button type="button" id="searchButton" name="searchButton" class="btn btn-warning m-b-5">
										<svg class="icon me-2">
											<use xlink:href="{{ asset('../vendors/@coreui/icons/svg/free.svg#cil-search') }}"></use>
										</svg>
									</button>
								</span>
							</div>						
						</div>
					</div>				
				</div>

				<!-- ACTION BUTTONS -->
				<div class="row">

					@include('includes/errors')
					@include('includes/success')
					
				</div>	

				<!-- ACTION BUTTONS -->

				<div class="row">				
					<div class="table-responsive">					
						<table class="table table-bordered data-table" id="puntocontrol">
							<thead>
								<tr>
									<th class="bg-primary fs-8 fw-semibold text-white">#</th>
									<th class="bg-primary fs-8 fw-semibold text-white">Nombre</th>
									<th class="bg-primary fs-8 fw-semibold text-white">Código</th>
									<th class="bg-primary fs-8 fw-semibold text-white">Estatus</th>
									<th class="bg-primary fs-8 fw-semibold text-white">Acción<i class="fa fa-ellipsis-h"></i></th>
								</tr>
							</thead>
							<tbody>
								<tr class="gradeX">
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>

       		</div>

		</div>    

	</div>  

</div>

	@include('includes/confirmacionmodal')
	@include('includes/messagebasicmodal')
	
@endsection



