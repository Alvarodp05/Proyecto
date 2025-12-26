<?php
// Proyecto/Capa_negocio/Maquinas/anadir_procesar.php
session_start();
require_once("clases_maquina.php");

if (!isset($_SESSION['add_data'])) {
    header("Location: anadir_paso1.php");
    exit;
}

// 1. ACTUALIZAR DATOS TEMPORALES (Si el usuario escribió algo en el dashboard)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // AQUÍ ESTÁ EL CAMBIO: GUARDAR EL NOMBRE TAMBIÉN
    if (isset($_POST['nombre'])) $_SESSION['add_data']['machine_name'] = $_POST['nombre'];
    
    if (isset($_POST['description'])) $_SESSION['add_data']['description'] = $_POST['description'];
    if (isset($_POST['imagen'])) $_SESSION['add_data']['imagen'] = $_POST['imagen'];
}

// =========================================================
// CASO A: GUARDAR FINAL (INSERT en Base de Datos)
// =========================================================
if (isset($_POST['accion_guardar'])) {
    $data = $_SESSION['add_data'];
    
    // Crear objeto
    $maquina = new Maquina();
    $maquina->id_maquina = $data['machine_id'];
    $maquina->nombre = $data['machine_name'];
    $maquina->descripcion = $data['description'];
    $maquina->imagen = $data['imagen'];
    $maquina->orden = $data['orden']; 
    
    // INSERTAR
    $maquina->guardar(); 
    
    // INSERTAR COMPONENTES
    Maquina::guardarDesdeSesion($data);
    
    unset($_SESSION['add_data']);
    header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php");
    exit;
}

// =========================================================
// CASO B: NAVEGACIÓN
// =========================================================
elseif (isset($_POST['accion_siguiente'])) {
    $p_action = $_POST['param_action'];
    $s_action = $_POST['stock_action'];

    if (!empty($p_action) && !empty($s_action)) {
        $_SESSION['flash_error'] = "Error: Elige solo una acción a la vez.";
        header("Location: anadir_paso2.php");
        exit;
    }

    if ($p_action == 'add') header("Location: anadir_paso3_add.php?tipo=param");
    elseif ($p_action == 'edit') header("Location: anadir_paso3_edit.php?tipo=param");
    elseif ($p_action == 'delete') header("Location: anadir_paso3_del.php?tipo=param");
    elseif ($s_action == 'add_stock') header("Location: anadir_paso3_add.php?tipo=stock");
    elseif ($s_action == 'edit_stock') header("Location: anadir_paso3_edit.php?tipo=stock");
    elseif ($s_action == 'delete_stock') header("Location: anadir_paso3_del.php?tipo=stock");
    else {
        $_SESSION['flash_error'] = "Selecciona una acción.";
        header("Location: anadir_paso2.php");
    }
    exit;
}

// =========================================================
// CASO C: PROCESAR FORMULARIOS (ADD/EDIT ITEM)
// =========================================================
if (isset($_POST['form_type'])) {
    
    $tipo = $_POST['tipo_item']; 
    $accion_form = $_POST['form_type']; 
    
    // C.1 AÑADIR O EDITAR ITEM
    if ($accion_form == 'add_item' || $accion_form == 'edit_item') {
        
        if ($accion_form == 'add_item') {
            $id = $_POST['manual_id'] ?? uniqid();
        } else {
            $id = $_POST['item_id'];
        }

        $item = [
            'label' => $_POST['label'],
            'units' => $_POST['units'] ?? '',
            'alarm_c_low' => $_POST['alarm_c_low'] ?? 0,
            'alarm_c_high' => $_POST['alarm_c_high'] ?? 0,
            'alarm_p_low' => $_POST['alarm_p_low'] ?? 0,
            'alarm_p_high' => $_POST['alarm_p_high'] ?? 0,
            'rand_min' => $_POST['rand_min'] ?? 0,
            'rand_max' => $_POST['rand_max'] ?? 100
        ];
        
        if ($tipo == 'param') {
            $_SESSION['add_data']['parameters'][$id] = $item;
        } else {
            $_SESSION['add_data']['stock'][$id] = $item;
        }
    }
    
    // C.2 BORRAR
    elseif ($accion_form == 'delete_items' && isset($_POST['items_a_borrar'])) {
        foreach ($_POST['items_a_borrar'] as $id_borrar) {
            if ($tipo == 'param') {
                unset($_SESSION['add_data']['parameters'][$id_borrar]);
            } else {
                unset($_SESSION['add_data']['stock'][$id_borrar]);
            }
        }
    }

    header("Location: anadir_paso2.php");
    exit;
}

header("Location: anadir_paso2.php");
?>