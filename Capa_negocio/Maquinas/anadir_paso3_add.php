<?php
session_start();

// 1. Seguridad: Si no hay datos en la sesión 'anadir_data', redirigir.
if (!isset($_SESSION['anadir_data'])) {
    header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
    exit;
}

// 2. Determinamos qué estamos editando (Parámetro o Stock)
$tipo = $_GET['tipo'] ?? 'param';
$es_param = ($tipo == 'param');
$titulo = $es_param ? "Parámetro" : "Ítem de Stock";
$seccion_sesion = $es_param ? 'parameters' : 'stock';

// 3. Comprobar si el formulario se ha enviado (a sí mismo)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 4. Recoger todos los datos del formulario
    $param_key = $_POST['param_key'];
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

    // 5. Construir el nuevo array
    $nuevo_item = [
        'label' => $param_label,
        'units' => $param_units, // <-- Valor (Uds o $_POST)
        'alarm_c_low' => $param_c_low,
        'alarm_c_high' => $param_c_high,
        'alarm_p_low' => $param_p_low,
        'alarm_p_high' => $param_p_high,
        'rand_min' => $rand_min,
        'rand_max' => $rand_max,
    ];

    // 6. Añadir el nuevo item a la sesión 'anadir_data'
    $_SESSION['anadir_data'][$seccion_sesion][$param_key] = $nuevo_item;

    // 7. Redirigir de vuelta al Paso 2 (la página principal de añadir)
    header("Location: anadir_paso2.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir <?php echo $titulo; ?></title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
</head>
<body>
    <div class="main-container" style="display:block; max-width: 800px;">
        
        <form action="anadir_paso3_add.php?tipo=<?php echo $tipo; ?>" method="POST"> 
            <div class="content-header">
                <h1>Añadir Nuevo <?php echo $titulo; ?></h1>
            </div>

            <div class="section">
                <div class="form-item-edit">
                    <label for="param_key">ID (ej: 'caudal_2'):</label>
                    <input type="text" id="param_key" name="param_key" placeholder="Debe ser único, sin espacios" required>
                </div>
                
                <div class="form-item-edit">
                    <label for="param_label">Nombre (ej: 'Caudal Secundario'):</label>
                    <input type="text" id="param_label" name="param_label" placeholder="Nombre que ve el usuario" required>
                </div>
                
                <?php if ($es_param): ?>
                    <div class="form-item-edit">
                        <label for="param_units">Unidades (ej: '[L/h]'):</label>
                        <input type="text" id="param_units" name="param_units" placeholder="[L/h]">
                    </div>
                <?php endif; ?>
                <hr style="margin: 20px 0;">
                <p><strong>Límites de Alarma:</strong></p>

                <div class="form-item-edit">
                    <label>Alarma Correctiva (Roja) - Inferior:</label>
                    <input type="number" name="param_c_low" value="0">
                </div>
                <div class="form-item-edit">
                    <label>Alarma Correctiva (Roja) - Superior:</label>
                    <input type="number" name="param_c_high" value="100">
                </div>
                <div class="form-item-edit">
                    <label>Alarma Preventiva (Naranja) - Inferior:</label>
                    <input type="number" name="param_p_low" value="10">
                </div>
                <div class="form-item-edit">
                    <label>Alarma Preventiva (Naranja) - Superior:</label>
                    <input type="number" name="param_p_high" value="90">
                </div>
				
				<hr style="margin: 20px 0;">
                <p><strong>Rango de Valor Aleatorio (para la simulación):</strong></p>

                <div class="form-item-edit">
                    <label>Valor Mínimo Aleatorio:</label>
                    <input type="number" name="rand_min" value="0">
                </div>
                <div class="form-item-edit">
                    <label>Valor Máximo Aleatorio:</label>
                    <input type="number" name="rand_max" value="1000">
                </div>
            </div> <div class="buttons-edit-wizard">
                <button type="submit" class="btn-aceptar">Añadir <?php echo $titulo; ?></button>
                <a href="anadir_paso2.php" class="btn-cancelar-link">Volver (Cancelar)</a>
            </div>

        </form>
    </div>
</body>
</html>