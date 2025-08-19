{{-- resources/views/mails/reset-password.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Restablecer contraseña - {{ $appName }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f4f4; margin:0; padding:20px;">
  <!-- Banner -->
  <div style="text-align:center;">
    <img src="{{ asset('images/header.png') }}" alt="Banner" style="width:100%; max-width:600px;">
  </div>

  <!-- Card -->
  <div style="max-width:600px; margin:16px auto; background:#ffffff; padding:20px; border-radius:8px; box-shadow:0 0 15px rgba(0,0,0,.08);">
    <!-- Logo -->
    <div style="text-align:center; margin-bottom:20px;">
      <img src="{{ asset('images/logo1.png') }}" alt="Logo" style="width:120px;">
    </div>

    <h1 style="color:#0d6efd; text-align:center; font-size:22px; margin: 0 0 14px;">Restablece tu contraseña</h1>

    <p style="color:#333; font-size:15px; line-height:1.6; margin: 0 0 12px;">
      Hola {{ $user->name ?? $user->email ?? 'usuario' }},<br><br>
      Hemos recibido una solicitud para <strong>restablecer tu contraseña</strong> en <strong>{{ $appName }}</strong>.
      Haz clic en el botón para continuar:
    </p>

    <!-- Botón -->
    <div style="text-align:center; margin: 20px 0;">
      <a href="{{ $resetUrl }}"
         style="background:#0d6efd; color:white; text-decoration:none; padding:12px 22px; border-radius:8px; display:inline-block; font-weight:600;">
        Restablecer contraseña
      </a>
    </div>

    <p style="color:#333; font-size:14px; line-height:1.6; margin: 0 0 12px;">
      <strong>Importante:</strong> este enlace caduca en <strong>{{ $expire }} minutos</strong>.
      Si tú no solicitaste este cambio, puedes ignorar este correo; tu contraseña no se modificará.
    </p>

    <!-- Enlace en texto plano -->
    <p style="color:#666; font-size:12px; line-height:1.6; margin-top:18px;">
      Si tienes problemas con el botón, copia y pega esta URL en tu navegador:<br>
      <a href="{{ $resetUrl }}" style="color:#0d6efd;">{{ $resetUrl }}</a>
    </p>

    <hr style="border:0; border-top:1px solid #eee; margin: 18px 0;">

    <p style="color:#666; font-size:12px; text-align:center; margin:0;">
      © {{ date('Y') }} {{ $appName }} — Todos los derechos reservados
    </p>
  </div>
</body>
</html>
