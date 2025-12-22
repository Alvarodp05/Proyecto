<?php
// session_start();
// DEBE iniciar la sesión para poder acceder y MODIFICAR el array 
// $_SESSION['maquinas_borradas'].
session_start();

// if ($_SERVER["REQUEST_METHOD"] == "POST" && ...)
// Es una comprobación de seguridad.
// $_SERVER["REQUEST_METHOD"] == "POST": Se asegura de que se llegó aquí
// enviando un formulario (POST), y no escribiendo la URL en el navegador (GET).
// isset($_POST['machine_id']): Se asegura de que el formulario envió un ID de máquina.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['machine_id']) && isset($_POST['action'])) {

    // $action = $_POST['action'];
    // "Atrapa" el valor del <select> (que será "borrar").
    $action = $_POST['action'];
    
    // $machine_id = $_POST['machine_id'];
    // "Atrapa" el valor del campo <input type="hidden">.
    $machine_id = $_POST['machine_id'];

    if ($action == 'borrar') {
        
        // if (!isset($_SESSION['maquinas_borradas'])) { ... }
        // Comprobación de seguridad por si acaso la sesión se perdió.
        // Si no existe la lista, la crea vacía.
        if (!isset($_SESSION['maquinas_borradas'])) {
            $_SESSION['maquinas_borradas'] = [];
        }

        // if (!in_array($machine_id, $_SESSION['maquinas_borradas'])) { ... }
        // "Si el ID de esta máquina NO está YA en la lista de borradas..."
        // (Esto evita que el mismo ID se añada varias veces).
        if (!in_array($machine_id, $_SESSION['maquinas_borradas'])) {
            
            // $_SESSION['maquinas_borradas'][] = $machine_id;
            // Esta es la acción principal:
            // Añade el ID de la máquina al final del array 'maquinas_borradas' en la sesión.
            // Los '[]' son un atajo para "añadir a este array".
            $_SESSION['maquinas_borradas'][] = $machine_id;
        }
    }
}

// header("Location: plantaproduccion_empleado_1.php");
// Es una instrucción HTTP. Le dice al navegador del usuario:
// "Tu trabajo aquí ha terminado. Ve inmediatamente a esta otra página."
// Esto es lo que provoca la recarga de la página.
header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");

// exit;
// Detiene la ejecución de este script PHP inmediatamente.
// Es una buena práctica usar 'exit' después de una redirección 'header'
// para asegurar que no se ejecute ningún otro código.
exit;
?>