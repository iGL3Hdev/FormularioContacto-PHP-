<?php

    //Configuracion
    $destinatario = "igl3hdev@gmail.com";
    $asunto_prefijo = "[Formulario Web]";

    //Variables para almacenar datos y errores

    $nombre = $email = $asunto = $mensaje = "";
    $errores = array();
    $enviado = false;

    //Proecesar el formulario si se envió

    if ($_SERVER["REQUEST_METHOD"] = "POST"){

        //Validar nombre

        if(empty($_POST["nombre"])){
            $errores["nombre"] = "El nombre es obligatorio";
        }else{
            $nombre = limpiar_entrada($_POST["nombre"]);
            if(strlen("nombre") < 2){
                $errores["nombre"] = "El nombre debe tener al menos 2 caracteres";
            }elseif(strlen($nombre) > 50){
                $errores["nombre"] = "El nombre no puede exceder de 50 caracteres";
            }elseif(!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/", $nombre)){
                $errores["nombre"] = "El nombre solo puede contener letras y espacios";
            }
        }

        //validar email

        if(empty($_POST["email"])){
            $errores["email"] = "El correo electronico es obligatorio";
        }else{
            $email = limpiar_entrada($_POST["email"]);
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errores["email"] = "El formato del correo electrónico no es válido";
            }
        }

        //validar asunto

        if(empty($_POST["asunto"])){
            $errores["mensaje"] = "El asunto es obligatorio";
        }else{
            $asunto = limpiar_entrada($_POST["asunto"]);
            if(strlen($asunto) < 5){
                $errores["asunto"] = "El asunto debe tener al menos 5 caracteres";
            }elseif(strlen($asunto > 100)){
                $errores["asunto"] = "El asunto no puede exceder de 100 caracteres";
            }
        }

        //Validar mensaje

        if(empty($_POST["mensjae"])){
            $errores["mensaje"] = "El mensaje es obligatorio";
        }else{
            $mensaje = limpiar_entrada($_POST["mensaje"]);
            if(strlen($mensaje) < 10){
                $errores["mensaje"] = "El mensaje debe tener al menos 10 caracteres";
            }elseif(strlen($mensaje) > 1000){
                $errores["mensaje"] = "El mensaje no puede exceder de 10000 caracteres";
            }
        }

        //Si no hay errores, enviar el email;

        if(empty($errores)){
            $enviado = enviar_email($destinatario, $nombre, $email, $asunto, $mensaje, $asunto_prefijo);

            //limpiar variables si el envio fue exitoso
            
            if($enviado){
                $nombre = $email = $asunto = $mensaje = "";
            }
        }

    }
        // Funcion para limpiar datos de entrada

        function limpiar_entrada($dato){
            $dato = trim($dato);
            $dato = stripslashes($dato);
            $dato = htmlspecialchars($dato);
            return $dato;
        }

        // Función para enviar email

        function enviar_email($destinatario, $nombre, $email, $asunto, $mensaje, $prefijo){
            $asunto_completo = $prefijo . $asunto;
        

        // Crear el cuerpo del mensaje

        $cuerpo = "Nuevo mensaje recibido desde el formulario de contacto: \n\n";
        $cuerpo .= "Nombre: " . $nombre . "\n";
        $cuerpo .= "Email: " . $email . "\n";
        $cuerpo .= "Asunto: " . $asunto . "\n";
        $cuerpo .= "Mensaje: \n " . $mensaje . "\n\n";
        $cuerpo .= "---\n";
        $cuerpo .= "Enviado desde: " . $_SERVER['HTTP_HOST'] . "\n";
        $cuerpo .= "IP del remitente: " . $_SERVER['REMOTE_ADDR'] . "\n";
        $cuerpo .= "Fecha: " . date('Y-m-d H:i:s');

        // Configurar header

        $headers = "From: " . $email . "\r\n";
        $headers .= "Reply-To: " . $email . "\r\n";
        $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        return mail($destinatario, $asunto_completo, $cuerpo, $headers);

        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de contacto</title>
    <style>

        *{
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body{
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        



    </style>
</head>
<body>

    <div class="container">
        <h1>Formulario de contacto</h1>

        <?php if($enviado): ?>
            <div class="success">
                 ✅ ¡Mensaje enviado correctamente! Te responderemos pronto.
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($errores)): ?>
            <div class="error-form">
                ❌ Por favor, corrige los errores indicados abajo.
            </div>
        <?php endif; ?>
    </div>
    
    <div class="form-info">
        📧 Completa el formulario y nos pondremos en contacto contigo lo antes posible.
    </div>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
                <label for="nombre">Nombre completo *</label>
                <input type="text" 
                       id="nombre" 
                       name="nombre" 
                       value="<?php echo htmlspecialchars($nombre); ?>"
                       placeholder="Ingresa tu nombre completo">
                <?php if (isset($errores["nombre"])): ?>
                    <div class="error"><?php echo $errores["nombre"]; ?></div>
                <?php endif; ?>
            </div>

    </form>
    
</body>
</html>