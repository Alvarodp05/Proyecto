<?php
// Proyecto/Capa_negocio/Maquinas/anadir_paso3_add.php
require_once("../Usuario/clase_usuario.php");
session_start();

$tipo = $_GET['tipo'] ?? 'param'; 
$titulo = ($tipo == 'param') ? 'Añadir Parámetro' : 'Añadir Stock';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $titulo; ?></title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <style>
        * { box-sizing: border-box; }
        body { background-color: #7e8a50; padding: 40px 20px; min-height: 100vh; }
        .main-container {
            background-color: #ffffff; padding: 40px; border-radius: 20px;
            max-width: 700px; margin: 0 auto;
        }
        .fila-doble { display: flex; gap: 20px; margin-bottom: 15px; }
        .columna-mitad { flex: 1; }
        .form-item-edit input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        h3 { border-bottom: 2px solid #eee; padding-bottom: 5px; margin-top: 25px; margin-bottom: 15px; color: #3f4b27; font-size: 1.1em; }
    </style>
</head>
<body>
    <div class="main-container">
        
        <div class="content-header">
            <h1><?php echo $titulo; ?></h1>
        </div>
        
        <form action="anadir_procesar.php" method="POST">
            <input type="hidden" name="form_type" value="add_item">
            <input type="hidden" name="tipo_item" value="<?php echo $tipo; ?>">
            
            <div class="section">
                
                <div class="form-item-edit">
                    <label>Identificador (ID único):</label>
                    <input type="text" name="manual_id" required placeholder="Ej: <?php echo ($tipo=='param')?'temp_aceite':'s_aceitunas'; ?>">
                    <small style="color:#666;">Debe ser único.</small>
                </div>

                <div class="form-item-edit" style="margin-top:15px;">
                    <label>Nombre (Etiqueta Visible):</label>
                    <input type="text" name="label" required placeholder="Ej: <?php echo ($tipo=='param')?'Presión':'Aceitunas'; ?>">
                </div>
                
                <?php if ($tipo == 'param'): ?>
                <div class="form-item-edit" style="margin-top:15px;">
                    <label>Unidades:</label>
                    <input type="text" name="units" placeholder="Ej: kg, L, ºC">
                </div>
                <?php endif; ?>

                <hr style="margin: 20px 0;">
                <h3>Configuración de Alarmas Críticas (Rojo)</h3>

                <div class="fila-doble">
                    <div class="columna-mitad">
                        <label>Mínimo:</label>
                        <input type="number" step="0.01" name="alarm_c_low" value="0">
                    </div>
                    <div class="columna-mitad">
                        <label>Máximo:</label>
                        <input type="number" step="0.01" name="alarm_c_high" value="100">
                    </div>
                </div>

                <h3>Configuración de Alarmas Preventivas (Naranja)</h3>
                <div class="fila-doble">
                    <div class="columna-mitad">
                        <label>Mínimo:</label>
                        <input type="number" step="0.01" name="alarm_p_low" value="10">
                    </div>
                    <div class="columna-mitad">
                        <label>Máximo:</label>
                        <input type="number" step="0.01" name="alarm_p_high" value="90">
                    </div>
                </div>

                <hr style="margin: 20px 0;">
                <h3>Simulación (Valores Aleatorios)</h3>
                <div class="fila-doble">
                    <div class="columna-mitad">
                        <label>Generar Mínimo:</label>
                        <input type="number" step="0.01" name="rand_min" value="0">
                    </div>
                    <div class="columna-mitad">
                        <label>Generar Máximo:</label>
                        <input type="number" step="0.01" name="rand_max" value="100">
                    </div>
                </div>

            </div>
            
            <div class="buttons-edit-wizard" style="margin-top:30px;">
                <button type="submit" class="btn-aceptar">Añadir Componente</button>
                <a href="anadir_paso2.php" class="btn-cancelar" style="text-decoration:none; margin-left:10px;">Cancelar</a>
            </div>
        </form>
    </div>
</body>
</html>