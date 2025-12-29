@extends('layouts.admin')

@section('content')
<div class="container py-5">
    <div class="text-center">
        <h1 class="display-4 text-danger"><i class="bi bi-shield-lock-fill"></i> Acceso Denegado</h1>
        <p class="lead text-muted">{{ $mensaje }}</p>
        <a href="{{ url()->previous() }}" class="btn btn-outline-primary mt-3">
            <i class="bi bi-arrow-left"></i> Volver atrás
        </a>
    </div>
</div>
@endsection
