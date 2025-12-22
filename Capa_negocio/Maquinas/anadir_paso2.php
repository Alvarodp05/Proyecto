<?php
session_start();
include_once ("../../Lib/BD/AccesoBD.php");

// Si se llega aquí desde el Paso 1, configuramos la sesión
if (isset($_POST['posicion'])) {
    $_SESSION['anadir_posicion'] = $_POST['posicion'];
    
    $bd = new AccesoBD();
	
	// Determinamos qué máquina vamos a cargar de la BD como plantilla
    if ($_POST['posicion'] == 'inicio') {
        $machine_id_target = 'maquina_0';
        $link_path = '../Maquinas/maquina_jefe_0.php'; 
    } else {
        $machine_id_target = 'maquina_7';
        $link_path = '../Maquinas/maquina_jefe_7.php';
    }
    
    // --- ¡CORRECCIÓN! CARGA DESDE LA BD ---
    // Obtenemos los datos actuales del slot vacío desde MySQL
    $_SESSION['anadir_data'] = $bd->obtenerDatosMaquina($machine_id_target);
    
    // Ya no necesitamos guardar rutas de archivos físicos
    
	// Asignamos el link de JEFE correcto y valores por defecto
    $_SESSION['anadir_data']['link'] = $link_path;
    $_SESSION['anadir_data']['machine_id'] = $machine_id_target;
    $_SESSION['anadir_data']['machine_name'] = 'Nombre de la Nueva Máquina';
	
}

// Seguridad
if (!isset($_SESSION['anadir_data'])) {
    header("Location: anadir_paso1.php");
    exit;
}

$data = $_SESSION['anadir_data'];

$error_doble_accion = null;
if (isset($_SESSION['flash_error'])) {
    $error_doble_accion = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Máquina - Paso 2</title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
</head>
<body>
    <div class="main-container" style="display:block; max-width: 800px;">
        
        <form action="anadir_procesar.php" method="POST">
            <div class="content-header">
                <h1>Añadiendo: <?php echo htmlspecialchars($data['machine_name']); ?></h1>
            </div>
            
            <?php if ($error_doble_accion): ?>
                <div classs="global-alarm-correctiva" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($error_doble_accion); ?>
                </div>
            <?php endif; ?>

            <div class="section">
                <h2>Datos Generales</h2>
                
                <div class="form-item-edit">
                    <label for="machine_id">ID de la Máquina (¡No cambiar!):</label>
                    <input type="text" id="machine_id" name="machine_id" value="<?php echo htmlspecialchars($data['machine_id']); ?>" readonly style="background:#eee;">
                </div>
                
                <div class="form-item-edit">
                    <label for="machine_name">Nombre de la Máquina:</label>
                    <input type="text" id="machine_name" name="machine_name" value="<?php echo htmlspecialchars($data['machine_name']); ?>">
                </div>
                
                <div class="form-item-edit">
                    <input type="hidden" id="link" name="link" value="<?php echo htmlspecialchars($data['link']); ?>">
                </div>
                
                <div class="form-item-edit">
                    <label for="imagen">Archivo de Imagen (Ruta):</label>
                    <input type="text" id="imagen" name="imagen" value="<?php echo htmlspecialchars($data['imagen']); ?>">
                </div>
                
                <div class="form-item-edit">
                    <label for="description">Descripción:</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($data['description']); ?></textarea>
                </div>
            </div>

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
            
            <div class="buttons-edit-wizard">
                <button type="submit" name="accion_guardar" value="guardar" class="btn-aceptar">AÑADIR MÁQUINA</button>
                <button type="submit" name="accion_borrar" value="borrar" class="btn-cancelar">Borrar Formulario</button>
                <button type="submit" name="accion_siguiente" value="siguiente" class="btn-siguiente">Siguiente</button>
                <a href="../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php" class="btn-cancelar-link">Cancelar</a>
            </div>

        </form>
    </div>
</body>
</html>