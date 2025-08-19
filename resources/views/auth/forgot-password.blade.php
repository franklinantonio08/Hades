{{-- resources/views/auth/forgot-password.blade.php --}}
@extends('layouts.auth')

@section('scripts')
  <script>
    // Variables generales (si necesitas baseurl o token en esta vista)
    const BASEURL = @json(url(''));
    const CSRF    = @json(csrf_token());

    // Si Laravel trae un status en sesión (link enviado), lo mostramos en el modal
    @if (session('status'))
      document.addEventListener('DOMContentLoaded', () => {
        showMessageModal({
          title: 'Correo enviado',
          message: @json(session('status')),
          variant: 'success',
          closeText: 'Entendido'
        });
      });
    @endif
  </script>
@endsection

@section('title', 'Recuperar contraseña')

@section('card')
  <img src="{{ asset('images/logo1.png') }}" alt="Logo institucional" class="brand-img">

  <h2 class="auth-title">¿Olvidaste tu contraseña?</h2>
  <div class="auth-subtitle">
    Ingresa tu correo y te enviaremos un enlace para restablecerla.
  </div>

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

  <form method="POST" action="{{ route('password.email') }}" id="forgotForm" novalidate>
    @csrf

    <div class="row g-3">
      <div class="col-12">
        <label for="email" class="form-label fw-semibold">Correo electrónico</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope-at"></i></span>
          <input id="email"
                 type="email"
                 name="email"
                 value="{{ old('email') }}"
                 class="form-control @error('email') is-invalid @enderror"
                 required
                 autocomplete="email"
                 placeholder="correo@dominio.com">
          @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
        <div class="form-text">
          Te enviaremos un enlace para que establezcas una nueva contraseña.
        </div>
      </div>
    </div>

    <button class="btn btn-primary w-100 mt-3" id="btnForgot">
      <span class="btn-label"><i class="bi bi-send-check me-2"></i> Enviar enlace de restablecimiento</span>
      <span class="btn-spinner spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
    </button>

    <div class="text-center mt-2">
      <a href="{{ route('login') }}" class="text-decoration-none">
        <i class="bi bi-box-arrow-in-right me-1"></i> Volver a iniciar sesión
      </a>
    </div>
  </form>

  {{-- Script mínimo para UX (deshabilitar botón y spinner) --}}
  @push('scripts')
  <script>
    (function () {
      const form = document.getElementById('forgotForm');
      const btn  = document.getElementById('btnForgot');
      if (!form || !btn) return;

      form.addEventListener('submit', function () {
        btn.disabled = true;
        btn.querySelector('.btn-label')?.classList.add('d-none');
        btn.querySelector('.btn-spinner')?.classList.remove('d-none');
      });
    })();
  </script>
  @endpush
@endsection
