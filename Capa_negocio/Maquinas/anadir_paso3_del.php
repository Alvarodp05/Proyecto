<?php
session_start();

// 1. Determinamos qué estamos borrando
$tipo = $_GET['tipo'] ?? 'param';
$es_param = ($tipo == 'param');
$seccion_sesion = $es_param ? 'parameters' : 'stock';

// 2. Seguridad
if (!isset($_SESSION['anadir_data']) || !isset($_SESSION['anadir_data'][$seccion_sesion])) {
    header("Location: anadir_paso1.php");
    exit;
}

// 3. Comprobar si el formulario se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['params_a_borrar'])) {
        foreach ($_POST['params_a_borrar'] as $key_a_borrar) {
            // 4. Eliminar el item de la sesión 'anadir_data'
           unset($_SESSION['anadir_data'][$seccion_sesion][$key_a_borrar]);
        }
    }

    // 5. Redirigir de vuelta al Paso 2
    header("Location: anadir_paso2.php");
    exit;
}

// Cargamos la lista (Parámetros o Stock)
$lista_actual = $_SESSION['anadir_data'][$seccion_sesion];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Borrar <?php echo $es_param ? "Parámetros" : "Stock"; ?></title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <style>
        .delete-list { background: #fff; padding: 20px; border-radius: 8px; }
        .delete-item { display: block; margin-bottom: 10px; font-size: 1.1em; }
        .delete-item input { margin-right: 10px; }
    </style>
</head>
<body>
    <div class="main-container" style="display:block; max-width: 800px;">
        <form action="anadir_paso3_del.php?tipo=<?php echo $tipo; ?>" method="POST">
            <div class="content-header">
                <h1>Borrar <?php echo $es_param ? "Parámetros" : "Stock"; ?></h1>
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
                <a href="anadir_paso2.php" class="btn-cancelar-link">Volver (Cancelar)</a>
            </div>
        </form>
    </div>
</body>
</html>