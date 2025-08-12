   

    @foreach ($cubiculo as $key => $value)
        @if ($value->posicion <= 7)
            <div class="input-group mb-3">
                <span class="btn btn-primary text-white btn-lg" style="width: 50%;">MÓDULO {{ $value->posicion }}</span>
                <input type="text" class="form-control form-control-lg" id="turno{{ $value->posicion }}" name="{{ $value->llamado }}" value="{{ $value->codigo }}" data-llamado="{{ $value->llamado }}">
           
            </div>
        @endif
    @endforeach