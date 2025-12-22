<?php
/* generar_hashes.php */
/* Asegúrate de que la ruta a AccesoBD sea correcta */
require_once "../../Lib/BD/AccesoBD.php"; 

echo "<h3>Migración a Base de Datos Segura</h3>";

// 1. Conexión usando tu clase
$bd = new AccesoBD();

// 2. Leemos las claves actuales
$sql = "SELECT nombre, clave FROM usuarios";
$bd->LanzarConsulta($sql);
$usuarios = $bd->RegistrosConsulta();

$cont = 0;

if ($bd->NumeroRegistros() > 0) {
    echo "<ul>";
    foreach ($usuarios as $u) {
        $nombre = $u->nombre;
        $clave_actual = $u->clave;

        // Si la clave ya empieza por $2y$, asumimos que ya es un hash y la saltamos
        if (substr($clave_actual, 0, 4) !== '$2y$') {
            // Generamos el hash seguro
            $hash_seguro = password_hash($clave_actual, PASSWORD_DEFAULT);
            
            // ACTUALIZAMOS DIRECTAMENTE LA BD
            $sqlUpdate = "UPDATE usuarios SET clave = '$hash_seguro' WHERE nombre = '$nombre'";
            $bd->LanzarConsulta($sqlUpdate);
            
            echo "<li>Usuario <strong>$nombre</strong> actualizado a hash seguro.</li>";
            $cont++;
        } else {
            echo "<li>Usuario <strong>$nombre</strong> ya tenía clave encriptada. Se omite.</li>";
        }
    }
    echo "</ul>";
} else {
    echo "No se encontraron usuarios.";
}

if ($cont > 0) {
    echo "<p style='color:green; font-weight:bold;'>¡Éxito! Se han encriptado $cont contraseñas.</p>";
} else {
    echo "<p>No hubo cambios necesarios.</p>";
}

$bd->desconectar();
?>