<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Nuevo Usuario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="{{ asset('../js/app.js') }}" defer></script>
   
   <script>
        var BASEURL = '{{ url()->current() }}';
        var token = '{{ csrf_token() }}';
    </script>

    <style>
       body {
            font-family: Arial, sans-serif;
            background-color: #f1f1f1;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            width: 100%;
            flex-wrap: wrap;
        }
        .column {
            flex: 0 0 30%;
            max-width: 30%;
            padding: 10px;
            box-sizing: border-box;
            background-color: #ffffff;
        }
        .second-column {
            flex: 0 0 70%;
            max-width: 70%;
            padding: 10px;
            box-sizing: border-box;
            background-color: #f1f1f1;
        }
        .info-box {
            background-color: #f1f1f1;
            padding: 10px;
            margin-bottom: 10px;
        }
        .selectable-box {
            border: 2px solid #000;
            border-radius: 5px;
            padding: 10px;
            margin: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .selectable-box:hover {
            background-color: #f0f8ff;
        }
        header, footer {
            background-color: #f1f1f1;
            padding: 10px;
            text-align: center;
        }
        .row {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
        }
        .btn-col {
            flex: 1;
            margin: 5px;
            box-sizing: border-box;
        }
        
        h2, .selectable-box strong {
            color: #000;
        }
        .icon {
            margin-right: 5px;
            padding: 8px;
            border-radius: 50%;
            background-color: #000;
            color: white;
        }
        .step-list {
            list-style-type: none;
            padding: 0;
        }
        .step-list li {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .step-number {
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            margin-right: 10px;
            font-weight: bold;
            border-radius: 50%;
            background-color: #f1f1f1;
            color: #999;
        }
        .step-list li:first-child .step-number {
            border: 2px solid #007BFF;
            color: #007BFF;
        }
        .step-list li .step-text {
            color: #007BFF;
        }
        .button-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .action-btn {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 10px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .action-btn:hover {
            background-color: #0056b3;
        }
        .btn-col form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            font-weight: bold;
        }
        input[type="text"], input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }    
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fff;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            border-radius: 10px;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    @include('includes.header')
    <div class="container">
        <div class="column">
            <h2><i class="fas fa-user-plus icon"></i> Registro nuevo usuario</h2>
            <div id="step-list-container">
                <ul class="step-list">
                    <li>
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <span class="step-text">Tipo de acceso</span>
                    </li>
                    <li>
                        <div class="step-number"><i class="fas fa-check"></i></div>
                        <span class="step-text">Prestador de servicio</span>
                    </li>
                    <li>
                        <div class="step-number">3</div>
                        <span class="step-text">Información personal</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="column second-column">
            <div class="info-box">
                <h3><i class="fas fa-info-circle icon"></i> Información personal</h3>
                <p>Ingresa un correo y crea una contraseña para tu nueva cuenta.<br>Los campos marcados con * son obligatorios.</p>
            </div>

            {{-- <form method="POST" action="{{ route('register') }}"> --}}
            <form method="POST" action="{{ route('register') }}">
               
                @csrf

            <div class="row">
                <div class="btn-col">
                    
                        <label for="name">Usuario *</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                        
                        <label for="email">Correo *</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                        
                        <label for="password">Contraseña *</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password">
                        
                        <label for="password_confirmation">Confirmar Contraseña *</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required>
                        
                        
                        <label>
                            <input type="checkbox" name="data_consent" onclick="openModal('modal1')"> Acepto el tratamiento de datos personales
                        </label>
                        <br>
                        <br>
                        <label>
                            <input type="checkbox" name="terms_consent" onclick="openModal('modal2')"> Acepto los términos y condiciones
                        </label>
                    
                    
                </div>
                <div class="btn-col">
                    <div class="info-box">
                        <label><i class="fas fa-info-circle icon"></i> Tu contraseña debe tener mínimo 8 caracteres.</label><br>
                        <label><i class="fas fa-info-circle icon"></i> Incluye al menos un número (0 - 9).</label><br>
                        <label><i class="fas fa-info-circle icon"></i> Incorpora al menos una letra minúscula (a - z).</label><br>
                        <label><i class="fas fa-info-circle icon"></i> Agrega al menos una letra mayúscula (A - Z).</label><br>
                    </div>
                </div>
            </div>
            <div class="button-container">
                <button class="action-btn" onclick="regresar()">Regresar</button>
                <button class="action-btn" id="submitForm" name="submitForm" type="submit" onclick="continuar()">Continuar</button>

            </div>

            </form>
        </div>
    </div>
    <div id="modal1" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal1')">&times;</span>
            <p>Información del Check Point 1</p>
            <button class="action-btn" onclick="aceptar('modal1', 'checkpoint1')">Aceptar</button>

            <button class="action-btn" onclick="cancelar('modal1')">Cancelar</button>
        </div>
    </div>
    <div id="modal2" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('modal2')">&times;</span>
            <p>Información del Check Point 2</p>
            <button class="action-btn" onclick="aceptar('modal2', 'checkpoint2')">Aceptar</button>
            <button class="action-btn" onclick="cancelar('modal2')">Cancelar</button>
        </div>
    </div>
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "block";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        function aceptar(modalId, checkpoint) {
            closeModal(modalId);
            console.log("Aceptado:", checkpoint);
        }

        function cancelar(modalId) {
            closeModal(modalId);
        }

        function regresar() {
            window.history.back();
        }

        function continuar() {
            document.querySelector('form').submit();
        }
    </script>
    @include('includes.footer')
</body>
</html>
