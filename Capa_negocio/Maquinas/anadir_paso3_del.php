<?php
// Proyecto/Capa_negocio/Maquinas/anadir_paso3_del.php
require_once("../Usuario/clase_usuario.php");
session_start();
$tipo = $_GET['tipo'] ?? 'param';
$lista = ($tipo == 'param') ? $_SESSION['add_data']['parameters'] : $_SESSION['add_data']['stock'];

if (empty($lista)) {
    $_SESSION['flash_error'] = "No hay nada que borrar.";
    header("Location: anadir_paso2.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Borrar Elementos</title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <style>
        .delete-item { display: block; padding: 10px; border-bottom: 1px solid #eee; cursor: pointer; }
        .delete-item:hover { background: #fee; }
    </style>
</head>
<body>
    <div class="main-container" style="max-width: 600px;">
        <div class="content-header"><h1>Borrar <?php echo ucfirst($tipo); ?></h1></div>
        
        <form action="anadir_procesar.php" method="POST">
            <input type="hidden" name="form_type" value="delete_items">
            <input type="hidden" name="tipo_item" value="<?php echo $tipo; ?>">
            
            <div class="section">
                <p>Selecciona lo que quieras eliminar:</p>
                <?php foreach ($lista as $key => $element): ?>
                    <label class="delete-item">
                        <input type="checkbox" name="items_a_borrar[]" value="<?php echo $key; ?>">
                        <?php echo htmlspecialchars($element['label']); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <div class="buttons-edit-wizard">
                <a href="anadir_paso2.php" class="btn-cancelar-link">Cancelar</a>
                <button type="submit" class="btn-cancelar">Eliminar Seleccionados</button>
            </div>
        </form>
    </div>
</body>
</html>