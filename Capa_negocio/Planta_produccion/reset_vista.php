<?php
// session_start();
// Inicia la sesión para poder acceder a las variables.
session_start();

// if (isset($_SESSION['maquinas_borradas'])) { ... }
// "Si la lista de borrados existe..."
if (isset($_SESSION['maquinas_borradas'])) {
    
    // unset($_SESSION['maquinas_borradas']);
    // 'unset' es el comando para "destruir" o "eliminar" una variable.
    // Aquí, elimina por completo el array 'maquinas_borradas' de la memoria de la sesión.
    unset($_SESSION['maquinas_borradas']);
}

// header("Location: plantaproduccion_empleado_1.php");
// Redirige al usuario de vuelta a la página de la planta.
// Al recargarse, la página no encontrará 'maquinas_borradas' (porque la hemos destruido),
// la volverá a crear vacía, y todas las máquinas se mostrarán de nuevo.
header("Location:../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
exit;
?>