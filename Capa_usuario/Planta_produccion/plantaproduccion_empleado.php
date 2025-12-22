<?php
// 1. ¡INICIAR LA SESIÓN!
session_start();

// 2. Incluimos el archivo de clases (que ahora conecta a BD)
include_once ("../../Capa_negocio/Maquinas/clases_maquina.php");

// 3. Inicializa la lista de borrados si no existe
if (!isset($_SESSION['maquinas_borradas'])) {
    $_SESSION['maquinas_borradas'] = [];
}

// 4. Creamos el objeto: esto ejecuta el constructor y obtiene la lista de la BD
$maquinas = new Maquinas(); 
$lista_archivos = $maquinas->lista_archivos; // IDs de la BD

// --- FUNCIÓN AUXILIAR DE DIBUJO (Lógica Empleado) ---
function dibujarMaquina_Empleado($machine_id) {
    // 1. Si está en la lista de borrados, no dibujar
    if (in_array($machine_id, $_SESSION['maquinas_borradas'])) {
        echo '<div></div>'; 
        return false;
    }
    
    // 2. Cargar datos de la BD usando la clase Maquina_Config
    $config = new Maquina_Config($machine_id);
    
    // 3. Si es un slot vacío o error, no dibujar
    if (strpos($config->machine_name, 'ERROR') !== false || 
        $config->machine_name == 'SLOT VACÍO 0' || 
        $config->machine_name == 'SLOT VACÍO 7') {
        return false; 
    }

    // --- LÓGICA DE ENLACE Y SUSTITUCIÓN (ESTADOS) ---
    $link_destino = $config->link; // Por defecto
    
    // CASO MAQUINA 1 (Sustitución Completa)
    if ($machine_id == 'maquina_1') {
        // Leemos el estado guardado en la BD
        if ($config->link == 'ESTADO_B') {
            // ESTADO B: Vamos a la sustituta (1_1)
            $link_destino = '../Maquinas/maquina_empleado_1_1.php'; 
            
            // Truco visual: Cargamos la config de la 1_1 para mostrar SU nombre e imagen
            $config_sustituta = new Maquina_Config('maquina_1_1');
            if (strpos($config_sustituta->machine_name, 'ERROR') === false) {
                $config = $config_sustituta;
            }
        } else {
            // ESTADO A: Original
            $link_destino = '../Maquinas/maquina_empleado_1.php'; 
        }
    } 
    // CASO MAQUINA 6 (Sustitución)
    elseif ($machine_id == 'maquina_6') {
        // Leemos el estado guardado en la BD
        if ($config->link == 'ESTADO_B') {
            // ESTADO B: Vamos a la vista B (6b)
            $link_destino = '../Maquinas/maquina_empleado_6_1.php';
            
            // Truco visual: Cargamos la config de la 6_1 (Tanque 2)
            $config_sustituta = new Maquina_Config('maquina_6_1');
             if (strpos($config_sustituta->machine_name, 'ERROR') === false) {
                 $config = $config_sustituta;
             }
        } else {
            // ESTADO A: Original
            $link_destino = '../Maquinas/maquina_empleado_6.php';
        }
    }
    // RESTO DE MÁQUINAS (Forzamos siempre a sus vistas de empleado estándar)
    elseif ($machine_id == 'maquina_2') { $link_destino = '../Maquinas/maquina_empleado_2.php'; }
    elseif ($machine_id == 'maquina_3') { $link_destino = '../Maquinas/maquina_empleado_3.php'; }
    elseif ($machine_id == 'maquina_4') { $link_destino = '../Maquinas/maquina_empleado_4.php'; }
    elseif ($machine_id == 'maquina_5') { $link_destino = '../Maquinas/maquina_empleado_5.php'; }
    elseif ($machine_id == 'maquina_0') { $link_destino = '../Maquinas/maquina_empleado_0.php'; }
    elseif ($machine_id == 'maquina_7') { $link_destino = '../Maquinas/maquina_empleado_7.php'; }
    
    // --- FIN LÓGICA ---

    echo '<div class="machine-card">';
    echo '    <a href="' . $link_destino . '">';
    echo '        <h3>' . $config->machine_id . '</h3>';
    echo '        <p>' . $config->machine_name . '</p>';
    echo '        <img src="' . $config->imagen . '" alt="Imagen de ' . $config->machine_name . '" class="machine-img">';
    echo '    </a>';
    // El empleado NO tiene botones de editar/borrar/sustituir
    echo '</div>';
    return true; 
}

// --- LÓGICA DE COMPROBACIÓN DE ACTIVIDAD (Slots 0 y 7) ---
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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Empleado</title>
    <link rel="stylesheet" href="../../Lib/Estilos/estilo_planta.css">
</head>
<body>
    <div class="main-container">
        <div class="header">
            <img src="../../Lib/Imagenes/Logo_aceitunas.jpg" alt="Aceituna">
            <h1>SISTEMA EMPLEADO</h1>
        </div>
        <hr>

        <div class="machines-grid">
            <?php
            // --- FILA 0 (Opcional) ---
            if ($maq_0_activa) {
                dibujarMaquina_Empleado('maquina_0');
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
                echo '<div class="arrow down-arrow" style="grid-column-start: 1;">&darr;</div>';
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
            }
            
            // --- FILA 1 ---
            dibujarMaquina_Empleado('maquina_1');
            echo '<div class="arrow">&rarr;</div>'; 
            dibujarMaquina_Empleado('maquina_2');
            echo '<div class="arrow">&rarr;</div>'; 
            dibujarMaquina_Empleado('maquina_3');

            // --- FILA 2: Flecha Abajo ---
            echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
            echo '<div class="arrow down-arrow">&darr;</div>'; 

            // --- FILA 3 ---
            dibujarMaquina_Empleado('maquina_6');
            echo '<div class="arrow">&larr;</div>';
            dibujarMaquina_Empleado('maquina_5');
            echo '<div class="arrow">&larr;</div>';
            dibujarMaquina_Empleado('maquina_4');

            // --- FILA 4 (Opcional) ---
            if ($maq_7_activa) {
                echo '<div class="arrow down-arrow" style="grid-column-start: 1;">&darr;</div>';
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
                dibujarMaquina_Empleado('maquina_7');
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
            }
            ?>
        </div>

        <div class="buttons">
            <a href="../Acceso/Acceso_proyecto_clases.php"><button type="button" class="btn-cancelar">Atrás</button></a>
        </div>
    </div>
</body>
</html>