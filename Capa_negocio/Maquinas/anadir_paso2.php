<?php
// Proyecto/Capa_negocio/Maquinas/anadir_paso2.php
require_once("../Usuario/clase_usuario.php");
session_start();

// 1. INICIALIZAR SI VENIMOS DEL PASO 1
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_maquina'])) {
    $_SESSION['add_data'] = [
        'machine_id' => $_POST['id_maquina'],
        'machine_name' => $_POST['nombre'], // Nombre inicial
        'description' => '',
        'imagen' => '../../Lib/Imagenes/logo_esquina.jpg',
        'orden' => ($_POST['id_maquina'] == 'maquina_0' ? 1 : 8),
        'parameters' => [],
        'stock' => []
    ];
}

// 2. SEGURIDAD
if (!isset($_SESSION['add_data'])) {
    header("Location: anadir_paso1.php");
    exit();
}

$data = $_SESSION['add_data'];
$error = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : null;
unset($_SESSION['flash_error']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configurar Nueva Máquina</title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <style>
        /* CSS CORREGIDO */
        * { box-sizing: border-box; }
        
        body { 
            background-color: #7e8a50; 
            padding: 40px 20px; 
            font-family: Arial, sans-serif;
        }
        .main-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 20px;
            max-width: 800px;
            margin: 0 auto;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .form-item-edit textarea, 
        .form-item-edit input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
        }

        .action-box { 
            background: #f8f9fa; 
            padding: 15px; 
            border-radius: 8px; 
            border: 1px solid #dee2e6;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .simple-list { list-style: none; padding: 0; margin: 0; }
        .simple-list li { 
            background: #fff; 
            border: 1px solid #e9ecef; 
            padding: 12px; 
            margin-bottom: 5px; 
            border-radius: 6px;
            display: flex; 
            justify-content: space-between;
        }
        
        .img-preview { width: 80px; border: 1px solid #ccc; margin-left: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="main-container"> 
        
        <form action="anadir_procesar.php" method="POST">
            
            <div class="content-header" style="border-bottom: 2px solid #eee; padding-bottom: 15px; margin-bottom: 20px;">
                <h1>Creando: <?php echo htmlspecialchars($data['machine_name']); ?></h1>
            </div>
			
            <?php if ($error): ?>
                <div style="background:#f8d7da; color:#721c24; padding:15px; border-radius:8px; margin-bottom:20px; border:1px solid #f5c6cb;">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="section">
                <h2>1. Datos Generales</h2>
                
                <div class="form-item-edit">
                    <label>Nombre de la Máquina:</label>
                    <input type="text" name="nombre" value="<?php echo htmlspecialchars($data['machine_name']); ?>" required>
                </div>

                <div class="form-item-edit" style="margin-top:10px;">
                    <label>Descripción:</label>
                    <textarea name="description" rows="3"><?php echo htmlspecialchars($data['description']); ?></textarea>
                </div>
                
                <div class="form-item-edit" style="margin-top:10px;">
                    <label>Imagen (URL):</label>
                    <div style="display:flex; align-items:center;">
                        <input type="text" name="imagen" value="<?php echo htmlspecialchars($data['imagen']); ?>">
                        <img src="<?php echo htmlspecialchars($data['imagen']); ?>" alt="Img" class="img-preview">
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>2. Parámetros</h2>
                <div class="action-box">
                    <span>Opciones:</span>
                    <select name="param_action" style="padding:8px; border-radius:4px;">
                        <option value="" selected>-- Seleccionar --</option>
                        <option value="add">Añadir Nuevo</option>
						<option value="edit">Editar Seleccionado</option>
                        <option value="delete">Borrar Seleccionado</option>
                    </select>
                </div>
                
                <?php if (empty($data['parameters'])): ?>
                    <div style="text-align:center; padding:10px; color:#999; border:1px dashed #ccc; border-radius:5px;">Sin parámetros</div>
                <?php else: ?>
                    <ul class="simple-list">
                        <?php foreach($data['parameters'] as $p): ?>
                            <li>
                                <strong><?php echo htmlspecialchars($p['label']); ?></strong>
                                <span style="color:#666; font-size:0.9em;"><?php echo htmlspecialchars($p['units'] ?? ''); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <div class="section">
                <h2>3. Stock</h2>
                <div class="action-box">
                    <span>Opciones:</span>
                    <select name="stock_action" style="padding:8px; border-radius:4px;">
                        <option value="" selected>-- Seleccionar --</option>
                        <option value="add_stock">Añadir Nuevo</option>
                        <option value="edit_stock">Editar Seleccionado</option>
                        <option value="delete_stock">Borrar Seleccionado</option>
                    </select>
                </div>

                <?php if (empty($data['stock'])): ?>
                    <div style="text-align:center; padding:10px; color:#999; border:1px dashed #ccc; border-radius:5px;">Sin stock</div>
                <?php else: ?>
                    <ul class="simple-list">
                        <?php foreach($data['stock'] as $s): ?>
                            <li><strong><?php echo htmlspecialchars($s['label']); ?></strong></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>

            <div class="buttons-edit-wizard" style="margin-top:30px; border-top:1px solid #eee; padding-top:20px;">
                <a href="anadir_paso1.php" class="btn-cancelar-link">Cancelar</a>
                
                <button type="submit" name="accion_siguiente" value="siguiente" class="btn-siguiente" style="margin-left:auto; margin-right:10px;">Ir a Acción</button>
                
                <button type="submit" name="accion_guardar" value="guardar" class="btn-aceptar">Guardar Máquina</button>
            </div>

        </form>
    </div>
</body>
</html>