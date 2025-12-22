<?php
// --- 1. INICIAR LA SESIÓN ---
session_start();

// --- 2. OBTENER USUARIOS DESDE LA CLASE ---
//Incluimos el archivo que define la clase 'usuario'
include_once("../../Capa_negocio/Usuario/Usuario2.php");

// Obtenemos los arrays de objetos usando los métodos estáticos de la clase
$jefes = usuario::obtenerJefes();
$empleados = usuario::obtenerEmpleados();


// --- 3. INICIALIZAR VARIABLES DE SESIÓN Y DE ESTADO ---
if (!isset($_SESSION['intentos_fallidos'])) {
    $_SESSION['intentos_fallidos'] = 0;
}
if (!isset($_SESSION['tiempo_bloqueo'])) {
    $_SESSION['tiempo_bloqueo'] = 0;
}

$mensaje_error = ""; 
$esta_bloqueado = false; 
$tiempo_actual = time(); 

// --- 4. LÓGICA DE BLOQUEO ---
if ($tiempo_actual < $_SESSION['tiempo_bloqueo']) {
    $esta_bloqueado = true;
    $tiempo_restante = ceil(($_SESSION['tiempo_bloqueo'] - $tiempo_actual) / 60);
    $mensaje_error = "Demasiados intentos. Debes esperar $tiempo_restante minuto(s).";
} 

// --- 5. LÓGICA DE LOGIN ---
elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $usuario = $_POST['usuario'];
    $clave = $_POST['clave'];

    // Comprobar si es un JEFE
    // Ahora la lógica de comprobación se mueve a la clase 'usuario'
    if (usuario::esJefeValido($usuario, $clave, $jefes)) {
        // ÉXITO (JEFE)
        // La lógica de sesión y redirección se mantiene aquí
        $_SESSION['intentos_fallidos'] = 0; 
        $_SESSION['tiempo_bloqueo'] = 0;   
        header("Location: ../Planta_produccion/plantaproduccion_jefe.php"); 
        exit; 

    // Comprobar si es un EMPLEADO
    // Igualmente, la comprobación se mueve a la clase
    } elseif (usuario::esEmpleadoValido($usuario, $clave, $empleados)) {
        // ÉXITO (EMPLEADO)
        $_SESSION['intentos_fallidos'] = 0; 
        $_SESSION['tiempo_bloqueo'] = 0;   
        header("Location: ../Planta_produccion/plantaproduccion_empleado.php"); 
        exit; 

    // FRACASO (Datos incorrectos)
    } else {
        // La lógica de fallo se mantiene aquí
        $_SESSION['intentos_fallidos']++; 
        
        if ($_SESSION['intentos_fallidos'] >= 3) {
            $_SESSION['tiempo_bloqueo'] = $tiempo_actual + (10 * 60);
            $mensaje_error = "Usuario y/o contraseña incorrecta. Has sido bloqueado por 10 minutos.";
            $esta_bloqueado = true;
        } else {
            $intentos_restantes = 3 - $_SESSION['intentos_fallidos'];
            $mensaje_error = "Usuario y/o contraseña incorrecta. (Intentos restantes: $intentos_restantes)";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso como Jefe</title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_Proyecto.css">
    
    <style>
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="../../Lib/Imágenes/Aceitunas.jpg" alt="Aceituna">
            <h1>A6T</h1>
        </div>

        <h2>Acceso a A6T</h2>

        <?php if (!empty($mensaje_error)): ?>
            <div class="error-message">
                <?php echo $mensaje_error; ?>
            </div>
        <?php endif; ?>

        <form action="Acceso_proyecto_3.php" method="post">
            <label for="usuario">Usuario:</label>
            <input type="text" id="usuario" name="usuario" placeholder="Ingrese su usuario"> 

            <label for="clave">Clave:</label>
            <input type="password" id="clave" name="clave" placeholder="Ingrese su clave">
      
            <div class="buttons">
                <button type="submit" class="btn-aceptar" <?php if ($esta_bloqueado) echo 'disabled'; ?>>Aceptar</button>
                <button type="reset" class="btn-cancelar">Cancelar</button>
            </div>
        </form>
    </div>
</body>
</html>