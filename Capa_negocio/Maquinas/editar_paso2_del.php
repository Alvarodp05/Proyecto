<?php
session_start();

// --- ¡NUEVO! Determinamos qué estamos borrando ---
$tipo = $_GET['tipo'] ?? 'param';
$es_param = ($tipo == 'param');
$seccion_sesion = $es_param ? 'parameters' : 'stock';

// 1. Seguridad: Si no hay datos de edición, redirigir.
if (!isset($_SESSION['edit_data']) || !isset($_SESSION['edit_data']['parameters'])) {
    header("Location:../../Capa_usuario/Planta_produccion/plantaproduccion_jefe.php");
    exit;
}

// 2. Comprobar si el formulario se ha enviado (a sí mismo)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Comprobar si se ha seleccionado algún parámetro para borrar
    if (isset($_POST['params_a_borrar'])) {
        
        // $_POST['params_a_borrar'] es un array con las "keys" de los parámetros
        // que seleccionaste (ej: ['caudal_agua', 'potencia_bomba'])
        
        foreach ($_POST['params_a_borrar'] as $key_a_borrar) {
            
            // 4. Eliminar el parámetro del array de la sesión
            // 'unset' destruye la variable o el índice del array
           unset($_SESSION['edit_data'][$seccion_sesion][$key_a_borrar]);
        }
        }
		
		// 5. Redirigir de vuelta al Paso 1
    header("Location: editar_paso1.php");
    exit;
    }

    

// Cargamos la lista correcta (Parámetros o Stock)
$lista_actual = $_SESSION['edit_data'][$seccion_sesion];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Borrar Parámetros</title>
    <link rel="stylesheet" href="../../Lib/estilos/Estilo_maquinas_jefe.css">
    <style>
        /* Estilo simple para la lista de checkboxes */
        .delete-list { background: #fff; padding: 20px; border-radius: 8px; }
        .delete-item { display: block; margin-bottom: 10px; font-size: 1.1em; }
        .delete-item input { margin-right: 10px; }
    </style>
</head>
<body>
    <div class="main-container" style="display:block; max-width: 800px;">
        <form action="editar_paso2_del.php?tipo=<?php echo $tipo; ?>" method="POST">
            <div class="content-header">
   
                <h1>Borrar <?php echo $es_param ? "Parámetros" : "Stock"; ?> Existentes</h1>
            </div>

            <div class="section delete-list">
                <?php if (empty($lista_actual)): ?>
                    <p>No hay ítems para borrar.</p>
                <?php else: ?>
				<p>Selecciona los ítems que deseas eliminar:</p>
                    <?php foreach ($lista_actual as $key => $config): ?>
                
                    
                        <label class="delete-item">
                            <input type="checkbox" name="params_a_borrar[]" value="<?php echo htmlspecialchars($key); ?>">
                            <?php echo htmlspecialchars($config['label']); ?> (ID: <?php echo htmlspecialchars($key); ?>)
                        </label>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>

            <div class="buttons-edit-wizard">
                <button type="submit" class="btn-cancelar">Borrar Seleccionados</button>
                <a href="editar_paso1.php" class="btn-cancelar-link">Volver (Cancelar)</a>
            </div>

        </form>
    </div>
</body>
</html>