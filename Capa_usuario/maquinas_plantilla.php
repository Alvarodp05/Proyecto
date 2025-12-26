<?php
// Proyecto/Capa_usuario/Maquinas/maquinas_plantilla.php

// 1. CARGA DE CLASES E INICIO DE SESIÓN
require_once("../../Capa_negocio/Usuario/clase_usuario.php");
require_once("../../Capa_negocio/Maquinas/clases_maquina.php");

session_start();

// 2. SEGURIDAD DE ACCESO
if (!isset($_SESSION['usuario_activo'])) {
    header("Location: ../Acceso/Acceso_proyecto_clases.php");
    exit();
}
/** @var Usuario $usuario */
$usuario = $_SESSION['usuario_activo'];

// --> Misma comprobación de seguridad que en la planta
if (!is_object($usuario) || !method_exists($usuario, 'esJefe')) {
    session_destroy();
    header("Location: ../Acceso/Acceso_proyecto_clases.php");
    exit();
}
$esJefe = $usuario->esJefe();

// 3. OBTENCIÓN DEL ID DE LA MÁQUINA
// --> $_GET['id_maquina'] viene de la URL (ej: maquinas_plantilla.php?id_maquina=maquina_1)
// --> Si no viene nada, usamos 'maquina_1' por defecto para que no falle.

$id_maquina = isset($_GET['id']) ? $_GET['id'] : null;

if (!$id_maquina) die("Error: No se ha especificado una máquina.");

// 4. CARGA DE DATOS DESDE BD
// --> Maquina::obtenerPorId hace:
// --> 1. SELECT a la tabla 'maquinas'.
// --> 2. SELECT a la tabla 'parametros' y lo guarda en $maquina->parametros.
// --> 3. SELECT a la tabla 'stock' y lo guarda en $maquina->stock.

$maquina = Maquina::obtenerPorId($id_maquina);
if (!$maquina) die("Error: Máquina no encontrada.");

// --- LÓGICA DE FLUJO ---

// 1. PASO A: Generar aleatorios nuevos para TODO
// (Esto ocurre siempre, asegurando que al refrescar todo cambia)
$maquina->simularValores();

// 2. PASO B: Si hemos pulsado "Guardar", aplicamos los manuales ENCIMA de los aleatorios
if ($esJefe && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion_manual'])) {
    $params_manuales = $_POST['p'] ?? [];
    $stock_manual = $_POST['s'] ?? [];
    
    // Esta función solo actualiza la BD si el input NO está vacío
    $maquina->actualizarValoresManuales($params_manuales, $stock_manual);
}

// 3. PASO C: Recargar los datos finales para mostrar
$maquina = Maquina::obtenerPorId($id_maquina);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $esJefe ? 'JEFE' : 'OPERARIO'; ?> - <?php echo htmlspecialchars($maquina->nombre); ?></title>
    <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas.css">
    <?php if($esJefe): ?>
        <link rel="stylesheet" href="../../Lib/Estilos/Estilo_maquinas_jefe.css">
    <?php endif; ?>
    
    <style>
        .maquina-imagen-container img { max-width: 100%; height: auto; border-radius: 8px; }
        .tabla-datos { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .tabla-datos th, .tabla-datos td { border: 1px solid #ddd; padding: 10px; text-align: center; vertical-align: middle; }
        .tabla-datos th { background-color: #f2f2f2; }
        
        /* ESTILO INPUT PLACEHOLDER */
        .input-tabla {
            width: 90%;
            padding: 5px;
            text-align: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
            color: #333;
        }
        .input-tabla:focus { border-color: #007bff; outline: none; background-color: #f0f8ff; }
        
        /* Hacemos que el placeholder se vea oscuro, como si fuera el valor real */
        .input-tabla::placeholder {
            color: #555;
            opacity: 1; /* Firefox */
        }
        
        .btn-guardar-manual {
            background-color: #28a745; color: white; border: none; padding: 10px 20px;
            border-radius: 5px; cursor: pointer; font-size: 1em; margin-top: 15px;
        }
        .btn-guardar-manual:hover { background-color: #218838; }

        /* Cajas de Alarmas */
        .box-alarm { padding: 15px; border-radius: 8px; margin-bottom: 15px; border: 1px solid transparent; }
        .box-alarm ul { margin: 5px 0 0 0; padding-left: 20px; }
        .alarm-correctiva { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
        .alarm-preventiva { background-color: #fff3cd; color: #856404; border-color: #ffeeba; }
        .alarm-stock { background-color: #d1ecf1; color: #0c5460; border-color: #bee5eb; }
        .alarm-ok { background-color: #d4edda; color: #155724; border-color: #c3e6cb; text-align: center; }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="image-section">
            <?php
            // 1. PREPARACIÓN DE LISTAS
            // Creamos tres arrays vacíos donde iremos guardando los nombres de los parámetros/stock fuera de rango
            $lista_correctivas = []; // Para errores graves (ROJO)
            $lista_preventivas = []; // Para advertencias (NARANJA)
            $lista_stock = [];       // Para falta de material (AZUL)

            // 2. ANÁLISIS DE PARÁMETROS TÉCNICOS
            // Recorremos todos los sensores de la máquina
            foreach ($maquina->parametros as $p) {
                
                // Si el sensor no tiene dato (es null), saltamos al siguiente para evitar errores.
                if ($p->valor_actual === null) continue;

                // COMPROBACIÓN CRÍTICA (Correctiva)
                // Si el valor está POR DEBAJO del mínimo crítico O POR ENCIMA del máximo crítico.
                if ($p->valor_actual < $p->alarm_c_min || $p->valor_actual > $p->alarm_c_max) {
                    // Guardamos el nombre del parámetro en negrita en la lista de errores graves.
                    $lista_correctivas[] = "<strong>{$p->nombre}</strong>";
                
                // COMPROBACIÓN PREVENTIVA (Advertencia)
                // Si no es crítico, miramos si está fuera del rango "ideal" (preventivo).
                } elseif ($p->valor_actual < $p->alarm_p_min || $p->valor_actual > $p->alarm_p_max) {
                    // Guardamos el nombre en la lista de advertencias.
                    $lista_preventivas[] = "<strong>{$p->nombre}</strong>";
                }
            }

            // 3. ANÁLISIS DE STOCK
            // Recorremos el stock
            foreach ($maquina->stock as $s) {
                if ($s->valor_actual === null) continue;

                // Si la cantidad actual es MENOR que el mínimo permitido.
                if ($s->valor_actual < $s->alarm_c_min) {
                    $lista_stock[] = "<strong>{$s->nombre}</strong>";
                }
            }

            // 4. VISUALIZACIÓN (PINTAR EL HTML)
            // Usamos esta variable 'bandera' para saber al final si todo estaba bien.
            $hay_alarmas = false;

            // A) PINTAR CAJA ROJA (Correctivas)
            // Si la lista NO está vacía, significa que hay errores graves.
            if (!empty($lista_correctivas)) {
                $hay_alarmas = true; // Marcamos que hay problemas
                // Imprimimos el div con la clase CSS 'alarm-correctiva' (Rojo)
                echo "<div class='box-alarm alarm-correctiva'><strong>ALARMAS CORRECTIVAS</strong><ul>";
                // Hacemos un bucle para escribir cada error como un elemento de lista (<li>)
                foreach ($lista_correctivas as $msg) echo "<li>$msg</li>";
                echo "</ul></div>";
            }

            // B) PINTAR CAJA NARANJA (Preventivas)
            if (!empty($lista_preventivas)) {
                $hay_alarmas = true;
                // Imprimimos el div con la clase CSS 'alarm-preventiva' (Naranja)
                echo "<div class='box-alarm alarm-preventiva'><strong>ALARMAS PREVENTIVAS</strong><ul>";
                foreach ($lista_preventivas as $msg) echo "<li>$msg</li>";
                echo "</ul></div>";
            }

            // C) PINTAR CAJA DE STOCK (Aviso de material)
            if (!empty($lista_stock)) {
                $hay_alarmas = true;
                // Imprimimos el div con la clase CSS 'alarm-stock'
                echo "<div class='box-alarm alarm-stock'><strong>ALERTA DE STOCK BAJO</strong><ul>";
                foreach ($lista_stock as $msg) echo "<li>$msg</li>";
                echo "</ul></div>";
            }

            // D) PINTAR CAJA VERDE (Todo OK)
            // Si después de revisar todo, la bandera sigue siendo 'false', es que no hubo ningún fallo.
            if (!$hay_alarmas) {
                echo "<div class='box-alarm alarm-ok'><strong>SISTEMA FUNCIONANDO CORRECTAMENTE</strong></div>";
            }
            ?>
            
            <div class="maquina-imagen-container">
                <img src="<?php echo htmlspecialchars($maquina->imagen); ?>" alt="Imagen">
            </div>
            
            <div style="text-align:center; margin-top:20px;">
                <a href="../Planta_produccion/plantaproduccion_plantilla.php" class="btn-cancelar" style="background:#6c757d; color:white; padding:10px 20px; text-decoration:none; border-radius:5px; display:inline-block;">Volver a Planta</a>
            </div>
        </div>

        <div class="content-section">
            <div class="content-header" style="display:flex; justify-content:space-between;">
                <h1><?php echo htmlspecialchars($maquina->nombre); ?></h1>
                <img src="../../Lib/Imagenes/logo_esquina.jpg" alt="Logo" style="width:80px;">
            </div> 
            
            <div class="section descripcion">
                <h2>Funcionamiento</h2>
                <p><?php echo nl2br(htmlspecialchars($maquina->descripcion)); ?></p>
            </div>

            <form action="" method="POST">
                <input type="hidden" name="accion_manual" value="1">
                
                <div class="tablas-container">
                    
                    <div class="tabla-grupo">
                        <h2>Parámetros</h2>
                        <?php if(empty($maquina->parametros)): ?>
                            <p>No hay parámetros.</p>
                        <?php else: ?>
                            <table class="tabla-datos">
                                <thead>
                                    <tr>
                                        <th>Parámetro</th>
                                        <th>Valor Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($maquina->parametros as $p): ?>
                                    <tr>
                                        <td style="text-align:left;">
                                            <strong><?php echo htmlspecialchars($p->nombre); ?></strong>
                                            <small>(<?php echo $p->unidades; ?>)</small>
                                        </td>
                                        <td>
                                            <?php if($esJefe): ?>
                                                <input type="text" 
                                                       name="p[<?php echo $p->id; ?>]" 
                                                       value="" 
                                                       placeholder="<?php echo ($p->valor_actual !== null) ? $p->valor_actual : '-'; ?>" 
                                                       class="input-tabla">
                                            <?php else: ?>
                                                <?php echo ($p->valor_actual !== null) ? $p->valor_actual : '-'; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>

                    <div class="tabla-grupo" style="margin-top:20px;">
                        <h2>Stock de Seguridad</h2>
                        <?php if(empty($maquina->stock)): ?>
                            <p>No hay stock.</p>
                        <?php else: ?>
                            <table class="tabla-datos">
                                <thead>
                                    <tr>
                                        <th>Pieza</th>
                                        <th>Stock Actual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($maquina->stock as $s): ?>
                                    <tr>
                                        <td style="text-align:left;"><strong><?php echo htmlspecialchars($s->nombre); ?></strong></td>
                                        <td>
                                            <?php if($esJefe): ?>
                                                <input type="text" 
                                                       name="s[<?php echo $s->id; ?>]" 
                                                       value="" 
                                                       placeholder="<?php echo ($s->valor_actual !== null) ? $s->valor_actual : '-'; ?>" 
                                                       class="input-tabla">
                                            <?php else: ?>
                                                <?php echo ($s->valor_actual !== null) ? $s->valor_actual : '-'; ?>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                    
                    <?php if($esJefe): ?>
                        <div style="text-align:right;">
                            <button type="submit" class="btn-guardar-manual">Guardar Cambios</button>
                        </div>
                    <?php endif; ?>
                    
                </div>
            </form>
        </div> 
    </div> 
</body>
</html>