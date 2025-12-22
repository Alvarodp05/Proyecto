<?php
session_start(); /*Sirve para iniciar sesión o retomar la sesión si esta sigue activa*/
include_once("../../Capa_negocio/Usuario/Usuario_clases2.php"); /*Se añade el fichero de la clase de usuario*/

$usuarion = new usuario (); /*se crea la variable de usuario nuevo con la clase de usuario*/

/*Llamada a las dos funciones a partir de usuario nuevo*/
$jefes = $usuarion->obtenerJefes();
$empleados = $usuarion->obtenerEmpleados();
/*si la variable intentos fallidos no existe se crea y se le da el valor de 0*/
if (!isset($_SESSION['intentos_fallidos'])) {
    $_SESSION['intentos_fallidos'] = 0;
}
/*Lo mismo que con intentos fallidos pero para el timeout*/
if (!isset($_SESSION['tiempo_bloqueo'])) {
    $_SESSION['tiempo_bloqueo'] = 0;
}

$mensaje_error = "";  /*se crea la variable para almacenar el mensaje de error, que de momento está vacia*/
$esta_bloqueado = false; /*se crea la variable de si el usuario esta bloqueado que de momento es falsa*/
$tiempo_actual = time();  /*se crea la variable para medir el tiempo actual*/

/*Si el usuario todavía no ha cumplido el timeout*/
if ($tiempo_actual < $_SESSION['tiempo_bloqueo']) {
    $esta_bloqueado = true; /*el estado de la variable sobre el bloqueo ahora es verdadero*/
    $tiempo_restante = ceil(($_SESSION['tiempo_bloqueo'] - $tiempo_actual) / 60); /*El tiempo que le queda es la diferencia entre el tiempo que se le veta y el tiempo que ha pasado vetado, ceil es para redondear al entero superior*/
    $mensaje_error = "Demasiados intentos. Debes esperar $tiempo_restante minuto(s).";  /*Se cambia el mensaje de error por uno que le dice al usuario cuanto tiempo le queda para poder intentar de nuevo el acceso*/
} 

/*Si el formulario se envia con post se realiza todo, si se hace con get no para evitar que se puedan filtrar usernames y claves*/
elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    /*Si se ha acabado el timeout y se superarons los intentos se reinicia el contador de fallos y el del tiempo*/
    if ($tiempo_actual >= $_SESSION['tiempo_bloqueo'] && $_SESSION['intentos_fallidos'] >= 3) {
        $_SESSION['intentos_fallidos'] = 0;
        $_SESSION['tiempo_bloqueo'] = 0;
    }

    $usuario = $_POST['usuario'];  /*se recoge del form el usuario y se almacena en una variable*/
    $clave = $_POST['clave']; /*lo mismo para la clave*/

    if (!empty($usuario) && !empty($clave)) { /*Para evitar que se pueda enviar el formulario vacio*/

        /*si jefe valido es true es porque los datos coinciden*/
        if ($usuarion->esJefeValido($usuario, $clave, $jefes)) {
            $_SESSION['intentos_fallidos'] = 0; /*reinicio del contador*/
            $_SESSION['tiempo_bloqueo'] = 0;  /*reinicio del tiempo*/ 
            header("Location: ../Planta_produccion/plantaproduccion_jefe.php"); /*te dirige a la planta de prod del jefe*/
            exit; 

        /*lo mismo para el empleado*/
        } elseif ($usuarion->esEmpleadoValido($usuario, $clave, $empleados)) {
            // ÉXITO (EMPLEADO)
            $_SESSION['intentos_fallidos'] = 0; 
            $_SESSION['tiempo_bloqueo'] = 0;   
            header("Location: ../Planta_produccion/plantaproduccion_empleado.php"); 
            exit; 

        /*en otro caso, es decir , datos incorrectos*/
        } else {
            $_SESSION['intentos_fallidos']++; /*se añade un nuevo intento fallido*/
            
            if ($_SESSION['intentos_fallidos'] >= 3) { /*si se tienen 3 intentos fallidos o mas*/
                $_SESSION['tiempo_bloqueo'] = $tiempo_actual + (10 * 60); /*Se pone el nuevo tiempo de bloqueo*/
                $mensaje_error = "Usuario y/o contraseña incorrecta. Has sido bloqueado por 10 minutos.";  /*Se cambia el mensaje para decirle al usuario que le acaban de bloquear y el tiempo que le queda*/
                $esta_bloqueado = true; /*El estado es bloqueado*/
            } else { /*Si no tiene 3 intentos fallidos o +*/
                $intentos_restantes = 3 - $_SESSION['intentos_fallidos']; /*Los intentos que le quedan son 3 menos los que haya fallado*/
                $mensaje_error = "Usuario y/o contraseña incorrecta. (Intentos restantes: $intentos_restantes)"; /*Se avisa de que los datos son incorrectos y de que le quedan x intentos*/
            }
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
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_Proyecto.css"> <!--Para importar la libreria de estilos-->
    
    
</head>
<body>
<body>
    <div class="login-container"> <!--Se crea el recuadro con todo lo que tendrá dentro-->
        <div class="logo">
            <img src="../../Lib/Imagenes/Aceitunas.jpg" alt="Aceituna">
            <h1>A6T</h1> <!--Se define el texto que tendrá el estilo del encabezado 1 que se define en el css-->
        </div>

        <h2>Acceso a A6T</h2> <!--Lo mismo para el encabezado 2-->
		
		<!--En este if lo que se hace es que si el mensaje de error no está vacio se crea una división con un estilo y ahí se escribe dicho mensaje -->
        <?php if (!empty($mensaje_error)): ?>
            <div class="error-message">
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
                <?php echo $mensaje_error; ?>
            </div>
        <?php endif; ?>

        <form action="Acceso_proyecto_clases.php" method="post"><!--Se envian los datos a este mismo archivo mediante el metodo post--> 
			<label for="usuario">Usuario:</label> <!--Se crea el campo del formulario usuario que mostrara por pantalla usuario-->
            <input type="text" id="usuario" name="usuario" placeholder="Ingrese su usuario">  <!--Se vincula el input con el campo del formulario creado justo arriba atraves del id, tambien se especifica el tipo de dato, como se va a almacnar y que se le escribe al cliente en la casilla cuando está vacía-->

            <label for="clave">Clave:</label>
            <input type="password" id="clave" name="clave" placeholder="Ingrese su clave">
      
            <div class="buttons"> <!--Se crea la division de los dos botones el de aceptar envia los datos y el de cancelar resetea todo-->
                <button type="submit" class="btn-aceptar" <?php if ($esta_bloqueado) echo 'disabled'; ?>>Aceptar</button>  <!--Aqui se hace que si el usuario esta bloqueado no pueda enviar los datos para que sean procesados-->
                <button type="reset" class="btn-cancelar">Cancelar</button>
            </div>
        </form>
    </div>
</body>
</html>