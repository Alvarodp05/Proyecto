<?php
session_start();

// 1. Determinamos qué estamos editando
$tipo = $_GET['tipo'] ?? 'param';
$es_param = ($tipo == 'param');
$seccion_sesion = $es_param ? 'parameters' : 'stock';
$titulo = $es_param ? "Parámetro" : "Ítem de Stock";

// 2. Seguridad
if (!isset($_SESSION['anadir_data'])) {
    header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
    exit;
}

// 3. LÓGICA DE GUARDADO (Si se envía el formulario)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $original_key = $_POST['original_param_key'];
    $new_key = $_POST['param_key'];
    
    // --- ¡LÓGICA DE UNIDADES MODIFICADA! ---
    $param_units = 'Uds'; // Valor por defecto para Stock
    if ($es_param) {
        $param_units = $_POST['param_units'];
    }
    // --- FIN LÓGICA ---
    
    $edited_parameter = [
        'label' => $_POST['param_label'],
        'units' => $param_units, // <-- Valor (Uds o $_POST)
        'alarm_c_low' => $_POST['param_c_low'],
        'alarm_c_high' => $_POST['param_c_high'],
        'alarm_p_low' => $_POST['param_p_low'],
        'alarm_p_high' => $_POST['param_p_high'],
        'rand_min' => $_POST['rand_min'],
        'rand_max' => $_POST['rand_max'],
    ];
    
    // Guardamos en la sesión 'anadir_data'
    unset($_SESSION['anadir_data'][$seccion_sesion][$original_key]);
    $_SESSION['anadir_data'][$seccion_sesion][$new_key] = $edited_parameter;
    
    header("Location: anadir_paso2.php");
    exit;
}

// 4. LÓGICA DE VISTA (Si se carga la página)
$key_para_editar = $_GET['key'] ?? null;
$lista_actual = $_SESSION['anadir_data'][$seccion_sesion] ?? [];

if ($key_para_editar && isset($lista_actual[$key_para_editar])) {
    $param_data = $lista_actual[$key_para_editar];
} else {
    // (lista_parametros se usará en la vista de lista)
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar <?php echo $titulo; ?></title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <style>
        .select-list { background: #fff; padding: 20px; border-radius: 8px; }
        .select-item { 
            display: block; margin-bottom: 10px; font-size: 1.1em;
            padding: 10px; background-color: #f9f9f9; border: 1px solid #eee;
            border-radius: 5px; text-decoration: none; color: #3f4b27;
            font-weight: bold;
        }
        .select-item:hover { background-color: #e9e9e9; }
    </style>
</head>
<body>
    <div class="main-container" style="display:block; max-width: 800px;">
        
        <?php if (isset($param_data)): ?>
            <form action="anadir_paso3_edit.php?tipo=<?php echo $tipo; ?>" method="POST">
                <div class="content-header">
                    <h1>Editando <?php echo $titulo; ?>: <?php echo htmlspecialchars($param_data['label']); ?></h1>
                </div>

                <input type="hidden" name="original_param_key" value="<?php echo htmlspecialchars($key_para_editar); ?>">
                
                <div class="section">
                    <div class="form-item-edit">
                        <label>ID del Parámetro:</label>
                        <input type="text" name="param_key" value="<?php echo htmlspecialchars($key_para_editar); ?>" required>
                    </div>
                    
                    <div class="form-item-edit">
                        <label>Nombre:</label>
                        <input type="text" name="param_label" value="<?php echo htmlspecialchars($param_data['label']); ?>" required>
                    </div>
                    
                    <?php if ($es_param): ?>
                        <div class="form-item-edit">
                            <label>Unidades:</label>
                            <input type="text" name="param_units" value="<?php echo htmlspecialchars($param_data['units']); ?>">
                        </div>
                    <?php endif; ?>
                    <hr style="margin: 20px 0;">
                    <p><strong>Límites de Alarma:</strong></p>

                    <div class="form-item-edit">
                        <label>Alarma Correctiva (Roja) - Inferior:</label>
                        <input type="number" name="param_c_low" value="<?php echo htmlspecialchars($param_data['alarm_c_low']); ?>">
                    </div>
                    <div class="form-item-edit">
                        <label>Alarma Correctiva (Roja) - Superior:</label>
                        <input type="number" name="param_c_high" value="<?php echo htmlspecialchars($param_data['alarm_c_high']); ?>">
                    </div>
                    <div class="form-item-edit">
                        <label>Alarma Preventiva (Naranja) - Inferior:</label>
                        <input type="number" name="param_p_low" value="<?php echo htmlspecialchars($param_data['alarm_p_low']); ?>">
                    </div>
                    <div class="form-item-edit">
                        <label>Alarma Preventiva (Naranja) - Superior:</label>
                        <input type="number" name="param_p_high" value="<?php echo htmlspecialchars($param_data['alarm_p_high']); ?>">
                    </div>
                    
                    <hr style="margin: 20px 0;">
                    <p><strong>Rango de Valor Aleatorio:</strong></p>

                    <div class="form-item-edit">
                        <label>Valor Mínimo Aleatorio:</label>
                        <input type="number" name="rand_min" value="<?php echo htmlspecialchars($param_data['rand_min']); ?>">
                    </div>
                    <div class="form-item-edit">
                        <label>Valor Máximo Aleatorio:</label>
                        <input type="number" name="rand_max" value="<?php echo htmlspecialchars($param_data['rand_max']); ?>">
                    </div>
                </div>

                <div class="buttons-edit-wizard">
                    <button type="submit" class="btn-aceptar">Guardar Cambios</button>
                    <a href="anadir_paso2.php" class="btn-cancelar-link">Volver (Cancelar)</a>
                </div>
            </form>

        <?php else: ?>
            <div class="content-header">
                <h1>Seleccionar <?php echo $titulo; ?> a Editar</h1>
            </div>

            <div class="section select-list">
                <?php if (empty($lista_actual)): ?>
                    <p>No hay items para editar.</p>
                <?php else: ?>
                    <p>Haz clic en el item que deseas editar:</p>
                    
                    <?php foreach ($lista_actual as $key => $config): ?>
                        <a href="anadir_paso3_edit.php?tipo=<?php echo $tipo; ?>&key=<?php echo htmlspecialchars($key); ?>" class="select-item">
                            <?php echo htmlspecialchars($config['label']); ?> (ID: <?php echo htmlspecialchars($key); ?>)
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="buttons-edit-wizard">
                <a href="anadir_paso2.php" class="btn-cancelar-link">Volver (Cancelar)</a>
            </div>

        <?php endif; ?>

    </div>
</body>
</html>