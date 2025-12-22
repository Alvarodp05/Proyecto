<?php
session_start();
include_once ("../../Lib/BD/AccesoBD.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (!isset($_POST['machine_id'])) {
        die("Error: No se recibió ID de máquina.");
    }
    $machine_id = $_POST['machine_id'];
    $redirect_url = $_POST['redirect_url'] ?? "../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php";

    $bd = new AccesoBD();
    
    // Recorremos todos los datos enviados
    foreach ($_POST as $key => $valor_nuevo) {
        
        // Ignoramos campos de control y los campos ocultos 'original_'
        if ($key == 'machine_id' || $key == 'redirect_url' || strpos($key, 'original_') === 0) {
            continue;
        }

        // --- LÓGICA DE COMPARACIÓN ---
        $valor_a_guardar = null; // Por defecto, asumimos que será aleatorio (NULL)

        // Buscamos si existe el valor original correspondiente
        if (isset($_POST['original_' . $key])) {
            $valor_original = $_POST['original_' . $key];

            // Si el valor NUEVO es DIFERENTE al ORIGINAL, significa que el jefe lo editó.
            if ($valor_nuevo != $valor_original) {
                $valor_a_guardar = $valor_nuevo; // Guardamos el valor fijo
            }
            // Si son iguales, $valor_a_guardar se queda en null (aleatorio)
        }
        // -----------------------------
        // Actualizamos Parametros
        // ¡CORREGIDO! Usamos $valor_a_guardar en lugar de $valor
        $bd->actualizarValor('parametros', $machine_id, $key, 'valor_actual', $valor_a_guardar);
        
        // Actualizamos Stock
        // ¡CORREGIDO! Usamos $valor_a_guardar en lugar de $valor
        $bd->actualizarValor('stock', $machine_id, $key, 'valor_actual', $valor_a_guardar);
    }

    // --- ¡LA MAGIA DEL SEMÁFORO! ---
    // Activamos esta bandera para que al cargar la página, NO se generen aleatorios
    $_SESSION['flag_acabo_de_guardar'] = true;
    // -------------------------------

    header("Location: " . $redirect_url);
    exit;

} else {
    echo "Acceso no permitido.";
}
?>