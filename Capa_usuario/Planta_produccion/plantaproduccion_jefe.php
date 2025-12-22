<?php
session_start();
include_once ("../../Capa_negocio/Maquinas/clases_maquina.php");

if (!isset($_SESSION['maquinas_borradas'])) {
    $_SESSION['maquinas_borradas'] = [];
}

$maquinas = new Maquinas(); 
$lista_archivos = $maquinas->lista_archivos;

function dibujarMaquina($machine_id) {
    if (in_array($machine_id, $_SESSION['maquinas_borradas'])) {
        echo '<div></div>'; 
        return false;
    }
    
    // Cargamos configuración original desde BD
    $config = new Maquina_Config($machine_id);
    
    if (strpos($config->machine_name, 'ERROR') !== false || 
        $config->machine_name == 'SLOT VACÍO 0' || 
        $config->machine_name == 'SLOT VACÍO 7') {
        return false;
    }

    // --- LÓGICA DE SUSTITUCIÓN ---
    $link_destino = $config->link; 
    $id_para_acciones = $machine_id; 
    
    // CASO MAQUINA 1
    if ($machine_id == 'maquina_1') {
        // Usamos $config->link en vez de $data['link']
        if ($config->link == 'ESTADO_B') {
             $link_destino = '../Maquinas/maquina_jefe_1_1.php';
             
             // Cargar datos visuales de la sustituta
             $config_sustituta = new Maquina_Config('maquina_1_1');
             if (strpos($config_sustituta->machine_name, 'ERROR') === false) {
                 $config = $config_sustituta;
                 $id_para_acciones = 'maquina_1_1'; 
             }
        } else {
             $link_destino = '../Maquinas/maquina_jefe_1.php';
        }
    } 
    // CASO MAQUINA 6 (Aquí estaba el error)
    elseif ($machine_id == 'maquina_6') {
        // ¡CORREGIDO! Usamos $config->link
        if ($config->link == 'ESTADO_B') {
             $link_destino = '../Maquinas/maquina_jefe_6_1.php';
             
             // Cargar datos visuales de la sustituta (6_1)
             $config_sustituta = new Maquina_Config('maquina_6_1');
             if (strpos($config_sustituta->machine_name, 'ERROR') === false) {
                 $config = $config_sustituta;
                 $id_para_acciones = 'maquina_6_1'; 
             }
        } else {
             $link_destino = '../Maquinas/maquina_jefe_6.php';
        }
    }
    // --- FIN LÓGICA ---

    echo '<div class="machine-card">';
    echo '    <a href="' . $link_destino . '">'; 
    echo '        <h3>' . $config->machine_id . '</h3>';
    echo '        <p>' . $config->machine_name . '</p>';
    echo '        <img src="' . $config->imagen . '" alt="Imagen de ' . $config->machine_name . '" class="machine-img">';
    echo '    </a>';
    
    echo '    <div class="machine-actions-container">';
    
    // FORMULARIO EDITAR
    echo '    <form class="form-edit" action="../../Capa_negocio/Maquinas/gestionar_maquina.php" method="POST">';
    echo '        <input type="hidden" name="machine_id" value="' . $id_para_acciones . '">';
    echo '        <input type="hidden" name="data_file" value="' . $id_para_acciones . '">'; 
    echo '        <select name="action">';
    echo '            <option value="editar">Editar</option>';
    
    if ($machine_id == 'maquina_1' || $machine_id == 'maquina_6') {
        echo '            <option value="sustituir">Sustituir</option>';
    }
    
    echo '        </select>';
    echo '        <button type="submit" class="btn-action">Ir</button>';
    echo '    </form>';
    
    // FORMULARIO BORRAR
    echo '    <form class="form-delete" action="../../Capa_negocio/Maquinas/gestionar_maquina.php" method="POST">';
    echo '        <input type="hidden" name="machine_id" value="' . $machine_id . '">';
    echo '        <input type="hidden" name="data_file" value="' . $machine_id . '">'; 
    echo '        <input type="hidden" name="action" value="borrar">'; 
    echo '        <button type="submit" class="btn-action-delete">Borrar</button>';
    echo '    </form>';
    echo '    </div>'; 
    echo '</div>';
    return true; 
}

// --- LÓGICA DE COMPROBACIÓN ---
$maq_0_activa = false;
$conf_0 = new Maquina_Config('maquina_0');
if ($conf_0->machine_name != 'SLOT VACÍO 0' && strpos($conf_0->machine_name, 'ERROR') === false && !in_array('maquina_0', $_SESSION['maquinas_borradas'])) {
    $maq_0_activa = true;
}

$maq_7_activa = false;
$conf_7 = new Maquina_Config('maquina_7');
if ($conf_7->machine_name != 'SLOT VACÍO 7' && strpos($conf_7->machine_name, 'ERROR') === false && !in_array('maquina_7', $_SESSION['maquinas_borradas'])) {
    $maq_7_activa = true;
}

// FLASH ERROR
$flash_error = null;
if (isset($_SESSION['flash_error'])) {
    $flash_error = $_SESSION['flash_error'];
    unset($_SESSION['flash_error']); 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema Jefe</title>
    <link rel="stylesheet" href="../../Lib/Estilos/estilo_planta.css">
</head>
<body>
	<div class="main-container">
        <div class="header">
            <img src="../../Lib/Imagenes/Logo_aceitunas.jpg" alt="Aceituna">
            <h1>SISTEMA JEFE</h1>
        </div>
        <hr>
        <?php if ($flash_error): ?>
            <div classs="global-alarm-correctiva" style="background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <?php echo htmlspecialchars($flash_error); ?>
            </div>
        <?php endif; ?>
        
        <div class="machines-grid">
            <?php
            if ($maq_0_activa) {
                dibujarMaquina('maquina_0');
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
                echo '<div class="arrow down-arrow" style="grid-column-start: 1;">&darr;</div>';
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
            }
            
            dibujarMaquina('maquina_1');
            echo '<div class="arrow">&rarr;</div>'; 
            dibujarMaquina('maquina_2');
            echo '<div class="arrow">&rarr;</div>'; 
            dibujarMaquina('maquina_3');

            echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
            echo '<div class="arrow down-arrow">&darr;</div>'; 

            dibujarMaquina('maquina_6');
            echo '<div class="arrow">&larr;</div>';
            dibujarMaquina('maquina_5');
            echo '<div class="arrow">&larr;</div>';
            dibujarMaquina('maquina_4');

            if ($maq_7_activa) {
                echo '<div class="arrow down-arrow" style="grid-column-start: 1;">&darr;</div>';
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
                dibujarMaquina('maquina_7');
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
            }
            ?>
        </div>

        <div class="buttons">
            <a href="../Acceso/Acceso_proyecto_clases.php"><button type="button" class="btn-cancelar">Atrás</button></a>
			<a href="../../Capa_negocio/Maquinas/anadir_paso1.php">
                <button type="button" class="btn-aceptar">Añadir Máquina</button>
            </a>
            <a href="../../Capa_negocio/Planta_produccion/reset_vista.php"><button type="button" class="btn-aceptar">Restaurar Máquinas</button></a>
        </div>
    </div>
</body>
</html>