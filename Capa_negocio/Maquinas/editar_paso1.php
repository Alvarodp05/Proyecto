<?php
// Proyecto/Capa_negocio/Maquinas/editar_paso1.php

require_once("../Usuario/clase_usuario.php");
require_once("clases_maquina.php");

session_start();

// 1. SEGURIDAD
if (!isset($_SESSION['usuario_activo']) || !$_SESSION['usuario_activo']->esJefe()) {
    header("Location: ../../Capa_usuario/Acceso/Acceso_proyecto_clases.php");
    exit();
}

// 2. VERIFICAR DATOS
if (!isset($_SESSION['edit_data'])) {
    header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php");
    exit;
}

$data = $_SESSION['edit_data'];

// Errores visuales
$error_doble_accion = null;
if (isset($_SESSION['flash_error'])) {
    $error_doble_accion = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']); 
}

// Imagen actual
$imagen_actual = isset($data['imagen']) ? $data['imagen'] : '../../Lib/Imagenes/logo_esquina.jpg';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Máquina</title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <style>
        body {
            display: block;
            height: auto;
            min-height: 100vh;
            padding: 40px 20px;
            box-sizing: border-box;
            background-color: #7e8a50;
        }
        
        .main-container {
            margin: 0 auto;
            background-color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            max-width: 800px; /* Más estrecho para listas simples */
        }

        .img-preview {
            width: 150px;
            height: auto;
            border-radius: 8px;
            border: 2px solid #ccc;
            margin-bottom: 10px;
        }
        
        /* ESTILO LISTA SIMPLE */
        .simple-list {
            list-style-type: none;
            padding: 0;
            margin: 10px 0;
        }
        .simple-list li {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 10px 15px;
            margin-bottom: 8px;
            border-radius: 4px;
            color: #3f4b27;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .simple-list li span {
            font-weight: normal;
            color: #666;
            font-size: 0.9em;
        }

        .action-box {
            background: #e9ecef;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border: 1px solid #ced4da;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
    </style>
</head>
<body>
    <div class="main-container"> 
        
        <form action="editar_procesar.php" method="POST">
            
            <div class="content-header" style="align-items: center; display: flex; justify-content: space-between;">
                <div>
                    <h1>Editando: <?php echo htmlspecialchars($data['machine_name']); ?></h1>
                </div>
                <div style="text-align: right;">
                    <img src="<?php echo htmlspecialchars($imagen_actual); ?>" alt="Imagen Actual" class="img-preview">
                    <p style="font-size:0.8em; color:#666; margin:0;"></p>
                </div>
            </div>
			
            <?php if ($error_doble_accion): ?>
                <div style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                    <?php echo htmlspecialchars($error_doble_accion); ?>
                </div>
            <?php endif; ?>

            <div class="section">
                <h2>Datos Generales</h2>
                <div class="form-item-edit">
                    <label for="machine_name">Nombre de la Máquina:</label>
                    <input type="text" id="machine_name" name="machine_name" value="<?php echo htmlspecialchars($data['machine_name']); ?>" required>
                </div>
                <div class="form-item-edit">
                    <label for="description">Descripción:</label>
                    <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($data['description']); ?></textarea>
                </div>
                </div>

            <div class="section">
                <h2>Parámetros</h2>
                
                <div class="action-box">
                    <label for="param_action" style="font-weight:bold;">Acción</label>
                    <select name="param_action" id="param_action" style="padding:5px;">
                        <option value="" selected>-- Nada --</option>
                        <option value="add">Añadir Nuevo</option>
						<option value="edit">Editar Existente</option>
                        <option value="delete">Borrar Existente</option>
                    </select>
                </div>

                <?php if (empty($data['parameters'])): ?>
                    <p style="color:#666; font-style:italic;">No hay parámetros definidos.</p>
                <?php else: ?>
                    <ul class="simple-list">
                        <?php foreach($data['parameters'] as $p): ?>
                            <li>
                                <?php echo htmlspecialchars($p['label']); ?>
                                <span>(<?php echo htmlspecialchars($p['units'] ?? '-'); ?>)</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <h2>Stock de Seguridad</h2>
                
                <div class="action-box">
                    <label for="stock_action" style="font-weight:bold;">Acción</label>
                    <select name="stock_action" id="stock_action" style="padding:5px;">
                        <option value="" selected>-- Nada --</option>
                        <option value="add_stock">Añadir Nuevo</option>
                        <option value="edit_stock">Editar Existente</option>
                        <option value="delete_stock">Borrar Existente</option>
                    </select>
                </div>

                <?php if (empty($data['stock'])): ?>
                    <p style="color:#666; font-style:italic;">No hay stock definido.</p>
                <?php else: ?>
                    <ul class="simple-list">
                        <?php foreach($data['stock'] as $s): ?>
                            <li><?php echo htmlspecialchars($s['label']); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="buttons-edit-wizard">
                <a href="../../Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php" class="btn-cancelar-link">Cancelar</a>
                
                <button type="submit" name="accion_borrar" value="borrar" class="btn-cancelar" style="margin-left:auto; margin-right:10px;">Recargar Datos</button>
                
                <button type="submit" name="accion_siguiente" value="siguiente" class="btn-siguiente">Siguiente (Acción)</button>
                
                <button type="submit" name="accion_guardar" value="guardar" class="btn-aceptar">Guardar Todo</button>
            </div>

        </form>
    </div>
</body>
</html>