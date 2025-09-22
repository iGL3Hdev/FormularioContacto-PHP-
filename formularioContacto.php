<?php
// Configuración
$destinatario = "tu-email@ejemplo.com"; // Cambia por tu dirección de correo
$asunto_prefijo = "[Formulario Web] ";

// Variables para almacenar datos y errores
$nombre = $email = $asunto = $mensaje = "";
$errores = array();
$enviado = false;

// Procesar el formulario si se envió
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Validar nombre
    if (empty($_POST["nombre"])) {
        $errores["nombre"] = "El nombre es obligatorio";
    } else {
        $nombre = limpiar_entrada($_POST["nombre"]);
        if (strlen($nombre) < 2) {
            $errores["nombre"] = "El nombre debe tener al menos 2 caracteres";
        } elseif (strlen($nombre) > 50) {
            $errores["nombre"] = "El nombre no puede exceder 50 caracteres";
        } elseif (!preg_match("/^[a-zA-ZáéíóúÁÉÍÓÚñÑüÜ\s]+$/", $nombre)) {
            $errores["nombre"] = "El nombre solo puede contener letras y espacios";
        }
    }
    
    // Validar email
    if (empty($_POST["email"])) {
        $errores["email"] = "El correo electrónico es obligatorio";
    } else {
        $email = limpiar_entrada($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores["email"] = "El formato del correo electrónico no es válido";
        }
    }
    
    // Validar asunto
    if (empty($_POST["asunto"])) {
        $errores["asunto"] = "El asunto es obligatorio";
    } else {
        $asunto = limpiar_entrada($_POST["asunto"]);
        if (strlen($asunto) < 5) {
            $errores["asunto"] = "El asunto debe tener al menos 5 caracteres";
        } elseif (strlen($asunto) > 100) {
            $errores["asunto"] = "El asunto no puede exceder 100 caracteres";
        }
    }
    
    // Validar mensaje
    if (empty($_POST["mensaje"])) {
        $errores["mensaje"] = "El mensaje es obligatorio";
    } else {
        $mensaje = limpiar_entrada($_POST["mensaje"]);
        if (strlen($mensaje) < 10) {
            $errores["mensaje"] = "El mensaje debe tener al menos 10 caracteres";
        } elseif (strlen($mensaje) > 1000) {
            $errores["mensaje"] = "El mensaje no puede exceder 1000 caracteres";
        }
    }
    
    // Si no hay errores, enviar el email
    if (empty($errores)) {
        $enviado = enviar_email($destinatario, $nombre, $email, $asunto, $mensaje, $asunto_prefijo);
        
        // Limpiar variables si el envío fue exitoso
        if ($enviado) {
            $nombre = $email = $asunto = $mensaje = "";
        }
    }
}

// Función para limpiar datos de entrada
function limpiar_entrada($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}

// Función para enviar email
function enviar_email($destinatario, $nombre, $email, $asunto, $mensaje, $prefijo) {
    $asunto_completo = $prefijo . $asunto;
    
    // Crear el cuerpo del mensaje
    $cuerpo = "Nuevo mensaje recibido desde el formulario de contacto:\n\n";
    $cuerpo .= "Nombre: " . $nombre . "\n";
    $cuerpo .= "Email: " . $email . "\n";
    $cuerpo .= "Asunto: " . $asunto . "\n";
    $cuerpo .= "Mensaje:\n" . $mensaje . "\n\n";
    $cuerpo .= "---\n";
    $cuerpo .= "Enviado desde: " . $_SERVER['HTTP_HOST'] . "\n";
    $cuerpo .= "IP del remitente: " . $_SERVER['REMOTE_ADDR'] . "\n";
    $cuerpo .= "Fecha: " . date('Y-m-d H:i:s');
    
    // Configurar headers
    $headers = "From: " . $email . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    
    // Enviar el email
    return mail($destinatario, $asunto_completo, $cuerpo, $headers);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Contacto</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        textarea {
            height: 120px;
            resize: vertical;
        }
        
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .success {
            background-color: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }
        
        .error-form {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .btn {
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #0056b3;
        }
        
        .btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        
        .form-info {
            background-color: #e7f3ff;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Formulario de Contacto</h1>
        
        <?php if ($enviado): ?>
            <div class="success">
                ✅ ¡Mensaje enviado correctamente! Te responderemos pronto.
            </div>
        <?php elseif ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($errores)): ?>
            <div class="error-form">
                ❌ Por favor, corrige los errores indicados abajo.
            </div>
        <?php endif; ?>
        
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
            
            <div class="form-group">
                <label for="email">Correo electrónico *</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?php echo htmlspecialchars($email); ?>"
                       placeholder="ejemplo@correo.com">
                <?php if (isset($errores["email"])): ?>
                    <div class="error"><?php echo $errores["email"]; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="asunto">Asunto *</label>
                <input type="text" 
                       id="asunto" 
                       name="asunto" 
                       value="<?php echo htmlspecialchars($asunto); ?>"
                       placeholder="¿Cuál es el motivo de tu consulta?">
                <?php if (isset($errores["asunto"])): ?>
                    <div class="error"><?php echo $errores["asunto"]; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label for="mensaje">Mensaje *</label>
                <textarea id="mensaje" 
                          name="mensaje" 
                          placeholder="Escribe aquí tu mensaje..."><?php echo htmlspecialchars($mensaje); ?></textarea>
                <?php if (isset($errores["mensaje"])): ?>
                    <div class="error"><?php echo $errores["mensaje"]; ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <button type="submit" class="btn">Enviar Mensaje</button>
            </div>
        </form>
        
        <div style="margin-top: 20px; font-size: 14px; color: #666;">
            * Campos obligatorios
        </div>
    </div>
</body>
</html>