<?php
// Proyecto/Capa_negocio/Maquinas/editar_paso2_edit.php
require_once("../Usuario/clase_usuario.php");
session_start();

// 1. Verificar sesión
if (!isset($_SESSION['edit_data'])) {
    header("Location: editar_paso1.php");
    exit;
}

$tipo = $_GET['tipo'] ?? 'param'; // 'param' o 'stock'
$data = $_SESSION['edit_data'];
$lista = ($tipo == 'param') ? $data['parameters'] : $data['stock'];

// Si no hay datos, volver
if (empty($lista)) {
    $_SESSION['flash_error'] = "No hay elementos para editar.";
    header("Location: editar_paso1.php");
    exit;
}

// 2. LÓGICA DE PANTALLA INTERMEDIA
$id_seleccionado = isset($_POST['selector_id']) ? $_POST['selector_id'] : null;
$mostrar_formulario = false;
$item = null;

if ($id_seleccionado && isset($lista[$id_seleccionado])) {
    $mostrar_formulario = true;
    $item = $lista[$id_seleccionado];
    
    // Variables para los values
    $val_label = $item['label'] ?? '';
    $val_units = $item['units'] ?? '';
    $val_c_low = $item['alarm_c_low'] ?? '';
    $val_c_high = $item['alarm_c_high'] ?? '';
    $val_p_low = $item['alarm_p_low'] ?? '';
    $val_p_high = $item['alarm_p_high'] ?? '';
    $val_r_min = $item['rand_min'] ?? '';
    $val_r_max = $item['rand_max'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Componente</title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <style>
        /* Estilos copiados y adaptados de editar_paso2_del.php para la lista */
        .selection-list { 
            background: #fff; 
            padding: 20px; 
            border-radius: 8px; 
        }
        .selection-item-btn { 
            display: block; 
            width: 100%;
            text-align: left;
            margin-bottom: 10px; 
            font-size: 1.1em; 
            background: none;
            border: none;
            border-bottom: 1px solid #eee; 
            padding: 10px 5px;
            cursor: pointer;
            color: #333;
        }
        .selection-item-btn:hover {
            background-color: #f9f9f9;
            font-weight: bold;
            color: #3f4b27;
        }
        .selection-item-btn span {
            float: right; 
            color: #777; 
            font-size: 0.9em; 
            font-weight: normal;
        }
    </style>
</head>
<body>
    <div class="main-container" style="display:block; max-width: 800px;">

        <?php if (!$mostrar_formulario): ?>
            
            <form action="" method="POST">
                <div class="content-header">
                    <h1>Editar <?php echo ($tipo == 'param') ? 'Parámetro' : 'Stock'; ?> Existente</h1>
                </div>

                <div class="section selection-list">
                    <p>Haz clic en el elemento que deseas modificar:</p>
                    
                    <?php foreach ($lista as $key => $element): ?>
                        <button type="submit" name="selector_id" value="<?php echo $key; ?>" class="selection-item-btn">
                            <?php echo htmlspecialchars($element['label']); ?>
                            <?php if($tipo == 'param'): ?>
                                <span>(<?php echo htmlspecialchars($element['units'] ?? '-'); ?>)</span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="buttons-edit-wizard">
                    <a href="editar_paso1.php" class="btn-cancelar-link">Volver / Cancelar</a>
                </div>
            </form>

        <?php else: ?>

            <form action="editar_procesar.php" method="POST">
                <input type="hidden" name="form_type" value="edit_item">
                <input type="hidden" name="tipo_item" value="<?php echo $tipo; ?>">
                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($id_seleccionado); ?>">

                <div class="content-header">
                    <h1>Editando: <?php echo htmlspecialchars($val_label); ?></h1>
                </div>

                <div class="section">
                    
                    <div class="form-item-edit">
                        <label>Nombre (Etiqueta):</label>
                        <input type="text" name="label" value="<?php echo htmlspecialchars($val_label); ?>" required>
                    </div>

                    <?php if ($tipo == 'param'): ?>
                        <div class="form-item-edit">
                            <label>Unidades:</label>
                            <input type="text" name="units" value="<?php echo htmlspecialchars($val_units); ?>">
                        </div>

                        <hr style="margin: 20px 0;">
                        <p><strong>Límites de Alarma Correctiva (Crítica):</strong></p>

                        <div class="form-item-edit">
                            <label>Valor Mínimo:</label>
                            <input type="number" step="0.01" name="alarm_c_low" value="<?php echo $val_c_low; ?>">
                        </div>
                        <div class="form-item-edit">
                            <label>Valor Máximo:</label>
                            <input type="number" step="0.01" name="alarm_c_high" value="<?php echo $val_c_high; ?>">
                        </div>

                        <hr style="margin: 20px 0;">
                        <p><strong>Límites de Alarma Preventiva (Aviso):</strong></p>

                        <div class="form-item-edit">
                            <label>Valor Mínimo:</label>
                            <input type="number" step="0.01" name="alarm_p_low" value="<?php echo $val_p_low; ?>">
                        </div>
                        <div class="form-item-edit">
                            <label>Valor Máximo:</label>
                            <input type="number" step="0.01" name="alarm_p_high" value="<?php echo $val_p_high; ?>">
                        </div>

                        <hr style="margin: 20px 0;">
                        <p><strong>Rango de Simulación (Aleatorio):</strong></p>

                        <div class="form-item-edit">
                            <label>Valor Mínimo:</label>
                            <input type="number" step="0.01" name="rand_min" value="<?php echo $val_r_min; ?>">
                        </div>
                        <div class="form-item-edit">
                            <label>Valor Máximo:</label>
                            <input type="number" step="0.01" name="rand_max" value="<?php echo $val_r_max; ?>">
                        </div>

                    <?php else: // ES STOCK ?>
                        
                        <hr style="margin: 20px 0;">
                        <p><strong>Configuración de Alarma:</strong></p>

                        <div class="form-item-edit">
                            <label>Alarma Mínima (Cantidad Crítica):</label>
                            <input type="number" step="0.01" name="alarm_c_low" value="<?php echo $val_c_low; ?>">
                        </div>

                        <p><strong>Rango de Simulación (Aleatorio):</strong></p>

                        <div class="form-item-edit">
                            <label>Stock Mínimo:</label>
                            <input type="number" step="0.01" name="rand_min" value="<?php echo $val_r_min; ?>">
                        </div>
                        <div class="form-item-edit">
                            <label>Stock Máximo:</label>
                            <input type="number" step="0.01" name="rand_max" value="<?php echo $val_r_max; ?>">
                        </div>

                    <?php endif; ?>
                </div>

                <div class="buttons-edit-wizard">
                    <button type="submit" class="btn-aceptar">Guardar Cambios</button>
                    <a href="editar_paso2_edit.php?tipo=<?php echo $tipo; ?>" class="btn-cancelar-link">Volver Atrás</a>
                </div>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>