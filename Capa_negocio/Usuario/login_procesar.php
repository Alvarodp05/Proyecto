<?php
session_start(); //Se inicializa la sesion
require_once("clase_usuario.php"); //Se llama al archivo de clase de usuario

//Inicializar variables de sesión si no existen, igual que en el acceso
if (!isset($_SESSION['intentos_fallidos'])) { $_SESSION['intentos_fallidos'] = 0; }
if (!isset($_SESSION['tiempo_bloqueo'])) { $_SESSION['tiempo_bloqueo'] = 0; }

//Comprobar si está bloqueado actualmente, como en el accesp
$tiempo_actual = time();
if ($tiempo_actual < $_SESSION['tiempo_bloqueo']) { // Sigue bloqueado
    header("Location: ../../Capa_usuario/Acceso/Acceso_proyecto_clases.php?error=bloqueado");//Si estas bloqueado te manda a la pagina de inicio de sesion y se establece que el error es que estas bloqueado con las consecuencias, sale el mensaje de bloqueo y no te deja escribir
    exit();
}

$nombre = $_POST['nombre'];//Se recoge el nombre del input enviado desde el acceso
$clave = $_POST['clave'];//Lo mismo para la clave

if ($nombre && $clave) { //Si tanto nombre y clave han sido rellenados
    $usuario = new Usuario(); //Se crea una variable usuario de la clase usuario

    if ($usuario->login($nombre, $clave)) { //Si coinciden usuario y clave
        //LOGIN CORRECTO: Se resetean contadores
        $_SESSION['intentos_fallidos'] = 0;
        $_SESSION['tiempo_bloqueo'] = 0;
        
        //Se guarda el usuario que esta en activo como los datos que vienen del formulario
        $_SESSION['usuario_activo'] = $usuario;

        header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php"); //Se manda a la planta de produccion, es decir, accede a nuestro sistema
        exit();
    } else {
        //LOGIN FALLIDO
        $_SESSION['intentos_fallidos']++;//Se suma un nuevo intento fallido

        //Se comprueba si se llega al límite (3 intentos)
        if ($_SESSION['intentos_fallidos'] >= 3) { //Si se han superado
            $_SESSION['tiempo_bloqueo'] = time() + 600; //Se establecen los 10 minutos de bloqueo
            header("Location: ../../Capa_usuario/Acceso/Acceso_proyecto_clases.php?error=bloqueado");//Te bloquea, te manda a la pagina de inicio de sesion y se establece que el error es que estas bloqueado con las consecuencias, sale el mensaje de bloqueo y no te deja escribir
        } else {
            // Aún quedan intentos
            $intentos_restantes = 3 - $_SESSION['intentos_fallidos']; //Se actualizan los intentos restantes como 3, que son el limite que hemos establecidos, menos los intentos fallidos
            header("Location: ../../Capa_usuario/Acceso/Acceso_proyecto_clases.php?error=credenciales&intentos=$intentos_restantes");//Te manda a la pagina de inicio y te dice los intentos que te quedan restantes
        }
        exit();
    }
} else {
    header("Location: ../../Capa_usuario/Acceso/Acceso_proyecto_clases.php?error=vacio");//En caso de que nombre y/o clave esten vacios, te manda a la pag ppal pero esta vez se establece que el error es de vacio y te muestra el correspondiente mensaje
    exit();
}
?>