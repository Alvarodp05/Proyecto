<?php
// Proyecto/Capa_negocio/Maquinas/editar_procesar.php
session_start();
require_once("clases_maquina.php");

// Verificación básica
if (!isset($_SESSION['edit_data'])) {
    header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php");
    exit;
}

// =========================================================
// CASO A: GUARDAR FINAL (Desde Paso 1)
// =========================================================
if (isset($_POST['accion_guardar'])) {
    $_SESSION['edit_data']['machine_name'] = $_POST['machine_name'];
    $_SESSION['edit_data']['description'] = $_POST['description'];

    $id_maquina_editada = $_SESSION['edit_data']['machine_id'];

    Maquina::guardarDesdeSesion($_SESSION['edit_data']);

    unset($_SESSION['edit_data']);

    header("Location: ../../Capa_usuario/Maquinas/maquinas_plantilla.php?id=" . $id_maquina_editada);
    exit;
}

// =========================================================
// CASO B: CANCELAR O BORRAR
// =========================================================
elseif (isset($_POST['accion_borrar'])) {
    unset($_SESSION['edit_data']);
    header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php");
    exit;
}

// =========================================================
// CASO C: IR AL SIGUIENTE PASO (Wizard)
// =========================================================
elseif (isset($_POST['accion_siguiente'])) {
    // Guardar cambios temporales
    $_SESSION['edit_data']['machine_name'] = $_POST['machine_name'];
    $_SESSION['edit_data']['description'] = $_POST['description'];

    $p_action = $_POST['param_action'];
    $s_action = $_POST['stock_action'];

    // --- CORRECCIÓN: VALIDACIÓN DE DOBLE ACCIÓN ---
    // Si ambos tienen valor (diferente de vacío), lanzamos error.
    if (!empty($p_action) && !empty($s_action)) {
        $_SESSION['flash_error'] = "Error: No puedes editar Parámetros y Stock al mismo tiempo. Por favor, selecciona solo una acción.";
        header("Location: editar_paso1.php");
        exit;
    }

    // Determinar a dónde ir
    if ($p_action == 'add') header("Location: editar_paso2_add.php?tipo=param");
    elseif ($p_action == 'edit') header("Location: editar_paso2_edit.php?tipo=param");
    elseif ($p_action == 'delete') header("Location: editar_paso2_del.php?tipo=param");
    elseif ($s_action == 'add_stock') header("Location: editar_paso2_add.php?tipo=stock");
    elseif ($s_action == 'edit_stock') header("Location: editar_paso2_edit.php?tipo=stock");
    elseif ($s_action == 'delete_stock') header("Location: editar_paso2_del.php?tipo=stock");
    else {
        $_SESSION['flash_error'] = "Por favor, selecciona una acción para continuar.";
        header("Location: editar_paso1.php");
    }
    exit;
}

// =========================================================
// CASO D: PROCESAR FORMULARIOS DEL PASO 2 (Add/Edit)
// =========================================================
if (isset($_POST['form_type'])) {
    
    $tipo = $_POST['tipo_item']; 
    $id = $_POST['item_id'] ?? uniqid(); 

    $nuevoItem = [
        'label' => $_POST['label'],
        'alarm_c_low' => $_POST['alarm_c_low'] ?? 0,
        'rand_min' => $_POST['rand_min'] ?? 0,
        'rand_max' => $_POST['rand_max'] ?? 100
    ];

    if ($tipo == 'param') {
        $nuevoItem['units'] = $_POST['units'];
        $nuevoItem['alarm_c_high'] = $_POST['alarm_c_high'] ?? 0;
        $nuevoItem['alarm_p_low'] = $_POST['alarm_p_low'] ?? 0;
        $nuevoItem['alarm_p_high'] = $_POST['alarm_p_high'] ?? 0;
    }

    if ($tipo == 'param') {
        $_SESSION['edit_data']['parameters'][$id] = $nuevoItem;
    } else {
        $_SESSION['edit_data']['stock'][$id] = $nuevoItem;
    }

    header("Location: editar_paso1.php");
    exit;
}

header("Location: editar_paso1.php");
?>