<?php
session_start(); //Se inicializa la sesión 

// Lógica de visualización (Bloqueo y Errores)
if (!isset($_SESSION['intentos_fallidos'])) { $_SESSION['intentos_fallidos'] = 0; } //Si no existe la variable intentos fallidos, se crea y se asigna el valor 0
if (!isset($_SESSION['tiempo_bloqueo'])) { $_SESSION['tiempo_bloqueo'] = 0; } //Lo mismo para el tiempo de bloqueo

$tiempo_actual = time(); //Se establece el tiempo actual, al no haberse definido antes se crea la variable y se le asigna el valor, si ya estuviera creada solo se le asigna el valor
$esta_bloqueado = false; //Se establece por defecto la variable de bloqueo como falsa
$mensaje_extra = ""; // Se establece por defecto el mensaje extra como un mensaje vacio

if ($tiempo_actual < $_SESSION['tiempo_bloqueo']) { //Si el tiempo actual es menor al timeout
    $esta_bloqueado = true; //Eso significa que el usuario sigue estando bloqueado, por lo que la variable de bloqueo pasa a ser verdadera
    $segundos_restantes = $_SESSION['tiempo_bloqueo'] - $tiempo_actual; //Se crea la variable seg_restantes y se define como el tiempo d bloqueo menos el tiempo de esta sesion
    $minutos = floor($segundos_restantes / 60); //Se pasa el tiempo restante a minutos, floor redondea al entero inferior
    $mensaje_extra = "Espere $minutos minutos."; //En este caso el mensaje es que el usuario debe esperar x minutos para volver a intentarlo
}

$error_msg = ""; // Se establece por defecto el mensaje d error como un mensaje vacio
if (isset($_GET['error'])) { //Si existe un parametro error
    if ($_GET['error'] == 'credenciales') { //Si el error es en las creedenciales
        $intentos = isset($_GET['intentos']) ? $_GET['intentos'] : (3 - $_SESSION['intentos_fallidos']); //Se actualizan los intentos restantes como 3, que son el limite que hemos establecidos, menos los intentos fallidos
        $error_msg = "Datos incorrectos. Quedan $intentos intentos."; //El mensaje de error es que los datos son incorrectos y que te quedan x intentos restantes
    } elseif ($_GET['error'] == 'bloqueado') { //Si el error es debido a que esta bloqueado pq no ha cumplido el timeout
        $error_msg = "Sistema bloqueado por seguridad."; //El mensaje error es que el sistema se ha bloqueado por seguridad, tambien aparece el mensaje de que quedan x minutos
        $esta_bloqueado = true;//Se establece el bloqueo como true
    } elseif ($_GET['error'] == 'vacio') { //Si el error es d vacio
        $error_msg = "Rellene todos los campos."; //El mensaje de error es que hay que rellenar todos los campos
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acceso al Proyecto</title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_Proyecto.css"><!--Para importar el archivo de los estilos-->
</head>
<body>

    <div class="login-container"> <!--Se crea el recuadro con todo lo que tendra dentro-->
        
        <div class="logo">
            <img src="../../Lib/Imagenes/Logo_aceitunas.jpg" alt="Logo">
            <h1>A6T</h1> <!--Texto que tendra estilo del encabezado1-->
            <h2>Bienvenido a A6T:</h2><!--Lo mismo pero para el encabezado2-->
        </div>

        <?php if ($error_msg): ?><!--Si hay un mensaje de error, se crea la division y se le aplica un estilo-->
            <div style="background:#f8d7da; color:#721c24; padding:10px; margin-bottom:15px; border-radius:5px;">
                <?php echo $error_msg; ?> <!--Se muestra el mensaje de error-->
                <?php if ($esta_bloqueado) echo "<br><small>$mensaje_extra</small>"; ?> <!--Si esta bloqueado por el timeout se muestra tb el mensaje de cuantos minutos le restan para poder volver a intentarlo-->
            </div>
        <?php endif; ?>

        <form action="../../Capa_negocio/Usuario/login_procesar.php" method="POST"> <!--Se mandan los datos a login procesar mediante el metodo post-->
            
            <label for="usuario">Usuario:</label> <!--Se crea el campo del formulario usuario que mostrara por pantalla usuario-->
            <input type="text" id="usuario" name="nombre" placeholder="Ingrese su usuario"><!--Se vincula el input con el campo del formulario creado justo arriba atraves del id, tambien se especifica el tipo de dato, como se va a almacnar y que se le escribe al cliente en la casilla cuando está vacía-->
                   <?php if($esta_bloqueado) echo 'disabled'; ?> <!--Hace que si el usuario esta bloqueado no pueda escribir en la casilla-->

            <label for="clave">Clave:</label>
            <input type="password" id="clave" name="clave" placeholder="Ingrese su clave"
                   <?php if($esta_bloqueado) echo 'disabled'; ?>>
      
            <div class="buttons"> <!--Se crea la division de los dos botones el de aceptar envia los datos y el de cancelar resetea todo-->
                <button type="submit" class="btn-aceptar" <?php if($esta_bloqueado) echo 'disabled style="opacity:0.5;"'; ?>>Aceptar</button><!--Aqui se hace que si el usuario esta bloqueado no pueda enviar los datos para que sean procesados-->
                <button type="reset" class="btn-cancelar">Cancelar</button>
            </div>
        </form>
    </div>

</body>
</html>