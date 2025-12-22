<?php
session_start();
include_once ("../../Lib/BD/AccesoBD.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {

    $action = $_POST['action'];
    $machine_id = $_POST['machine_id'];
    $bd = new AccesoBD();

    if ($action == 'editar') {
        // --- INICIAR EDICIÓN ---
        // Carga los datos de la máquina que le llegue (sea 1 o 1_1)
        $data = $bd->obtenerDatosMaquina($machine_id);
        if ($data) {
            $_SESSION['edit_data'] = $data;
            header("Location: editar_paso1.php");
            exit;
        } else {
            die("Error: No se pudieron cargar los datos.");
        }
        
    } elseif ($action == 'borrar') {
        // ... (Tu lógica de borrado aquí, no cambia) ...
        if ($machine_id == 'maquina_0' || $machine_id == 'maquina_7') {
            $bd->resetearMaquina($machine_id, ($machine_id == 'maquina_0') ? 'SLOT VACÍO 0' : 'SLOT VACÍO 7');
        } else {
            if (!isset($_SESSION['maquinas_borradas'])) $_SESSION['maquinas_borradas'] = [];
            if (!in_array($machine_id, $_SESSION['maquinas_borradas'])) $_SESSION['maquinas_borradas'][] = $machine_id;
        }
        header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
        exit;

    } elseif ($action == 'sustituir') {
        // --- LÓGICA DE SUSTITUCIÓN ---
        
        // Si nos llega 'maquina_1_1' (porque estamos en vista B), 
        // sabemos que el interruptor está en 'maquina_1'.
        $id_interruptor = $machine_id;
        if ($machine_id == 'maquina_1_1') {
            $id_interruptor = 'maquina_1';
        }
		// Si estamos en la vista B (6_1), el interruptor está en la 6
        if ($machine_id == 'maquina_6_1') {
            $id_interruptor = 'maquina_6';
        }

        // Leemos el estado del interruptor
        $data = $bd->obtenerDatosMaquina($id_interruptor);
        $estado_actual = $data['link'];
        
        // Cambiamos estado
        $nuevo_estado = ($estado_actual == 'ESTADO_A') ? 'ESTADO_B' : 'ESTADO_A';
        
        $bd->actualizarEstadoMaquina($id_interruptor, $nuevo_estado);
		
		header("Location:../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
        exit;
    }
}
header("Location:../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
exit;
?>