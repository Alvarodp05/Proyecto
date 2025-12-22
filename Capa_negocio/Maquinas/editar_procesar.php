<?php
session_start();
include_once ("../../Lib/BD/AccesoBD.php");

if (!isset($_SESSION['edit_data'])) {
    header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
    exit;
}

// 1. Actualizar datos en sesión (Navegación)
if (isset($_POST['machine_name'])) $_SESSION['edit_data']['machine_name'] = $_POST['machine_name'];
if (isset($_POST['description']))  $_SESSION['edit_data']['description'] = $_POST['description'];

// 2. Gestión de Botones de Navegación (Siguiente, Borrar Formulario...)
if (isset($_POST['accion_siguiente'])) {
    $param_accion = $_POST['param_action'] ?? '';
    $stock_accion = $_POST['stock_action'] ?? '';

    if ($param_accion != '' && $stock_accion != '') {
        $_SESSION['flash_error'] = "Error: No puedes seleccionar ambas acciones.";
        header("Location: editar_paso1.php");
        exit;
    }
    if ($param_accion != '') {
        header("Location: editar_paso2_" . ($param_accion == 'add' ? 'add' : ($param_accion == 'edit' ? 'edit' : 'del')) . ".php?tipo=param");
        exit;
    }
    if ($stock_accion != '') {
        header("Location: editar_paso2_" . ($stock_accion == 'add_stock' ? 'add' : ($stock_accion == 'edit_stock' ? 'edit' : 'del')) . ".php?tipo=stock");
        exit;
    }
    header("Location: editar_paso1.php");
    exit;
}

if (isset($_POST['accion_borrar'])) {
    $bd = new AccesoBD();
    $_SESSION['edit_data'] = $bd->obtenerDatosMaquina($_SESSION['edit_data']['machine_id']);
    header("Location: editar_paso1.php");
    exit;
}

// --- 3. GUARDAR CAMBIOS REALES EN LA BASE DE DATOS ---
if (isset($_POST['accion_guardar'])) {
    
    $data = $_SESSION['edit_data'];
    $id_maquina = $data['machine_id'];
    
    $bd = new AccesoBD();

    // A. Actualizar Datos Generales (Nombre, Descripción, Imagen)
    $bd->guardarInfoGeneral($id_maquina, $data['machine_name'], $data['description'], $data['imagen']);

    // B. Sincronización Total de Parámetros y Stock
    // 1. Borramos todo lo viejo de esta máquina en la BD
    $bd->limpiarDetallesMaquina($id_maquina);

    // 2. Insertamos todo lo nuevo que hay en la sesión
    if (!empty($data['parameters'])) {
        foreach ($data['parameters'] as $key => $p) {
            $bd->insertarParametro(
                $id_maquina, 
                $key, // El ID del parámetro
                $p['label'], 
                $p['units'], 
                $p['alarm_c_low'], $p['alarm_c_high'], 
                $p['alarm_p_low'], $p['alarm_p_high'], 
                $p['rand_min'], $p['rand_max']
            );
        }
    }

    if (!empty($data['stock'])) {
        foreach ($data['stock'] as $key => $s) {
            $bd->insertarStock(
                $id_maquina, 
                $key, // El ID del stock
                $s['label'], 
                $s['alarm_c_low'], $s['alarm_c_high'], 
                $s['alarm_p_low'], $s['alarm_p_high'], 
                $s['rand_min'], $s['rand_max']
            );
        }
    }
    
    // C. Limpieza y Redirección
    unset($_SESSION['edit_data']);
    
    // Lógica de redirección (se mantiene igual)
    $link_estado = $data['link'];
    $redirect_url = "";

    if ($id_maquina == 'maquina_1') {
        if ($link_estado == 'ESTADO_B') $redirect_url = '../../Capa_usuario/Maquinas/maquina_jefe_1_1.php';
        else $redirect_url = '../../Capa_usuario/Maquinas/maquina_jefe_1.php';
    } elseif ($id_maquina == 'maquina_6') {
        if ($link_estado == 'ESTADO_B') $redirect_url = '../../Capa_usuario/Maquinas/maquina_jefe_6b.php';
        else $redirect_url = '../../Capa_usuario/Maquinas/maquina_jefe_6.php';
    } elseif ($id_maquina == 'maquina_0') {
        $redirect_url = '../../Capa_usuario/Maquinas/maquina_jefe_0.php';
    } elseif ($id_maquina == 'maquina_7') {
        $redirect_url = '../../Capa_usuario/Maquinas/maquina_jefe_7.php';
    } else {
        // Extraer numero para el resto (2, 3, 4, 5)
        $num = str_replace('maquina_', '', $id_maquina);
        $redirect_url = "../../Capa_usuario/Maquinas/maquina_jefe_{$num}.php";
    }
    
    header("Location: " . $redirect_url);
    exit;
}

header("Location: editar_paso1.php");
exit;
?>