{{-- resources/views/layouts/payment.blade.php --}}
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Procesamiento de Pagos - {{ config('app.name', 'Laravel') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .payment-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 800px;
            margin: 20px;
        }
        .payment-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 20px;
            border-radius: 15px 15px 0 0;
            text-align: center;
        }
        .payment-body {
            padding: 30px;
        }
        #creditcard-container {
            min-height: 300px;
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            background: #f8f9fa;
        }
        .alert {
            border-radius: 10px;
            margin: 15px 0;
        }
        .btn-payment {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-payment:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
    </style>
    
    @yield('styles')
</head>
<body>

<div class="payment-container">
    <div class="payment-header">
        <h2 class="mb-0">@yield('title', 'Procesamiento de Pagos')</h2>
        <p class="mb-0 opacity-75">Sistema seguro de tokenización de tarjetas</p>
    </div>
    
    <div class="payment-body">
        @yield('content')
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

@yield('scripts')
</body>
</html>