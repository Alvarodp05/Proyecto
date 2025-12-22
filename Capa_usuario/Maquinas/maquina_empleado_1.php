<?php
// 1. ¡INICIAR LA SESIÓN!
session_start();

// 2. INCLUIMOS LAS CLASES
include_once ("../../Capa_negocio/Maquinas/clases_maquina.php");

// 3. DEFINIMOS QUÉ MÁQUINA SOMOS
// --- ¡CORRECCIÓN IMPORTANTE! ---
$machine_id = 'maquina_1';

// 4. CREAMOS LOS OBJETOS
$config = new Maquina_Config($machine_id);
$estado = new Maquina_Valores($config);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interfaz: <?php echo htmlspecialchars($config->machine_name); ?></title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas.css">
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
</head>
<body>

    <div class="main-container">

        <div class="image-section">
            <?php
            // --- MOSTRAR ALARMAS ---
            if (empty($estado->alarmas_correctivas) && empty($estado->alarmas_preventivas) && empty($estado->stock_seguridad)) {
                echo "<div class='global-ok' style='background-color: #dff0d8; border: 1px solid #d6e9c6; color: #3c763d; width: 400px; padding: 15px; margin-bottom: 20px; border-radius: 10px; box-sizing: border-box; font-size: 0.95em;'><strong>TODO CORRECTO</strong></div>";
            }
            if (!empty($estado->alarmas_correctivas)) {
                echo "<div class='global-alarm-correctiva'><strong>¡ALARMA CORRECTIVA!</strong><ul>"; 
                foreach ($estado->alarmas_correctivas as $p) { echo "<li>$p</li>"; }
                echo "</ul></div>";
            }
            if (!empty($estado->alarmas_preventivas)) {
                echo "<div class='global-alarm-preventiva'><strong>¡ALARMA PREVENTIVA!</strong><ul>";
                foreach ($estado->alarmas_preventivas as $p) { echo "<li>$p</li>"; }
                echo "</ul></div>";
            }
            if (!empty($estado->stock_seguridad)) {
                echo "<div class='global-alarm-stock'><strong>¡ALARMA STOCK!</strong><ul>";
                foreach ($estado->stock_seguridad as $i) { echo "<li>$i</li>"; }
                echo "</ul></div>";
            }
            ?>
            <img src="<?php echo htmlspecialchars($config->imagen); ?>" alt="Imagen de la máquina">
        </div>

        <div class="content-section">
            
            <div class="content-header">
                <h1><?php echo htmlspecialchars($config->machine_name); ?></h1>
                <div class="header-logo">
                    <img src="../../Lib/Imagenes/logo_esquina.jpg" alt="Logo A6T">
                </div>
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