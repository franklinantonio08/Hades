@extends('layouts.admin')

@section('content')

    <div class="container-fluid">
        <div class="row mb-4">
            @include('includes.errors')
            @include('includes.success')
        </div>

        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col">
                            <h4 class="card-title fw-semibold">Registro de Infractores</h4>
                        </div>
                    </div>

                    
                    
                    <!-- Formulario -->
                    <div class="table-responsive my-2">
                        <form id="nuevoregistro" name="nuevoregistro" method="POST" action="{{ url()->current('/nuevo') }}" enctype="multipart/form-data" autocomplete="off">
                            @csrf
                            <div class="col-lg-6">

                                <input type="hidden" id="consuladoId" name="consuladoId" value="{{$consulado->id}}" class="form-control text-right" placeholder="">

                                <div class="input-group mb-3">
                                    <span class="input-group-text" style="width: 180px;" >Descripcion</span>
                                    <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="" value="{{$consulado->descripcion}}">
                                </div>

                                <div class="form-floating mb-3">
                                    <textarea class="form-control" id="comentario" name="comentario" placeholder="Comentario" style="height: 10rem;"></textarea>
                                    <label for="comentario">Comentario</label>
                                </div>

                                <!-- Botones de acción -->
                                <div class="form-group text-end">
                                    <button id="submitForm" type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Guardar</button>
                                    <a href="{{ url()->previous() }}" class="btn btn-danger"><i class="fa fa-remove"></i> Cancelar</a>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Fin Formulario -->

                </div>
            </div>    
        </div>
    </div>

    @include('includes.confirmacionmodal')
    @include('includes.messagebasicmodal')
    @include('includes.loader')
    @include('includes.download')
@endsection

@section('scripts')

    <script>
        var BASEURL = '{{ url()->current() }}';
        var token = '{{ csrf_token() }}';
    </script>

    <!-- JS Específicos -->
    <script src="{{ asset('../js/dist/consulados/consulados.js') }}"></script>
    <script src="{{ asset('../js/comun/confirmacionModal.js') }}" type="text/javascript"></script>
    <script src="{{ asset('../js/comun/messagebasicModal.js') }}"></script>
    
    <!-- Plugins -->
    <script src="{{ asset('../plugins/moment/moment.js') }}" type="text/javascript"></script>
    <script src="{{ asset('../plugins/bootstrap-daterangepicker/daterangepicker.js') }}" type="text/javascript"></script>


@stop
