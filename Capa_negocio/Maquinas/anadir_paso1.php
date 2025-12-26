<?php
// Proyecto/Capa_negocio/Maquinas/anadir_paso1.php
require_once("../Usuario/clase_usuario.php");
require_once("clases_maquina.php");
session_start();

if (!isset($_SESSION['usuario_activo']) || !$_SESSION['usuario_activo']->esJefe()) {
    header("Location: ../../Capa_usuario/Acceso/Acceso_proyecto_clases.php");
    exit();
}

// Limpiamos datos antiguos si entramos de nuevas
if (!isset($_GET['volver'])) {
    unset($_SESSION['add_data']);
}

// Comprobamos disponibilidad real en BD
$m0 = Maquina::obtenerPorId('maquina_0');
$m7 = Maquina::obtenerPorId('maquina_7');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir Máquina - Paso 1</title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_Proyecto.css">
    <style>
        .btn-opcion-grande {
            display: block; width: 100%; padding: 20px;
            margin-bottom: 15px; font-size: 1.2em; font-weight: bold;
            text-align: left; background: #fff; border: 2px solid #ddd;
            border-radius: 8px; cursor: pointer; transition: all 0.3s;
            color: #333;
        }
        .btn-opcion-grande:hover {
            border-color: #3f4b27; background: #f1f8e9; transform: translateX(5px);
        }
        .badge-exist {
            float: right; font-size: 0.8em; background: #eee; 
            padding: 5px 10px; border-radius: 4px; color: #777;
        }
    </style>
</head>
<body>
    <div class="login-container" style="width: 500px; margin-top:50px;">
        <div class="logo">
            <img src="../../Lib/Imagenes/logo_esquina.jpg" alt="Logo">
            <h2>Añadir Máquina (Paso 1/3)</h2>
        </div>
        
        <p style="text-align:center; color:#666; margin-bottom:20px;">
            Selecciona el hueco a habilitar:
        </p>

        <?php if (!$m0): ?>
            <form action="anadir_paso2.php" method="POST">
                <input type="hidden" name="id_maquina" value="maquina_0">
                <input type="hidden" name="nombre" value="Recepción">
                <button type="submit" class="btn-opcion-grande">
                    ➕ Añadir Máquina 0 (Recepción)
                </button>
            </form>
        <?php else: ?>
            <div class="btn-opcion-grande" style="opacity:0.6; cursor:default;">
                ✅ Máquina 0 (Recepción)
                <span class="badge-exist">Ya existe</span>
            </div>
        <?php endif; ?>

        <?php if (!$m7): ?>
            <form action="anadir_paso2.php" method="POST">
                <input type="hidden" name="id_maquina" value="maquina_7">
                <input type="hidden" name="nombre" value="Salida">
                <button type="submit" class="btn-opcion-grande">
                    ➕ Añadir Máquina 7 (Salida)
                </button>
            </form>
        <?php else: ?>
            <div class="btn-opcion-grande" style="opacity:0.6; cursor:default;">
                ✅ Máquina 7 (Salida)
                <span class="badge-exist">Ya existe</span>
            </div>
        <?php endif; ?>

        <div class="buttons" style="margin-top:30px; border-top:1px solid #eee; padding-top:15px;">
            <a href="../../Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php">
                <button type="button" class="btn-cancelar">Cancelar</button>
            </a>
        </div>
    </div>
</body>
</html>