@extends('layouts.auth')

@section('scripts')

  @include('includes.messagebasicmodal')

  <script>
    // Base del sitio (NO la URL actual para evitar /register/buscaposiciones)
    const BASEURL = @json(url(''));
    const token   = @json(csrf_token());

    window.REDIRECT_HOME = @json(url('/'));

    const ENDPOINT_FILIACION = @json(route('registro.consultaFiliacion'));

  </script>

  {{-- ruta correcta del js público --}}
  {{-- <script src="{{ asset('js/auth/register.js') }}" type="text/javascript"></script> --}}
@endsection

@vite(['resources/css/app.css', 'resources/js/app.js'])

@section('title', 'Crear Cuenta')

@section('card')
  <img src="{{ asset('images/logo1.png') }}" alt="Logo institucional" class="brand-img">

  <h2 class="auth-title">Registro</h2>
  <div class="auth-subtitle">Selecciona tu perfil y completa tus datos</div>

  @if ($errors->any())
    <div class="alert alert-danger py-2">
      <div class="d-flex align-items-start">
        <i class="bi bi-exclamation-triangle me-2 mt-1"></i>
        <div>
          <strong>Revisa los campos:</strong>
          <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>
  @endif

  <form method="POST" action="{{ route('register') }}" id="registerForm" novalidate enctype="multipart/form-data">
    @csrf

    {{-- Tipo de usuario (responsive grid) --}}
    <div class="mb-3">
      <label class="form-label fw-semibold d-block">Soy:</label>
      @php $tipoOld = old('tipo_usuario', 'solicitante'); @endphp
      <div class="row g-2 user-type" role="radiogroup" aria-label="Tipo de usuario">
        <div class="col-12 col-sm-6">
          <input type="radio" class="btn-check" name="tipo_usuario" id="optSolicitante" value="solicitante" {{ $tipoOld==='solicitante'?'checked':'' }}>
          <label class="btn btn-outline-primary w-100" for="optSolicitante"><i class="bi bi-person me-1"></i> Solicitante</label>
        </div>
        <div class="col-12 col-sm-6">
          <input type="radio" class="btn-check" name="tipo_usuario" id="optAbogado" value="abogado" {{ $tipoOld==='abogado'?'checked':'' }}>
          <label class="btn btn-outline-primary w-100" for="optAbogado"><i class="bi bi-briefcase me-1"></i> Abogado</label>
        </div>
      </div>
      @error('tipo_usuario') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
    </div>

    {{-- Nombres y Apellidos --}}
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="primer_nombre" class="form-label fw-semibold">Primer nombre</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input id="primer_nombre" type="text" name="primer_nombre" value="{{ old('primer_nombre') }}"
                 class="form-control @error('primer_nombre') is-invalid @enderror"
                 required placeholder="Ej: Juan">
          @error('primer_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="col-12 col-md-6">
        <label for="segundo_nombre" class="form-label fw-semibold">Segundo nombre (opcional)</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input id="segundo_nombre" type="text" name="segundo_nombre" value="{{ old('segundo_nombre') }}"
                 class="form-control @error('segundo_nombre') is-invalid @enderror"
                 placeholder="Ej: Carlos">
          @error('segundo_nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="col-12 col-md-6">
        <label for="primer_apellido" class="form-label fw-semibold">Primer apellido</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
          <input id="primer_apellido" type="text" name="primer_apellido" value="{{ old('primer_apellido') }}"
                 class="form-control @error('primer_apellido') is-invalid @enderror"
                 required placeholder="Ej: Pérez">
          @error('primer_apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
      <div class="col-12 col-md-6">
        <label for="segundo_apellido" class="form-label fw-semibold">Segundo apellido (opcional)</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person-vcard"></i></span>
          <input id="segundo_apellido" type="text" name="segundo_apellido" value="{{ old('segundo_apellido') }}"
                 class="form-control @error('segundo_apellido') is-invalid @enderror"
                 placeholder="Ej: Gómez">
          @error('segundo_apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    {{-- Contacto --}}
    <div class="row g-3 mt-0 mt-md-1">
      <div class="col-12 col-md-6">
        <label for="email" class="form-label fw-semibold">Correo electrónico</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
          <input id="email" type="email" name="email" value="{{ old('email') }}"
                 class="form-control @error('email') is-invalid @enderror"
                 required autocomplete="email" placeholder="correo@dominio.com">
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>

      <div class="col-12 col-md-6">
        <label for="telefono" class="form-label fw-semibold">Teléfono (opcional)</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-telephone"></i></span>
          <input id="telefono" type="text" name="telefono" value="{{ old('telefono') }}"
                 class="form-control @error('telefono') is-invalid @enderror"
                 autocomplete="tel" placeholder="+507 6000-0000">
          @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
      </div>
    </div>

    {{-- Solicitante --}}
    <div id="sectionSolicitante" class="border rounded p-3 mt-3 mb-3">
      <h6 class="text-primary mb-3"><i class="bi bi-person-lines-fill me-1"></i> Datos de Solicitante</h6>
      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label for="documento_tipo" class="form-label fw-semibold">Tipo de documento</label>
          <select id="documento_tipo" name="documento_tipo" class="form-select">
            <option value="" disabled {{ old('documento_tipo') ? '' : 'selected' }}>Selecciona...</option>
            <option value="Ruex" {{ old('documento_tipo')==='Ruex' ? 'selected':'' }}>N° Filiación</option>
            {{-- <option value="Pasaporte" {{ old('documento_tipo')==='Pasaporte' ? 'selected':'' }}>Pasaporte</option> --}}
          </select>
          @error('documento_tipo') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 col-md-6">
          <label for="documento_numero" class="form-label fw-semibold">N.º de documento</label>
          <input id="documento_numero" type="text" name="documento_numero" value="{{ old('documento_numero') }}"
                 class="form-control @error('documento_numero') is-invalid @enderror"
                 placeholder="Ej: 701234">
          @error('documento_numero') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

         <div class="col-12 col-md-6">
          <label for="genero" class="form-label fw-semibold">Genero</label>
          <select id="genero" name="genero" class="form-select">
            <option value="" disabled {{ old('genero') ? '' : 'selected' }}>Selecciona...</option>
            <option value="M" {{ old('genero')==='M' ? 'selected':'' }}>Masculino</option>
            <option value="F" {{ old('genero')==='F' ? 'selected':'' }}>Femenino</option>
          </select>
          @error('genero') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        </div>

     <div class="col-12 col-md-6">
      <label for="fecha_nacimiento" class="form-label fw-semibold">Fecha de nacimiento</label>
      <input id="fecha_nacimiento" type="date" name="fecha_nacimiento"
             value="{{ old('fecha_nacimiento') }}"
             class="form-control @error('fecha_nacimiento') is-invalid @enderror">
      @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>


      </div>
    </div>

    {{-- Abogado --}}
    <div id="sectionAbogado" class="border rounded p-3 mt-3 mb-3">
      <h6 class="text-primary mb-3"><i class="bi bi-briefcase me-1"></i> Datos de Abogado</h6>
      <div class="row g-3">
        <div class="col-12 col-md-6">
          <label for="abogado_id" class="form-label fw-semibold">ID / Licencia profesional</label>
          <input id="abogado_id" type="text" name="abogado_id" value="{{ old('abogado_id') }}"
                 class="form-control @error('abogado_id') is-invalid @enderror"
                 placeholder="N.º de id profesional">
          @error('abogado_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="col-12 col-md-6">
          <label for="firma_estudio" class="form-label fw-semibold">Estudio jurídico (opcional)</label>
          <input id="firma_estudio" type="text" name="firma_estudio" value="{{ old('firma_estudio') }}"
                 class="form-control" placeholder="Nombre del estudio">
        </div>
        <div class="col-12">
          <label for="idoneidad" class="form-label fw-semibold">Idoneidad (PDF/JPG/PNG, máx. 5MB)</label>
          <input id="idoneidad" type="file" name="idoneidad"
                 class="form-control @error('idoneidad') is-invalid @enderror"
                 accept="application/pdf,image/jpeg,image/png" data-max="5242880">
          <div id="idoneidadFeedback" class="invalid-feedback">@error('idoneidad') {{ $message }} @enderror</div>
          <div class="form-text">Adjunta tu idoneidad emitida por el colegio u organismo competente.</div>
        </div>
      </div>
    </div>

    {{-- Passwords --}}
    <div class="row g-3">
      <div class="col-12 col-md-6">
        <label for="password" class="form-label fw-semibold">Contraseña</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
          <input id="password" type="password" name="password"
                 class="form-control @error('password') is-invalid @enderror"
                 required autocomplete="new-password" placeholder="Mínimo 8 caracteres">
          <button class="btn btn-outline-secondary" type="button" id="togglePwd">
            <i class="bi bi-eye-slash" id="toggleIcon"></i>
          </button>
          @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div id="capsHint" class="caps-hint"><i class="bi bi-arrow-up-square me-1"></i> Bloq Mayús activado</div>
      </div>
      <div class="col-12 col-md-6">
        <label for="password_confirmation" class="form-label fw-semibold">Confirmar contraseña</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-shield-check"></i></span>
          <input id="password_confirmation" type="password" name="password_confirmation"
                 class="form-control" required autocomplete="new-password" placeholder="Repite tu contraseña">
        </div>
      </div>
    </div>

    <button class="btn btn-primary w-100 mt-3" id="btnRegister">
      <span class="btn-label"><i class="bi bi-person-plus me-2"></i> Crear cuenta</span>
      <span class="btn-spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>

    <div class="text-center mt-2">
      <span class="text-muted">¿Ya tienes cuenta?</span>
      <a href="{{ route('login') }}">Inicia sesión</a>
    </div>
  </form>
@endsection


{{-- @section('modals')
  @include('includes.messagebasicmodal')
@endsection --}}