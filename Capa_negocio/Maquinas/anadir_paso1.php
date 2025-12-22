<?php
session_start();
include_once ("../../Lib/BD/AccesoBD.php");

// --- COMPROBACIÓN DE SLOTS VÍA BASE DE DATOS ---
$bd = new AccesoBD();

// 1. Comprobar Slot 0
$data_0 = $bd->obtenerDatosMaquina('maquina_0');
$slot_0_lleno = false;
// Si existe y su nombre no es el del slot vacío, está ocupado
if ($data_0 && $data_0['machine_name'] != 'SLOT VACÍO 0') {
    $slot_0_lleno = true;
}

// 2. Comprobar Slot 7
$data_7 = $bd->obtenerDatosMaquina('maquina_7');
$slot_7_lleno = false;
if ($data_7 && $data_7['machine_name'] != 'SLOT VACÍO 7') {
    $slot_7_lleno = true;
}

// 3. Si AMBOS están llenos, error y fuera
if ($slot_0_lleno && $slot_7_lleno) {
    $_SESSION['flash_error'] = "Límite de máquinas alcanzado. No se pueden añadir más máquinas.";
    header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
    exit;
}
// --- FIN COMPROBACIÓN ---

// Limpieza
unset($_SESSION['anadir_data']);
unset($_SESSION['flash_error']); 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Máquina - Paso 1</title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <link rel="stylesheet" href="../../Lib/Estilos/estilo_planta.css">
    <style>
        .option-box input[type="radio"] { display: none; }
        .option-box label {
            display: block; padding: 20px; border: 2px dashed #ccc;
            border-radius: 10px; margin-bottom: 20px; font-size: 1.2em;
            font-weight: bold; color: #4e5930; cursor: pointer;
            transition: all 0.2s ease-in-out;
        }
        .option-box label:hover { background-color: #f9f9f9; border-color: #7e8a50; }
        .option-box input[type="radio"]:checked + label {
            background-color: #d4edda; border-color: #28a745; color: #155724;
            box-shadow: 0 0 10px rgba(40, 167, 69, 0.2);
        }
        .option-box label.disabled {
            background-color: #f9f9f9; border-color: #eee; color: #aaa; cursor: not-allowed;
        }
        .buttons-full-width {
            display: flex; justify-content: space-between; align-items: center; margin-top: 20px;
        }
        .buttons-full-width .btn-cancelar {
            padding: 10px 30px; border-radius: 8px; font-weight: bold; font-size: 1em;
            text-decoration: none; line-height: 1.5; border: 2px solid #a94442;
            box-sizing: border-box; color: white;
        }
    </style>
</head>
<body>
    <div class="main-container" style="display:block; max-width: 600px;">
        <form action="anadir_paso2.php" method="POST">
            <div class="content-header">
                <h1>Añadir Nueva Máquina</h1>
            </div>
            <p style="text-align: left; color: #4e5930; margin-bottom: 25px;">Selecciona dónde quieres añadir la nueva máquina en la planta:</p>

            <div class="section">
                
                <div class="option-box">
                    <input type="radio" id="pos_inicio" name="posicion" value="inicio" 
                        <?php if ($slot_0_lleno) echo 'disabled'; ?>
                        <?php if (!$slot_0_lleno) echo 'checked'; ?>
                    >
                    <label for="pos_inicio" class="<?php if ($slot_0_lleno) echo 'disabled'; ?>">
                        Añadir al Principio (Slot 0)
                        <?php if ($slot_0_lleno) echo '<br><small style="color:red;">(Este slot ya está en uso)</small>'; ?>
                    </label>
                </div>
                
                <div class="option-box">
                    <input type="radio" id="pos_final" name="posicion" value="final"
                        <?php if ($slot_7_lleno) echo 'disabled'; ?>
                        <?php if ($slot_0_lleno && !$slot_7_lleno) echo 'checked'; ?>
                    >
                    <label for="pos_final" class="<?php if ($slot_7_lleno) echo 'disabled'; ?>">
                        Añadir al Final (Slot 7)
                        <?php if ($slot_7_lleno) echo '<br><small style="color:red;">(Este slot ya está en uso)</small>'; ?>
                    </label>
                </div>

            </div>

            <div class="buttons-full-width">
                <a href="../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php" class="btn-cancelar">Cancelar</a>
                <button type="submit">Siguiente</button>
            </div>
        </form>
    </div>
</body>
</html>