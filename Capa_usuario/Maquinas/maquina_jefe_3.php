<?php
session_start();
include_once ("../../Capa_negocio/Maquinas/clases_maquina.php");

$machine_id = 'maquina_3'; // ID en Base de Datos

$config = new Maquina_Config($machine_id);
$estado = new Maquina_Valores($config);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>ADMIN - <?php echo htmlspecialchars($config->machine_name); ?></title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
</head>
<body>
    <div class="main-container">
        <div class="image-section">
            <?php
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
            <img src="<?php echo htmlspecialchars($config->imagen); ?>" alt="Imagen Maquina">
        </div>
        
        <div class="content-section">
            <div class="content-header">
                <h1>JEFE - <?php echo htmlspecialchars($config->machine_name); ?></h1>
                <div class="header-logo"><img src="../../Lib/Imagenes/logo_esquina.jpg" alt="Logo"></div>
            </div> 
            
            <div class="section">
                <h2>Funcionamiento</h2>
                <p><?php echo nl2br(htmlspecialchars($config->description ?? '')); ?></p>
            </div>

            <form action="../../Capa_negocio/Maquinas/guardar_bd.php" method="POST">
                <input type="hidden" name="machine_id" value="<?php echo $config->machine_id; ?>">
                <input type="hidden" name="redirect_url" value="../../Capa_usuario/Maquinas/maquina_jefe_3.php">

                <h2>Parámetros</h2>
                <table border="1" cellpadding="5" cellspacing="0"> 
                    <thead><tr><th>Parámetro</th><th>Hoy</th><th>Hace 1 día</th><th>Hace 2 días</th></tr></thead>
                    <tbody>
                        <?php foreach ($config->parameters as $key => $conf): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($conf['label']); ?> (<?php echo htmlspecialchars($conf['units']); ?>)</td>
                            <td>
    <input type="hidden" name="original_<?php echo $key; ?>" value="<?php echo $estado->valores_actuales[$key]; ?>">
    
    <input type="text" name="<?php echo $key; ?>" value="<?php echo $estado->valores_actuales[$key]; ?>">
</td>
                            <td><input type="text" value="<?php echo $estado->valores_ayer[$key]; ?>" readonly></td>
                            <td><input type="text" value="<?php echo $estado->valores_antier[$key]; ?>" readonly></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h2>Stock de Seguridad</h2>
                <table border="1" cellpadding="5" cellspacing="0"> 
                    <thead><tr><th>Pieza</th><th>Hoy</th><th>Hace 1 día</th></tr></thead>
                    <tbody>
                         <?php foreach ($config->stock as $key => $conf): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($conf['label']); ?> (<?php echo htmlspecialchars($conf['units']); ?>)</td>
                            <td>
    <input type="hidden" name="original_<?php echo $key; ?>" value="<?php echo $estado->valores_actuales[$key]; ?>">
    
    <input type="text" name="<?php echo $key; ?>" value="<?php echo $estado->valores_actuales[$key]; ?>">
</td>
                            <td><input type="text" value="<?php echo $estado->valores_ayer[$key]; ?>" readonly></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <br> 
                <div class="buttons-edit-wizard" style="justify-content: space-between;"> 
                    <a href="../Planta_produccion/plantaproduccion_jefe.php" class="btn-cancelar-link">Volver</a>
                    <button type="submit" class="btn-aceptar">Guardar Cambios</button>
                </div>
            </form> 
        </div> 
    </div> 
</body>
</html>