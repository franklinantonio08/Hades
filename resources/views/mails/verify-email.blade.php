<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Verifica tu correo - {{ $appName ?? config('app.name') }}</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <style>
    body {
      background-color: #f4f4f4;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 20px;
    }

    .container {
      max-width: 600px;
      margin: 0 auto;
      background: #ffffff;
      padding: 30px 25px;
      border-radius: 12px;
      box-shadow: 0 5px 20px rgba(0,0,0,0.06);
    }

    .logo {
      text-align: center;
      margin-bottom: 25px;
    }

    .logo img {
      width: 110px;
    }

    .banner img {
      width: 100%;
      max-width: 600px;
      border-radius: 10px;
    }

    h1 {
      font-size: 24px;
      color: #0d6efd;
      text-align: center;
      margin-top: 20px;
      margin-bottom: 16px;
    }

    p {
      color: #444;
      font-size: 15px;
      line-height: 1.6;
      margin: 0 0 16px;
      text-align: center;
    }

    .btn {
      display: inline-block;
      padding: 14px 28px;
      background-color: #0d6efd;
      color: #ffffff !important;
      text-decoration: none;
      border-radius: 6px;
      font-size: 16px;
      margin: 24px auto;
    }

    .link {
      font-size: 13px;
      color: #0d6efd;
      word-break: break-all;
      text-align: center;
    }

    .footer {
      font-size: 12px;
      color: #888;
      text-align: center;
      margin-top: 32px;
    }
  </style>
</head>
<body>
  <div class="banner">
    <img src="{{ asset('images/header.png') }}" alt="Banner">
  </div>

  <div class="container">
    <div class="logo">
      <img src="{{ asset('images/logo1.png') }}" alt="Logo">
    </div>

    <h1>Verifica tu correo electrónico</h1>

    <p>Hola {{ $user->name ?? 'usuario' }}, gracias por registrarte en <strong>{{ $appName ?? config('app.name') }}</strong>.</p>
    <p>Para activar tu cuenta, por favor confirma tu dirección de correo electrónico.</p>

    <div style="text-align:center;">
      <a href="{{ $verificationUrl }}" class="btn">Verificar correo</a>
    </div>

    <p>Si el botón no funciona, copia y pega este enlace en tu navegador:</p>
    <p class="link">{{ $verificationUrl }}</p>

    <div class="footer">
      <p>Si no creaste esta cuenta, puedes ignorar este mensaje.</p>
      <p>© {{ date('Y') }} {{ $appName ?? config('app.name') }}. Todos los derechos reservados.</p>
    </div>
  </div>
</body>
</html>
