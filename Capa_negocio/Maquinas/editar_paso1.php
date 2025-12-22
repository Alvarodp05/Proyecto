<?php
session_start();

// Si no hay datos de edición en la sesión, volvemos a la planta
if (!isset($_SESSION['edit_data'])) {
    header("Location:../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
    exit;
}

// Cargamos los datos de la sesión en una variable local
$data = $_SESSION['edit_data'];

// --- ¡NUEVO! MOSTRAR MENSAJE DE ERROR ---
$error_doble_accion = null;
if (isset($_SESSION['flash_error'])) {
    $error_doble_accion = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']); // Limpiamos
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Máquina</title>
    <!-- Reutilizamos el estilo de jefe, pero necesitaremos añadirle más CSS -->
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
</head>
<body>
    <div class="main-container" style="display:block; max-width: 800px;"> <!-- Quitamos flex para un formulario -->
        
        <!-- El formulario se envía a SÍ MISMO para procesar la acción -->
        <form action="editar_procesar.php" method="POST">
            <div class="content-header">
                <h1>Editando: <?php echo htmlspecialchars($data['machine_name']); ?></h1>
            </div>
			
			<!-- ¡NUEVO! Mostramos el error si existe -->
            <?php if ($error_doble_accion): ?>
                <div classs="global-alarm-correctiva" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($error_doble_accion); ?>
                </div>
            <?php endif; ?>

            <!-- 1. Editar Nombre y Descripción -->
            <div class="section">
                <h2>Datos Generales</h2>
                <div class="form-item-edit">
                    <label for="machine_name">Nombre de la Máquina:</label>
                    <input type="text" id="machine_name" name="machine_name" value="<?php echo htmlspecialchars($data['machine_name']); ?>">
                </div>
                <div class="form-item-edit">
                    <label for="description">Descripción:</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($data['description']); ?></textarea>
                </div>
            </div>

            <!-- 2. ¡ORDEN CORREGIDO! Parámetros AHORA PRIMERO -->
            <div class="section">
                <h2>Parámetros</h2>
                <div class="form-item-edit">
                    <label for="param_action">Acción de Parámetro:</label>
                    <select name="param_action" id="param_action">
                        <option value="" selected>Ninguna</option>
                        <option value="add">Añadir Nuevo Parámetro</option>
						<option value="edit">Editar Parámetro Existente</option>
                        <option value="delete">Borrar Parámetro Existente</option>
                    </select>
                </div>
                <h4>Parámetros Actuales:</h4>
                <ul class="param-list">
                    <?php if (empty($data['parameters'])): ?>
                        <li>No hay parámetros definidos.</li>
                    <?php else: ?>
                        <?php foreach($data['parameters'] as $key => $p): ?>
                            <li><strong><?php echo htmlspecialchars($p['label']); ?></strong> (<?php echo $key; ?>)</li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- 3. ¡ORDEN CORREGIDO! Stock AHORA SEGUNDO -->
            <div class="section">
                <h2>Stock de Seguridad</h2>
                <div class="form-item-edit">
                    <label for="stock_action">Acción de Stock:</label>
                    <select name="stock_action" id="stock_action">
                        <option value="" selected>Ninguna</option>
                        <option value="add_stock">Añadir Nuevo Stock</option>
                        <option value="edit_stock">Editar Stock Existente</option>
                        <option value="delete_stock">Borrar Stock Existente</option>
                    </select>
                </div>
                <h4>Stock Actual:</h4>
                <ul class="param-list">
                    <?php if (empty($data['stock'])): ?>
                        <li>No hay stock definido.</li>
                    <?php else: ?>
                        <?php foreach($data['stock'] as $key => $p): ?>
                            <li><strong><?php echo htmlspecialchars($p['label']); ?></strong> (<?php echo $key; ?>)</li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
            <!-- 3. Botones de Acción -->
            <div class="buttons-edit-wizard">
                <button type="submit" name="accion_guardar" value="guardar" class="btn-aceptar">Guardar Cambios</button>
                <button type="submit" name="accion_borrar" value="borrar" class="btn-cancelar">Borrar Formulario</button>
                <button type="submit" name="accion_siguiente" value="siguiente" class="btn-siguiente">Siguiente</button>
                <a href="../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php" class="btn-cancelar-link">Cancelar</a>
            </div>

        </form>
    </div>
</body>
</html>