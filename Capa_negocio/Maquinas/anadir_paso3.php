<?php
// Proyecto/Capa_negocio/Maquinas/anadir_paso3.php
require_once("../Usuario/clase_usuario.php");
session_start();

if (!isset($_SESSION['add_data'])) {
    header("Location: anadir_paso1.php");
    exit;
}

$data = $_SESSION['add_data'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configurar Máquina - Paso 3</title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <style>
        /* Estilo similar a editar_paso1 */
        body { background-color: #7e8a50; padding: 40px 20px; }
        .main-container { background: #fff; padding: 40px; border-radius: 20px; max-width: 800px; margin: 0 auto; }
        .simple-list { list-style: none; padding: 0; }
        .simple-list li { background: #f9f9f9; border: 1px solid #ddd; padding: 10px; margin-bottom: 5px; border-radius: 4px; display: flex; justify-content: space-between; }
        .action-bar { background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="content-header">
            <h1>Configurar: <?php echo htmlspecialchars($data['machine_name']); ?></h1>
            <p style="color:#666;">Define los parámetros y stock antes de finalizar.</p>
        </div>

        <div class="section">
            <h2>Parámetros</h2>
            <div class="action-bar">
                <span>¿Añadir métricas?</span>
                <a href="anadir_paso3_add.php?tipo=param" class="btn-siguiente" style="text-decoration:none; font-size:0.9em;">+ Añadir Nuevo</a>
            </div>
            
            <?php if (empty($data['parameters'])): ?>
                <p style="font-style:italic; color:#777;">Sin parámetros definidos.</p>
            <?php else: ?>
                <ul class="simple-list">
                    <?php foreach($data['parameters'] as $p): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($p['label']); ?></strong>
                            <span><?php echo htmlspecialchars($p['units']); ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Stock</h2>
            <div class="action-bar">
                <span>¿Control de material?</span>
                <a href="anadir_paso3_add.php?tipo=stock" class="btn-siguiente" style="text-decoration:none; font-size:0.9em;">+ Añadir Nuevo</a>
            </div>

            <?php if (empty($data['stock'])): ?>
                <p style="font-style:italic; color:#777;">Sin stock definido.</p>
            <?php else: ?>
                <ul class="simple-list">
                    <?php foreach($data['stock'] as $s): ?>
                        <li><strong><?php echo htmlspecialchars($s['label']); ?></strong></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <form action="anadir_procesar.php" method="POST">
            <input type="hidden" name="accion_finalizar" value="true">
            <div class="buttons-edit-wizard">
                <a href="anadir_paso2.php" class="btn-cancelar-link">Atrás (Datos Básicos)</a>
                <button type="submit" class="btn-aceptar">✅ FINALIZAR Y GUARDAR</button>
            </div>
        </form>
    </div>
</body>
</html>