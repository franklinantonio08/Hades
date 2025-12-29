@extends('layouts.admin')

@section('content')
<div class="row">
    @include('includes.errors')
    @include('includes.success')
</div>

<div class="col-lg-12">
<div class="card mb-4">
<div class="container-fluid py-4">

<form method="POST" action="{{ url('/dist/usuarios/editar') }}">
@csrf
@method('PUT')

<div class="card shadow-sm border-0">

<div class="card-header bg-primary text-white text-center">
    <h5 class="mb-0">Edición de Información Personal</h5>
    <small>Datos Generales, Origen y Familia</small>
</div>

<div class="card-body">

{{-- ===================== IDENTIFICACIÓN ===================== --}}
<h5 class="form-label fw-bold text-primary bg-light p-2">Datos de Identificación</h5>
<div class="row g-3 mb-4">

<input type="hidden" name="id" value="{{ old('id', $Sujeto->id ?? '') }}">

<div class="col-md-4">
    <label class="form-label fw-bold text-primary">Número de filiación</label>
    <input class="form-control" name="numero_filiacion"
           value="{{ old('numero_filiacion', $Sujeto->numero_filiacion ?? '') }}">
</div>

<div class="col-md-4">
    <label class="form-label fw-bold text-primary">Primer nombre</label>
    <input class="form-control" name="primer_nombre"
           value="{{ old('primer_nombre', $Sujeto->primer_nombre ?? '') }}">
</div>

<div class="col-md-4">
    <label class="form-label fw-bold text-primary">Segundo nombre</label>
    <input class="form-control" name="segundo_nombre"
           value="{{ old('segundo_nombre', $Sujeto->segundo_nombre ?? '') }}">
</div>

<div class="col-md-4">
    <label class="form-label fw-bold text-primary">Primer apellido</label>
    <input class="form-control" name="primer_apellido"
           value="{{ old('primer_apellido', $Sujeto->primer_apellido ?? '') }}">
</div>

<div class="col-md-4">
    <label class="form-label fw-bold text-primary">Segundo apellido</label>
    <input class="form-control" name="segundo_apellido"
           value="{{ old('segundo_apellido', $Sujeto->segundo_apellido ?? '') }}">
</div>

<div class="col-md-4">
    <label class="form-label fw-bold text-primary">Apellido de casada</label>
    <input class="form-control" name="apellido_casada"
           value="{{ old('apellido_casada', $Sujeto->apellido_casada ?? '') }}">
</div>

<div class="col-md-4">
    <label class="form-label fw-bold text-primary">Fecha de nacimiento</label>
    <input type="date" class="form-control" name="fecha_nacimiento"
           value="{{ old('fecha_nacimiento', $Sujeto->fecha_nacimiento ?? '') }}">
</div>

<div class="col-md-4">
    <label class="form-label fw-bold text-primary">Edad</label>
    <input class="form-control" name="edad"
           value="{{ old('edad', $Sujeto->edad ?? '') }}">
</div>

</div>

{{-- ===================== ORIGEN ===================== --}}
<h5 class="form-label fw-bold text-primary bg-light p-2">Nacionalidad y Origen</h5>
<div class="row g-3 mb-4">

<div class="col-md-6">
    <label class="form-label fw-bold text-primary">País de nacimiento</label>
    <select class="form-select" name="pais_nacimiento_id">
        <option value="">Seleccione…</option>
        @foreach($pais as $p)
            <option value="{{ $p->id }}"
                {{ old('pais_nacimiento_id', $Sujeto->pais_nacimiento_id ?? '') == $p->id ? 'selected' : '' }}>
                {{ $p->pais }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-6">
    <label class="form-label fw-bold text-primary">Lugar de nacimiento</label>
    <input class="form-control" name="lugar_nacimiento"
           value="{{ old('lugar_nacimiento', $Sujeto->lugar_nacimiento ?? '') }}">
</div>

<div class="col-md-6">
    <label class="form-label fw-bold text-primary">Nacionalidad</label>
    <select class="form-select" name="nacionalidad_id">
        <option value="">Seleccione…</option>
        @foreach($pais as $p)
            <option value="{{ $p->id }}"
                {{ old('nacionalidad_id', $Sujeto->nacionalidad_id ?? '') == $p->id ? 'selected' : '' }}>
                {{ $p->nacionalidad ?? $p->pais }}
            </option>
        @endforeach
    </select>
</div>

</div>

{{-- ===================== CARACTERÍSTICAS ===================== --}}
<h5 class="form-label fw-bold text-primary bg-light p-2">Características Físicas</h5>
<div class="row g-3 mb-4">

<div class="col-md-4">
    <label class="form-label fw-bold text-primary">Color de ojos</label>
    <select class="form-select" name="color_ojos">
        <option value="">Seleccione…</option>
        @foreach($catalogos['colores_ojos'] as $color)
            <option value="{{ $color }}"
                {{ old('color_ojos', $Sujeto->color_ojos ?? '') == $color ? 'selected' : '' }}>
                {{ $color }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-4">
    <label class="form-label fw-bold text-primary">Color de piel</label>
    <select class="form-select" name="color_piel">
        <option value="">Seleccione…</option>
        @foreach($catalogos['colores_piel'] as $color)
            <option value="{{ $color }}"
                {{ old('color_piel', $Sujeto->color_piel ?? '') == $color ? 'selected' : '' }}>
                {{ $color }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-4">
    <label class="form-label fw-bold text-primary">Estatura (m)</label>
    <input type="number" step="0.01" class="form-control" name="estatura"
           value="{{ old('estatura', $Sujeto->estatura ?? '') }}">
</div>

</div>

{{-- ===================== SITUACIÓN PERSONAL ===================== --}}
<h5 class="form-label fw-bold text-primary bg-light p-2">Situación Personal</h5>
<div class="row g-3 mb-4">

<div class="col-md-6">
    <label class="form-label fw-bold text-primary">Estado civil</label>
    <select class="form-select" name="estado_civil">
        <option value="">Seleccione…</option>
        @foreach($catalogos['estados_civiles'] as $estado)
            <option value="{{ $estado }}"
                {{ old('estado_civil', $Sujeto->estado_civil ?? '') == $estado ? 'selected' : '' }}>
                {{ $estado }}
            </option>
        @endforeach
    </select>
</div>

<div class="col-md-6">
    <label class="form-label fw-bold text-primary">Ocupación</label>
    <input class="form-control" name="ocupacion"
           value="{{ old('ocupacion', $Sujeto->ocupacion ?? '') }}">
</div>

</div>

{{-- ===================== CONTACTO ===================== --}}
<h5 class="form-label fw-bold text-primary bg-light p-2">Información de Contacto</h5>
<div class="row g-3 mb-4">

<div class="col-md-6">
    <label class="form-label fw-bold text-primary">Teléfono</label>
    <input class="form-control" name="telefono"
           value="{{ old('telefono', $Sujeto->telefono ?? '') }}">
</div>

<div class="col-md-6">
    <label class="form-label fw-bold text-primary">Correo electrónico</label>
    <input type="email" class="form-control" name="correo"
           value="{{ old('correo', $Sujeto->email ?? '') }}">
</div>

</div>

{{-- ===================== DIRECCIÓN ORIGEN ===================== --}}
<h5 class="form-label fw-bold text-primary bg-light p-2">Dirección del País de Origen</h5>
<textarea class="form-control mb-4" name="direccion_origen" rows="3">
{{ old('direccion_origen', $Sujeto->direccion_origen ?? '') }}
</textarea>

{{-- ===================== FAMILIA ===================== --}}
<h5 class="form-label fw-bold text-primary bg-light p-2">Información Familiar</h5>

@foreach(['conyuge'=>'Cónyuge','padre'=>'Padre','madre'=>'Madre'] as $key=>$label)
<div class="card mb-3">
<div class="card-header bg-light fw-bold text-primary">{{ $label }}</div>
<div class="card-body row g-3">

@foreach(['primer_nombre'=>'Primer nombre','segundo_nombre'=>'Segundo nombre','primer_apellido'=>'Primer apellido','segundo_apellido'=>'Segundo apellido'] as $campo=>$texto)
<div class="col-md-3">
    <label class="form-label fw-bold text-primary">{{ $texto }}</label>
    <input class="form-control" name="{{ $key.'_'.$campo }}" value="{{ old($key.'_'.$campo) }}">
</div>
@endforeach

<div class="col-md-6">
    <label class="form-label fw-bold text-primary">Nacionalidad</label>
    <select class="form-select" name="{{ $key }}_nacionalidad_id">
        <option value="">Seleccione…</option>
        @foreach($pais as $p)
            <option value="{{ $p->id }}"
                {{ old($key.'_nacionalidad_id') == $p->id ? 'selected' : '' }}>
                {{ $p->nacionalidad ?? $p->pais }}
            </option>
        @endforeach
    </select>
</div>

</div>
</div>
@endforeach

{{-- ===================== COMENTARIOS ===================== --}}
<h5 class="form-label fw-bold text-primary bg-light p-2">Comentario</h5>
<textarea class="form-control" name="comentario" rows="3">{{ old('comentario') }}</textarea>

</div>

<div class="card-footer text-end">
    <button class="btn btn-primary">Guardar cambios</button>
    <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancelar</a>
</div>

</div>
</form>

</div>
</div>
</div>
@endsection
