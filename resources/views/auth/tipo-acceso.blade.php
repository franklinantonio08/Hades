<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro Nuevo Usuario</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="{{ asset('../js/app.js') }}" defer></script>

    <script>
        var BASEURL = '{{ url()->current() }}';
        var token = '{{ csrf_token() }}';
    </script>
    
    <style>
        /* Estilos CSS */
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
        .selectable-box.selected {
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
                        <div class="step-number" style="border: 2px solid #007BFF; color: #007BFF;">1</div>
                        <span class="step-text" style="color: #007BFF;">Tipo de acceso</span>
                    </li>
                    <li>
                        <div class="step-number">2</div>
                        <span class="step-text">Prestador de servicio</span>
                    </li>
                    <li>
                        <div class="step-number">3</div>
                        <span class="step-text">Información personal</span>
                    </li>
                    <li>
                        <div class="step-number">4</div>
                        <span class="step-text">Preguntas de identidad</span>
                    </li>
                    <li>
                        <div class="step-number">5</div>
                        <span class="step-text">Contraseña</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="column second-column">
            <div class="info-box" style="border: none;">
                <h3><i class="fas fa-info-circle icon"></i> Tipo de acceso</h3>
                <p>Ten en cuenta que, según el acceso que selecciones para tu registro, tendrás unos permisos específicos:</p>
                <ul>
                    <li>Nivel medio de seguridad: registro con documento de identidad</li>
                    <li>Nivel bajo de seguridad: registro con correo electrónico</li>
                </ul>
            </div>
            <div class="row">
                <div class="btn-col">
                    <div class="selectable-box" onclick="mostrarPasos('documento', this)">
                        <strong><i class="fas fa-id-card icon"></i> Registro con documento de identidad</strong>
                        <p>Con un solo registro, tendrás mayor cobertura para realizar trámites y servicios de entidades del Estado colombiano.</p>
                    </div>
                </div>
                <div class="btn-col">
                    <div class="selectable-box" onclick="mostrarPasos('correo', this)">
                        <strong><i class="fas fa-envelope icon"></i> Registro con correo electrónico</strong>
                        <p>Ingresa a servicios que no necesiten de acceso a información privada y participa con tus comentarios y aportes.</p>
                    </div>
                </div>
            </div>

            <div class="button-container">
                <button class="action-btn" onclick="regresar()">Regresar</button>
                <button class="action-btn" onclick="continuar()">Continuar</button>
            </div>
        </div>
    </div>

    @include('includes.sidebar')
    @include('includes.footer')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.toggleMenu = function() {
                const sidebar = document.querySelector('.sidebar');
                sidebar.style.left = sidebar.style.left === '-250px' ? '0' : '-250px';
            }
    
            window.mostrarPasos = function(opcion, element) {
                var contenidoDinamico = document.getElementById('step-list-container');
    
                // Limpiar el contenido anterior
                contenidoDinamico.innerHTML = '';
    
                // Deseleccionar todos los elementos
                var selectableBoxes = document.querySelectorAll('.selectable-box');
                selectableBoxes.forEach(function(box) {
                    box.classList.remove('selected');
                });
    
                // Seleccionar el elemento clicado
                element.classList.add('selected');
    
                // Determinar qué pasos mostrar según la opción seleccionada
                switch (opcion) {
                    case 'documento':
                        contenidoDinamico.innerHTML = `
                            <ul class="step-list">
                                <li><div class="step-number">1</div><span class="step-text">Tipo de acceso</span></li>
                                <li><div class="step-number">2</div><span class="step-text">Prestador de servicio</span></li>
                                <li><div class="step-number">3</div><span class="step-text">Información personal</span></li>
                                <li><div class="step-number">4</div><span class="step-text">Preguntas de identidad</span></li>
                                <li><div class="step-number">5</div><span class="step-text">Contraseña</span></li>
                            </ul>
                        `;
                        break;
                    case 'correo':
                        contenidoDinamico.innerHTML = `
                            <ul class="step-list">
                                <li><div class="step-number">1</div><span class="step-text">Tipo de acceso</span></li>
                                <li><div class="step-number">2</div><span class="step-text">Prestador de servicio</span></li>
                                <li><div class="step-number">3</div><span class="step-text">Contraseña</span></li>
                            </ul>
                        `;
                        break;
                }
            }
    
            window.regresar = function() {
                alert("Función para regresar");
                // Aquí puedes agregar la lógica para regresar a la pantalla anterior.
            }
    
            window.continuar = function() {
                alert("Función para continuar");
                // Aquí puedes agregar la lógica para continuar al siguiente paso.
            }
        });
    </script>
</body>
</html>
