<?php
require_once "../../Lib/BD/AccesoBD.php"; //Se conecta al AccesoBD

echo "<h2>Herramienta de Encriptación Manual</h2>"; //Encabezado de clase h2

$bd = new AccesoBD();

$sqlBusqueda = "SELECT nombre, clave FROM usuarios";//Se obtienen todos los usuarios
$bd->prepararConsulta($sqlBusqueda);//Se prepara la consulta
$bd->lanzarConsultaPreparada();//Se lanza la consulta
$listaUsuarios = $bd->registrosConsulta();

$contadorActualizados = 0;//Se crea y se establece a 0 el contador de claves actualizadas

if (count($listaUsuarios) > 0) { //Si hay mas de 0 usuarios
    echo "<ul>"; //Para empezar a escribir una lista con puntos
    
    foreach ($listaUsuarios as $usuario) { //Para analizar de uno en uno a los usuarios
        $nombre = $usuario->nombre; //Se asigna el nombre del usuario a la vble nombre
        $clave_actual = $usuario->clave; //Lo mismo para la clave

        // Si no empieza por $2y$, es que no está encriptada
        if (substr($clave_actual, 0, 4) !== '$2y$') {
            
            $nuevoHash = password_hash($clave_actual, PASSWORD_DEFAULT); //Se hashea la clave
            
            $sqlUpdate = "UPDATE usuarios SET clave = ? WHERE nombre = ?"; //Se actualiza en la BD
            $bd->prepararConsulta($sqlUpdate);
            $bd->lanzarConsultaPreparada([$nuevoHash, $nombre]);
            
            echo "<li><strong>$nombre</strong>: Encriptada ahora.</li>"; //Si no estaba encriptada se manda el mensaje
            $contadorActualizados++;//Y se aumenta en uno el contador de actualizados
        } else {
            echo "<li><strong>$nombre</strong>: Ya estaba segura.</li>";//Si no es distinto es pq estaba encriptada, se manda ese mensaje
        }
    }
    echo "</ul>";
} else {
    echo "<p>No hay usuarios.</p>"; //Si no se han contado ningun usuario, imprime este mensaje
}

if ($contadorActualizados > 0) { //Si se han actualizado alguna clave se pone el mensaje
    echo "<p style='color:green;'>¡Listo! Se actualizaron $contadorActualizados.</p>";
} else {
    echo "<p>Todo estaba correcto.</p>"; //Si no es pq ya estaba todo encriptado
}

$bd->desconectar(); //Se desconecta de la BD
?>