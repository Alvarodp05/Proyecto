<?php
session_start();

// 1. Seguridad: Si no hay datos de edición en la sesión, redirigir a la planta.
if (!isset($_SESSION['edit_data'])) {
    header("Location:../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
    exit;
}
// --- ¡NUEVO! Determinamos qué estamos editando ---
$tipo = $_GET['tipo'] ?? 'param'; // 'param' por defecto
$es_param = ($tipo == 'param');
$titulo = $es_param ? "Parámetro" : "Ítem de Stock";
$seccion_sesion = $es_param ? 'parameters' : 'stock';

// 2. Comprobar si el formulario se ha enviado (a sí mismo)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 3. Recoger todos los datos del formulario
    $param_key = $_POST['param_key']; // El ID (ej: "nuevo_param_1")
    $param_label = $_POST['param_label'];
    
    // --- ¡LÓGICA DE UNIDADES MODIFICADA! ---
    $param_units = 'Uds'; // Valor por defecto para Stock
    if ($es_param) {
        // Solo recogemos las unidades del POST si es un Parámetro
        $param_units = $_POST['param_units'];
    }
    // --- FIN LÓGICA ---
    
    $param_c_low = $_POST['param_c_low'];
    $param_c_high = $_POST['param_c_high'];
    $param_p_low = $_POST['param_p_low'];
    $param_p_high = $_POST['param_p_high'];
	
    $rand_min = $_POST['rand_min'];
    $rand_max = $_POST['rand_max'];

    // 4. Construir el nuevo array de parámetro
    $nuevo_parametro = [
        'label' => $param_label,
        'units' => $param_units, // <-- Valor (Uds o $_POST)
        'alarm_c_low' => $param_c_low,
        'alarm_c_high' => $param_c_high,
        'alarm_p_low' => $param_p_low,
        'alarm_p_high' => $param_p_high,
        'rand_min' => $rand_min,
        'rand_max' => $rand_max,
    ];

    // 5. Añadir el nuevo parámetro al array de la sesión
    $_SESSION['edit_data'][$seccion_sesion][$_POST['param_key']] = $nuevo_parametro;

    // 6. Redirigir de vuelta al Paso 1
    header("Location: editar_paso1.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Parámetro</title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
</head>
<body>
    <div class="main-container" style="display:block; max-width: 800px;">
        
        <form action="editar_paso2_add.php?tipo=<?php echo $tipo; ?>" method="POST"> <div class="content-header">
                <h1>Añadir Nuevo <?php echo $titulo; ?></h1>
            </div>

            <div class="section">
                <div class="form-item-edit">
                    <label for="param_key">ID del Parámetro:</label>
                    <input type="text" id="param_key" name="param_key" placeholder="Debe ser único, sin espacios" required>
                </div>
                
                <div class="form-item-edit">
                    <label for="param_label">Nombre:</label>
                    <input type="text" id="param_label" name="param_label" placeholder="Nombre que ve el usuario" required>
                </div>
                
                <?php if ($es_param): ?>
                    <div class="form-item-edit">
                        <label for="param_units">Unidades:</label>
                        <input type="text" id="param_units" name="param_units" placeholder="[L/h]">
                    </div>
                <?php endif; ?>
                <hr style="margin: 20px 0;">
                <p><strong>Límites de Alarma:</strong></p>

                <div class="form-item-edit">
                    <label for="param_c_low">Alarma Correctiva (Roja) - Valor Inferior:</label>
                    <input type="number" id="param_c_low" name="param_c_low" value="0">
                </div>
                
                <div class="form-item-edit">
                    <label for="param_c_high">Alarma Correctiva (Roja) - Valor Superior:</label>
                    <input type="number" id="param_c_high" name="param_c_high" value="100">
                </div>
                
                <div class="form-item-edit">
                    <label for="param_p_low">Alarma Preventiva (Naranja) - Valor Inferior:</label>
                    <input type="number" id="param_p_low" name="param_p_low" value="10">
                </div>
                
                <div class="form-item-edit">
                    <label for="param_p_high">Alarma Preventiva (Naranja) - Valor Superior:</label>
                    <input type="number" id="param_p_high" name="param_p_high" value="90">
                </div>
				
				
				<hr style="margin: 20px 0;">
                <p><strong>Rango de Valor Aleatorio (para la simulación):</strong></p>

                <div class="form-item-edit">
                    <label for="rand_min">Valor Mínimo Aleatorio:</label>
                    <input type="number" id="rand_min" name="rand_min" value="0">
                </div>
                
                <div class="form-item-edit">
                    <label for="rand_max">Valor Máximo Aleatorio:</label>
                    <input type="number" id="rand_max" name="rand_max" value="1000">
                </div>
           
            </div>

            <div class="buttons-edit-wizard">
                <button type="submit" class="btn-aceptar">Añadir Parámetro</button>
                <a href="editar_paso1.php" class="btn-cancelar-link">Volver (Cancelar)</a>
            </div>

        </form>
    </div>
</body>
</html>