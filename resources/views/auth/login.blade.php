@extends('layouts.auth')

@section('title', 'Iniciar Sesión')

@section('card')
  <img src="{{ asset('images/logo1.png') }}" alt="Logo institucional" class="brand-img">

  <h2 class="auth-title">Iniciar Sesión</h2>
  <div class="auth-subtitle">Accede con tus credenciales</div>

  {{-- Estados / Validaciones --}}
  @if (session('status'))
    <div class="alert alert-success py-2" role="status">
      <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
    </div>
  @endif
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

  <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
    @csrf

    <div class="mb-3">
      <label for="email" class="form-label fw-semibold">Usuario</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-person"></i></span>
        <input id="email" type="text" name="email" value="{{ old('email') }}"
               class="form-control @error('email') is-invalid @enderror"
               required autocomplete="email" autofocus placeholder="tu.usuario">
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
    </div>

    <div class="mb-2">
      <label for="password" class="form-label fw-semibold">Contraseña</label>
      <div class="input-group">
        <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
        <input id="password" type="password" name="password"
               class="form-control @error('password') is-invalid @enderror"
               required autocomplete="current-password" placeholder="••••••••">
        <button class="btn btn-outline-secondary" type="button" id="togglePwd" tabindex="-1" aria-label="Mostrar u ocultar contraseña">
          <i class="bi bi-eye-slash" id="toggleIcon"></i>
        </button>
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>
      <div id="capsHint" class="caps-hint"><i class="bi bi-arrow-up-square me-1"></i> Bloq Mayús activado</div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3">
      <div class="form-check">
        <input type="checkbox" name="remember" id="remember_me" class="form-check-input">
        <label for="remember_me" class="form-check-label">Recordarme</label>
      </div>
      @if (Route::has('password.request'))
        <a class="link-primary small" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
      @endif
    </div>

    <button type="submit" class="btn btn-primary w-100" id="btnSubmit">
      <span class="btn-label"><i class="bi bi-box-arrow-in-right me-2"></i> Iniciar sesión</span>
      <span class="btn-spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>
  </form>

  <div class="small-link">
    @if (Route::has('register'))
      <span class="text-muted">¿No tienes cuenta?</span>
      <a href="{{ route('register') }}">Crear cuenta</a>
    @endif
  </div>
@endsection

@push('scripts')
<script>
  // Mostrar/ocultar contraseña
  (function(){
    const pwd = document.getElementById('password');
    const btn = document.getElementById('togglePwd');
    const icon = document.getElementById('toggleIcon');
    if (btn) btn.addEventListener('click', () => {
      const showing = pwd.getAttribute('type') === 'text';
      pwd.setAttribute('type', showing ? 'password' : 'text');
      icon.classList.toggle('bi-eye');
      icon.classList.toggle('bi-eye-slash');
      pwd.focus({preventScroll:true});
    });
  })();

  // Hint de Bloq Mayús
  (function(){
    const pwd = document.getElementById('password');
    const hint = document.getElementById('capsHint');
    function update(e){
      const caps = e.getModifierState && e.getModifierState('CapsLock');
      hint.style.display = caps ? 'block' : 'none';
    }
    ['keyup','keydown','focus'].forEach(ev => pwd.addEventListener(ev, update));
    pwd.addEventListener('blur', ()=> hint.style.display='none');
  })();

  // Spinner en submit + prevención doble click
  (function(){
    const form = document.getElementById('loginForm');
    const btn  = document.getElementById('btnSubmit');
    form.addEventListener('submit', () => {
      btn.disabled = true;
      btn.querySelector('.btn-label').classList.add('d-none');
      btn.querySelector('.btn-spinner').classList.remove('d-none');
    });
  })();
</script>
@endpush
