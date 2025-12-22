<?php
session_start();
include_once ("../../Lib/BD/AccesoBD.php");

// Seguridad: Si no hay datos en sesión, fuera.
if (!isset($_SESSION['anadir_data'])) {
    header("Location: anadir_paso1.php");
    exit;
}

// 1. Guardar cambios temporales en la sesión (si se navega entre pasos)
if (isset($_POST['machine_id']))   $_SESSION['anadir_data']['machine_id'] = $_POST['machine_id'];
if (isset($_POST['machine_name'])) $_SESSION['anadir_data']['machine_name'] = $_POST['machine_name'];
if (isset($_POST['link']))         $_SESSION['anadir_data']['link'] = $_POST['link'];
if (isset($_POST['imagen']))       $_SESSION['anadir_data']['imagen'] = $_POST['imagen'];
if (isset($_POST['description']))  $_SESSION['anadir_data']['description'] = $_POST['description'];


// 2. GESTIÓN DE NAVEGACIÓN (Siguiente / Borrar / Cancelar)
// (Esta parte es idéntica a la que tenías, solo gestiona el flujo)

if (isset($_POST['accion_siguiente'])) {
    $param_accion = $_POST['param_action'] ?? '';
    $stock_accion = $_POST['stock_action'] ?? '';

    if ($param_accion != '' && $stock_accion != '') {
        $_SESSION['flash_error'] = "Error: No puedes seleccionar ambas acciones.";
        header("Location: anadir_paso2.php");
        exit;
    }
    if ($param_accion != '') {
        // Redirige a añadir/editar/borrar parámetro
        header("Location: anadir_paso3_" . ($param_accion == 'add' ? 'add' : ($param_accion == 'edit' ? 'edit' : 'del')) . ".php?tipo=param");
        exit;
    }
    if ($stock_accion != '') {
        // Redirige a añadir/editar/borrar stock
        header("Location: anadir_paso3_" . ($stock_accion == 'add_stock' ? 'add' : ($stock_accion == 'edit_stock' ? 'edit' : 'del')) . ".php?tipo=stock");
        exit;
    }
    header("Location: anadir_paso2.php");
    exit;
}

if (isset($_POST['accion_borrar'])) {
    unset($_SESSION['anadir_data']);
    header("Location: anadir_paso2.php");
    exit;
}


// --- 3. GUARDADO FINAL EN BASE DE DATOS ---
if (isset($_POST['accion_guardar'])) {
    
    $data = $_SESSION['anadir_data'];
    $id_maquina = $data['machine_id']; // Será 'maquina_0' o 'maquina_7'
    
    $bd = new AccesoBD();
    
    // A. Guardar Info General (Tabla 'maquinas')
    // Determinamos el orden fijo para los slots
    $orden = ($id_maquina == 'maquina_0') ? 0 : 7;
    
    $bd->guardarInfoGeneral(
        $id_maquina, 
        $data['machine_name'], 
        $data['description'], 
        $data['imagen'], 
        $data['link'], 
        $orden
    );

    // B. Guardar Estructura (Parametros y Stock)
    
    // 1. Limpieza previa: Borramos lo que hubiera en ese slot en la BD
    $bd->limpiarDetallesMaquina($id_maquina);

    // 2. Insertar Parámetros desde la sesión
    if (!empty($data['parameters'])) {
        foreach ($data['parameters'] as $key => $p) {
            // AccesoBD se encarga de convertir las comas a puntos decimales
            $bd->insertarParametro(
                $id_maquina, 
                $key, 
                $p['label'], 
                $p['units'], 
                $p['alarm_c_low'], $p['alarm_c_high'], 
                $p['alarm_p_low'], $p['alarm_p_high'], 
                $p['rand_min'], $p['rand_max']
            );
        }
    }

    // 3. Insertar Stock desde la sesión
    if (!empty($data['stock'])) {
        foreach ($data['stock'] as $key => $s) {
            $bd->insertarStock(
                $id_maquina, 
                $key, 
                $s['label'], 
                $s['alarm_c_low'], $s['alarm_c_high'], 
                $s['alarm_p_low'], $s['alarm_p_high'], 
                $s['rand_min'], $s['rand_max']
            );
        }
    }

    // C. Limpieza Final y Redirección
    unset($_SESSION['anadir_data']);
    unset($_SESSION['anadir_data_file_path']);
    unset($_SESSION['anadir_posicion']);

    header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
    exit;
}

// Fallback por si se carga el archivo directamente
header("Location: anadir_paso2.php");
exit;
?>