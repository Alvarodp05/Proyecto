<?php
// Proyecto/Capa_negocio/Maquinas/gestionar_maquina.php
require_once("../Usuario/clase_usuario.php");
require_once("clases_maquina.php");
session_start();

if (!isset($_SESSION['usuario_activo']) || !$_SESSION['usuario_activo']->esJefe()) {
    header("Location: ../../Capa_usuario/Acceso/Acceso_proyecto_clases.php");
    exit();
}

$accion = $_POST['action'] ?? '';
$id = $_POST['machine_id'] ?? '';

// 1. EDITAR
if ($accion === 'editar' && $id) {
    $maq = Maquina::obtenerPorId($id);
    if ($maq) {
        $params_array = [];
        foreach ($maq->parametros as $p) {
            $key = $p->id_parametro; 
            $params_array[$key] = [
                'label' => $p->nombre,
                'units' => $p->unidades,
                'alarm_c_low' => $p->alarm_c_min, 'alarm_c_high' => $p->alarm_c_max,
                'alarm_p_low' => $p->alarm_p_min, 'alarm_p_high' => $p->alarm_p_max,
                'rand_min' => $p->rand_min, 'rand_max' => $p->rand_max
            ];
        }
        $stock_array = [];
        foreach ($maq->stock as $s) {
            $key = $s->id_stock;
            $stock_array[$key] = [
                'label' => $s->nombre,
                'alarm_c_low' => $s->alarm_c_min,
                'rand_min' => $s->rand_min, 'rand_max' => $s->rand_max
            ];
        }
        $_SESSION['edit_data'] = [
            'machine_id' => $maq->id_maquina,
            'machine_name' => $maq->nombre,
            'description' => $maq->descripcion,
            'parameters' => $params_array,
            'stock' => $stock_array,
            'imagen' => $maq->imagen
        ];
        header("Location: editar_paso1.php");
        exit();
    }
} 

// 2. BORRAR
elseif ($accion === 'borrar' && $id) {
    // Las máquinas opcionales se borran de verdad
    if ($id == 'maquina_0' || $id == 'maquina_7') {
        Maquina::borrarDefinitivamente($id);
    } else {
        Maquina::ocultarEnSesion($id);
    }
    header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php");
    exit();
} 

// 3. SUSTITUIR (Lógica Original de Estados)
elseif ($accion === 'sustituir' && $id) {
    
    // Grupo 1: Si recibimos 1 o 1_1, actuamos sobre la Máquina 1
    if ($id == 'maquina_1' || $id == 'maquina_1_1') {
        $maq = Maquina::obtenerPorId('maquina_1');
        // Alternar: Si es A pasa a B, si es B pasa a A
        $nuevoEstado = ($maq->link == 'ESTADO_B') ? 'ESTADO_A' : 'ESTADO_B';
        Maquina::cambiarEstado('maquina_1', $nuevoEstado);
    }
    
    // Grupo 6: Si recibimos 6 o 6_1, actuamos sobre la Máquina 6
    elseif ($id == 'maquina_6' || $id == 'maquina_6_1') {
        $maq = Maquina::obtenerPorId('maquina_6');
        $nuevoEstado = ($maq->link == 'ESTADO_B') ? 'ESTADO_A' : 'ESTADO_B';
        Maquina::cambiarEstado('maquina_6', $nuevoEstado);
    }

    header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php");
    exit();
}

header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php");
?>