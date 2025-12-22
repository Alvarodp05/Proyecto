<?php
session_start();
include_once ("../../Capa_negocio/Maquinas/clases_maquina.php");
$machine_id = 'maquina_5';
$config = new Maquina_Config($machine_id);
$estado = new Maquina_Valores($config);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Interfaz: <?php echo htmlspecialchars($config->machine_name); ?></title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas.css">
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
</head>
<body>
    <div class="main-container">
        <div class="image-section">
            <img src="<?php echo htmlspecialchars($config->imagen); ?>" alt="Imagen Maquina">
            </div>
        <div class="content-section">
            <div class="content-header">
                <h1><?php echo htmlspecialchars($config->machine_name); ?></h1>
                <div class="header-logo"><img src="../../Lib/Imagenes/logo_esquina.jpg" alt="Logo"></div>
            </div> 
            <div class="section">
                <h2>Funcionamiento</h2>
                <p><?php echo nl2br(htmlspecialchars($config->description ?? '')); ?></p>
            </div>
            <div class="details-grid">
                <div class="column">
                    <h2>Parámetros</h2>
                    <?php foreach ($config->parameters as $key => $conf): ?>
                        <div class="form-item">
                            <label><?php echo htmlspecialchars($conf['label']); ?></label>
                            <div class="input-group">
                                <input type="text" value="<?php echo $estado->valores_actuales[$key]; ?>" readonly>
                                <span class="unit-param"><?php echo htmlspecialchars($conf['units']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="column">
                    <h2>Stock de Seguridad</h2>
                    <?php foreach ($config->stock as $key => $conf): ?>
                        <div class="form-item">
                            <label><?php echo htmlspecialchars($conf['label']); ?></label>
                            <div class="input-group">
                                <input type="text" value="<?php echo $estado->valores_actuales[$key]; ?>" readonly>
                                <span class="unit-stock"><?php echo htmlspecialchars($conf['units']); ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div> 
            <div class="buttons-edit-wizard" style="justify-content: flex-end; margin-top: 30px;">
                <a href="../Planta_produccion/plantaproduccion_empleado.php" class="btn-cancelar-link">Volver</a>
            </div>
        </div> 
    </div> 
</body>
</html>