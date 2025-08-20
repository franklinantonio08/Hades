{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.auth')

@section('title', 'Nueva contraseña')

@section('card')
  <img src="{{ asset('images/logo1.png') }}" alt="Logo institucional" class="brand-img">

  <h2 class="auth-title">Establecer nueva contraseña</h2>
  <div class="auth-subtitle">Crea una contraseña segura para tu cuenta.</div>

  {{-- Errores de validación (servidor) --}}
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

  <form method="POST" action="{{ route('password.update') }}" id="resetForm" novalidate>
    @csrf

    {{-- Token y email (el email va readonly para evitar inconsistencias) --}}
    <input type="hidden" name="token" value="{{ request()->route('token') }}">
    <div class="row g-3">
      <div class="col-12">
        <label for="email" class="form-label fw-semibold">Correo electrónico</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
          <input id="email"
                 type="email"
                 name="email"
                 value="{{ old('email', request('email')) }}"
                 class="form-control @error('email') is-invalid @enderror"
                 required
                 autocomplete="email"
                 placeholder="correo@dominio.com"
                 readonly>
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-text">Este es el correo al que se envió el enlace de restablecimiento.</div>
      </div>

      <div class="col-12 col-md-6">
        <label for="password" class="form-label fw-semibold">Nueva contraseña</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
          <input id="password"
                 type="password"
                 name="password"
                 class="form-control @error('password') is-invalid @enderror"
                 required
                 autocomplete="new-password"
                 placeholder="Mínimo 8 caracteres">
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
          <input id="password_confirmation"
                 type="password"
                 name="password_confirmation"
                 class="form-control"
                 required
                 autocomplete="new-password"
                 placeholder="Repite tu contraseña">
        </div>
      </div>
    </div>

    <button class="btn btn-primary w-100 mt-3" id="btnReset">
      <span class="btn-label"><i class="bi bi-check2-circle me-2"></i> Guardar nueva contraseña</span>
      <span class="btn-spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>

    <div class="text-center mt-2">
      <a href="{{ route('login') }}" class="text-decoration-none">
        <i class="bi bi-box-arrow-in-right me-1"></i> Volver a iniciar sesión
      </a>
    </div>
  </form>

  @push('scripts')
  <script>
    (function () {
      const form = document.getElementById('resetForm');
      const btn  = document.getElementById('btnReset');

      // Toggle de contraseña
      const pwd   = document.getElementById('password');
      const tBtn  = document.getElementById('togglePwd');
      const tIcon = document.getElementById('toggleIcon');
      const caps  = document.getElementById('capsHint');

      if (tBtn && pwd && tIcon) {
        tBtn.addEventListener('click', () => {
          const isText = pwd.getAttribute('type') === 'text';
          pwd.setAttribute('type', isText ? 'password' : 'text');
          tIcon.classList.toggle('bi-eye');
          tIcon.classList.toggle('bi-eye-slash');
          pwd.focus();
        });
      }

      // Hint de Bloq Mayús
      if (pwd && caps) {
        const handler = (e) => {
          const on = e.getModifierState && e.getModifierState('CapsLock');
          caps.style.display = on ? 'block' : 'none';
        };
        ['keydown','keyup','focus'].forEach(ev => {
          pwd.addEventListener(ev, (e) => handler(e));
        });
        pwd.addEventListener('blur', () => caps.style.display = 'none');
      }

      // Spinner + deshabilitar botón al enviar
      if (form && btn) {
        form.addEventListener('submit', function () {
          btn.disabled = true;
          btn.querySelector('.btn-label')?.classList.add('d-none');
          btn.querySelector('.btn-spinner')?.classList.remove('d-none');
        });
      }
    })();
  </script>
  @endpush
@endsection
