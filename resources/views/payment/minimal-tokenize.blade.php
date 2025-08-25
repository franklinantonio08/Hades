<!DOCTYPE html>
<html>
<head>
    <title>Tokenización</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h1>Formulario de Tokenización</h1>
    <div id="creditcard-container"></div>
    <div id="messages"></div>

    <script>
    $(document).ready(function() {
        console.log('Cargando widget...');
        const widgetUrl = 'https://apicomponentv2-test.merchantprocess.net/UIComponent/CreditCard';
        
        $.ajax({
            type: "GET",
            url: widgetUrl,
            data: { APIKey: "{{ $api_key }}", Culture: "es" },
            success: function(response) {
                $("#creditcard-container").html(response);
                $("#messages").text('Widget cargado');
            },
            error: function(xhr, status, error) {
                $("#messages").text('Error: ' + error);
            }
        });
    });
    </script>
</body>
</html>