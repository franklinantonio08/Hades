@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show shadow-sm border-start border-4 border-danger" role="alert">
    
    <div class="d-flex align-items-start gap-3">
        <div class="fs-3 text-danger">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>

        <div class="flex-grow-1">
            <h6 class="fw-bold mb-1">Ocurrió un problema</h6>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>

        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
    </div>

</div>
@endif
