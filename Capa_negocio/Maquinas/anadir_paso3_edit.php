<?php
// Proyecto/Capa_negocio/Maquinas/anadir_paso3_edit.php
require_once("../Usuario/clase_usuario.php");
session_start();

// 1. Verificar sesión
if (!isset($_SESSION['add_data'])) {
    header("Location: anadir_paso1.php");
    exit;
}

$tipo = $_GET['tipo'] ?? 'param';
$data = $_SESSION['add_data'];
$lista = ($tipo == 'param') ? $data['parameters'] : $data['stock'];

// Si no hay datos, volver
if (empty($lista)) {
    $_SESSION['flash_error'] = "No hay elementos para editar.";
    header("Location: anadir_paso2.php");
    exit;
}

// 2. LÓGICA DE SELECCIÓN
$id_seleccionado = isset($_POST['selector_id']) ? $_POST['selector_id'] : null;
$mostrar_formulario = false;
$item = null;

if ($id_seleccionado && isset($lista[$id_seleccionado])) {
    $mostrar_formulario = true;
    $item = $lista[$id_seleccionado];
    
    // Preparar valores
    $val_label = $item['label'] ?? '';
    $val_units = $item['units'] ?? '';
    
    // Alarmas
    $val_c_low = $item['alarm_c_low'] ?? 0;
    $val_c_high = $item['alarm_c_high'] ?? 0;
    $val_p_low = $item['alarm_p_low'] ?? 0;
    $val_p_high = $item['alarm_p_high'] ?? 0;
    
    // Aleatorios
    $val_r_min = $item['rand_min'] ?? 0;
    $val_r_max = $item['rand_max'] ?? 100;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar <?php echo ucfirst($tipo); ?></title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <style>
        * { box-sizing: border-box; }
        body { background-color: #7e8a50; padding: 40px 20px; min-height: 100vh; }
        .main-container {
            background-color: #ffffff; padding: 40px; border-radius: 20px;
            max-width: 700px; margin: 0 auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .selection-list { background: #fff; padding: 10px; }
        .selection-item-btn { 
            display: block; width: 100%; text-align: left; margin-bottom: 8px; padding: 15px; 
            cursor: pointer; border: 1px solid #eee; background: #f9f9f9; 
            font-size: 1.1em; color: #333; border-radius: 5px; transition: all 0.2s;
        }
        .selection-item-btn:hover { background-color: #e2e6ea; border-color: #adb5bd; font-weight: bold; color: #3f4b27; transform: translateX(5px); }

        .fila-doble { display: flex; gap: 20px; margin-bottom: 15px; }
        .columna-mitad { flex: 1; }
        .form-item-edit input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        h3 { border-bottom: 2px solid #eee; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; color: #3f4b27; font-size: 1.1em; }
    </style>
</head>
<body>
    <div class="main-container">

        <?php if (!$mostrar_formulario): ?>
            
            <div class="content-header">
                <h1>Seleccionar <?php echo ($tipo == 'param') ? 'Parámetro' : 'Stock'; ?></h1>
            </div>

            <form action="" method="POST">
                <div class="section selection-list">
                    <p style="margin-bottom:15px; color:#666;">Elige el elemento a modificar:</p>
                    
                    <?php foreach ($lista as $key => $element): ?>
                        <button type="submit" name="selector_id" value="<?php echo $key; ?>" class="selection-item-btn">
                            <?php echo htmlspecialchars($element['label']); ?>
                            <?php if($tipo == 'param'): ?>
                                <span style="float:right; color:#777; font-size:0.9em; font-weight:normal;">
                                    (<?php echo htmlspecialchars($element['units'] ?? '-'); ?>)
                                </span>
                            <?php endif; ?>
                        </button>
                    <?php endforeach; ?>
                </div>

                <div class="buttons-edit-wizard" style="margin-top:20px;">
                    <a href="anadir_paso2.php" class="btn-cancelar-link">Volver Atrás</a>
                </div>
            </form>

        <?php else: ?>

            <div class="content-header">
                <h1>Editando: <?php echo htmlspecialchars($val_label); ?></h1>
            </div>

            <form action="anadir_procesar.php" method="POST">
                <input type="hidden" name="form_type" value="edit_item">
                <input type="hidden" name="tipo_item" value="<?php echo $tipo; ?>">
                <input type="hidden" name="item_id" value="<?php echo htmlspecialchars($id_seleccionado); ?>">

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
                    <?php endif; ?>

                    <hr style="margin: 20px 0;">
                    <h3>Alarmas Críticas (Rojo)</h3>
                    <div class="fila-doble">
                        <div class="columna-mitad">
                            <label>Mínimo:</label>
                            <input type="number" step="0.01" name="alarm_c_low" value="<?php echo $val_c_low; ?>">
                        </div>
                        <div class="columna-mitad">
                            <label>Máximo:</label>
                            <input type="number" step="0.01" name="alarm_c_high" value="<?php echo $val_c_high; ?>">
                        </div>
                    </div>

                    <h3>Alarmas Preventivas (Naranja)</h3>
                    <div class="fila-doble">
                        <div class="columna-mitad">
                            <label>Mínimo:</label>
                            <input type="number" step="0.01" name="alarm_p_low" value="<?php echo $val_p_low; ?>">
                        </div>
                        <div class="columna-mitad">
                            <label>Máximo:</label>
                            <input type="number" step="0.01" name="alarm_p_high" value="<?php echo $val_p_high; ?>">
                        </div>
                    </div>

                    <hr style="margin: 20px 0;">
                    <h3>Simulación</h3>
                    <div class="fila-doble">
                        <div class="columna-mitad">
                            <label>Generar Mínimo:</label>
                            <input type="number" step="0.01" name="rand_min" value="<?php echo $val_r_min; ?>">
                        </div>
                        <div class="columna-mitad">
                            <label>Generar Máximo:</label>
                            <input type="number" step="0.01" name="rand_max" value="<?php echo $val_r_max; ?>">
                        </div>
                    </div>

                </div>

                <div class="buttons-edit-wizard" style="margin-top:30px;">
                    <a href="anadir_paso3_edit.php?tipo=<?php echo $tipo; ?>" class="btn-cancelar-link">Atrás</a>
                    <button type="submit" class="btn-aceptar">Guardar Cambios</button>
                </div>
            </form>

        <?php endif; ?>
    </div>
</body>
</html>