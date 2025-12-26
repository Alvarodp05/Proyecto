<?php
// Proyecto/Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php

// 1. CARGA DE CLASES (¡OBLIGATORIO ANTES DE SESSION_START!)
// --> Necesitamos cargar las "plantillas" (Clases) antes de abrir la sesión.
// --> Si PHP encuentra un objeto 'Usuario' en la sesión pero no conoce la clase, dará error.
require_once("../../Capa_negocio/Usuario/clase_usuario.php");
require_once("../../Capa_negocio/Maquinas/clases_maquina.php");

// 2. INICIAR SESIÓN
// --> Recupera la memoria del servidor asociada a este visitante.
session_start();

// 3. SEGURIDAD BÁSICA
// --> Si no hay variable 'usuario_activo' (!isset), es que nadie se ha logueado. Fuera.
if (!isset($_SESSION['usuario_activo'])) {
    header("Location: ../Acceso/Acceso_proyecto_clases.php");
    exit();
}

/** @var Usuario $usuario */
// --> Recuperamos el objeto guardado. PHP intenta reconstruirlo usando clase_usuario.php
$usuario = $_SESSION['usuario_activo'];

// Verificación anti-error de objeto incompleto
/* EXPLICACIÓN DETALLADA DE LA CONDICIÓN DE SEGURIDAD:
   if (!is_object($usuario) || !method_exists($usuario, 'esJefe')) ...
   
   1. !is_object($usuario):
      A veces, si la sesión se corrompe o la clase no se cargó bien antes del session_start,
      PHP devuelve algo llamado "__PHP_Incomplete_Class" que no es un objeto funcional.
      Esta comprobación asegura que $usuario es un objeto real y válido.
      
   2. !method_exists($usuario, 'esJefe'):
      Aunque sea un objeto, ¿es de la versión correcta?
      Si cambiaste el código de la clase Usuario y borraste la función 'esJefe',
      llamarla provocaría un "Fatal Error" y la web se caería.
      Esta función pregunta: "¿Tienes disponible la función 'esJefe' dentro de ti?".
      
   CONCLUSIÓN: Si no es un objeto o le falta la función vital, destruimos la sesión
   y mandamos al usuario al login para que entre limpio de nuevo.
*/
if (!is_object($usuario) || !method_exists($usuario, 'esJefe')) {
    session_destroy(); // Borra la sesión corrupta
    header("Location: ../Acceso/Acceso_proyecto_clases.php");
    exit();
}

// --> Ahora es seguro llamar a esJefe() porque hemos comprobado que existe.
$esJefe = $usuario->esJefe();

// Inicializar borrados visuales
// --> Si es la primera vez que entramos, creamos el array vacío para evitar errores de "Undefined index".
if (!isset($_SESSION['maquinas_borradas'])) {
    $_SESSION['maquinas_borradas'] = []; // Creamos un array por si queremos borrar varias máquinas a la vez
}

// 4. FUNCIÓN DE DIBUJO
// --> Esta función encapsula toda la lógica para pintar UNA casilla.
// --> Recibe el ID (ej: 'maquina_1') y si somos jefes.
function dibujarMaquina($machine_id, $esJefe) {
    
    // A) Verificar borrado visual (Soft Delete)
    // --> Miramos si el ID está en la lista de maquinas borradas de la sesión
    if (in_array($machine_id, $_SESSION['maquinas_borradas'])) {
        echo '<div></div>'; // Pinta un hueco vacío en la rejilla CSS
        return false;       // Termina aquí, no dibuja nada más.
    }

    // B) Cargar Configuración de la BD
    // --> Llama al método estático que conecta a BD y trae datos + stock + parámetros.
    $config = Maquina::obtenerPorId($machine_id); 
	//variable nueva donde guarda el resultado se convertirá en un objeto de tipo Maquina
	//:: significan que estás llamando a una función estática

    // --> Si la máquina no existe en BD, no dibujamos.
    if (!$config) return false;
    
	// --> Filtro extra: si el nombre indica error o vacío, no se dibuja.
    if (strpos($config->nombre, 'ERROR') !== false || //O
        strpos($config->nombre, 'SLOT VACÍO') !== false) {
        return false;
		//"String Position" (Posición en la cadena) busca un texto dentro de otro y te dice dónde está
		/*Busca la palabra 'ERROR' dentro del nombre de la máquina ($config->nombre),
		si el resultado NO ES false (es decir, me ha devuelto un número porque sí la encontró)
		entonces entra en el if y oculta la máquina*/
    }

    // C) Lógica de Sustitución Visual (Solo para Máquinas 1 y 6)
    // --> Por defecto, el enlace lleva a la máquina que pedimos.
    $link_destino = "../Maquinas/maquinas_plantilla.php?id=" . $machine_id; //El punto . en PHP sirve para pegar texto, estás pegando el código real de la máquina (por ejemplo: 'maquina_1').
	//El signo ? indica que vas a pasar datos por la URL, id es el nombre de la variable que viajará.
	//construye la dirección URL a la que irás si haces clic en la máquina
    $id_para_acciones = $machine_id;
    //copia de seguridad del ID original de la máquina para usarlo en los botones de gestión
	
    // --> CASO MÁQUINA 1:
    if ($machine_id == 'maquina_1') {
		// Si la máquina 1 está en estado B (Sustituida)
        if ($config->link == 'ESTADO_B') 
		// Intentamos cargar la sustituta (1_1)
		{
             $sustituta = Maquina::obtenerPorId('maquina_1_1');
			 // Si la sustituta existe y es válida:
             if ($sustituta && strpos($sustituta->nombre, 'ERROR') === false) {
                 $config = $sustituta; // Ahora la variable $config tiene los datos de la SUSTITUTA
                 $link_destino = "../Maquinas/maquinas_plantilla.php?id=maquina_1_1"; //// Cambiamos el enlace para que al hacer clic vayas a la 1_1
                 $id_para_acciones = 'maquina_1_1'; 
             }
        }
    }

    // --> CASO MÁQUINA 6:	
    elseif ($machine_id == 'maquina_6') {
        if ($config->link == 'ESTADO_B') {
             $sustituta = Maquina::obtenerPorId('maquina_6_1');
             if ($sustituta && strpos($sustituta->nombre, 'ERROR') === false) {
                 $config = $sustituta;
                 $link_destino = "../Maquinas/maquinas_plantilla.php?id=maquina_6_1";
                 $id_para_acciones = 'maquina_6_1';
             }
        }
    }

    // D) DIBUJAR LA TARJETA HTML
    // --> Aquí usamos los datos de $config (que puede ser la original o la sustituta).
    echo '<div class="machine-card">';
    echo '    <a href="' . $link_destino . '">'; 
    echo '        <h3>' . htmlspecialchars($config->id_maquina) . '</h3>'; // htmlspecialchars evita inyección de código HTML malicioso
    echo '        <p>' . htmlspecialchars($config->nombre) . '</p>';
    echo '        <img src="' . htmlspecialchars($config->imagen) . '" alt="Imagen" class="machine-img">';
    echo '    </a>';
    
    // E) CONTROLES DE JEFE
	// --> Solo se pintan si $esJefe es true.
    if ($esJefe) {
        echo '    <div class="machine-actions-container">';
        
        // FORMULARIO DE ACCIONES (EDITAR / SUSTITUIR)
        // Aquí apuntamos a gestionar_maquina.php
        echo '    <form class="form-edit" action="../../Capa_negocio/Maquinas/gestionar_maquina.php" method="POST">';
        echo '        <input type="hidden" name="machine_id" value="' . $id_para_acciones . '">';
        echo '        <select name="action">';
        echo '            <option value="editar">Editar</option>';
		
        if ($machine_id == 'maquina_1' || $machine_id == 'maquina_6')
        //(Solo aparece si es la 1 o la 6 original)
			{
            echo '            <option value="sustituir">Sustituir</option>';
        }
        echo '        </select>';
        echo '        <button type="submit" class="btn-action">Ir</button>';
        echo '    </form>';
        
        // FORMULARIO BORRAR
        echo '    <form class="form-delete" action="../../Capa_negocio/Maquinas/gestionar_maquina.php" method="POST">';
        echo '        <input type="hidden" name="machine_id" value="' . $machine_id . '">';
        echo '        <input type="hidden" name="action" value="borrar">'; 
        echo '        <button type="submit" class="btn-action-delete">Borrar</button>';
        echo '    </form>';
        echo '    </div>'; 
    }
    
    echo '</div>';
    return true; 
}

// 5. PREPARAR MÁQUINAS OPCIONALES (0 y 7)
// --> Antes de dibujar la cuadrícula, comprobamos si existen las máquinas de entrada y salida
// --> para saber si pintar la fila superior/inferior o dejarlas vacías.
$maq_0_activa = false;
$conf_0 = Maquina::obtenerPorId('maquina_0');
// Comprobamos si existe en BD Y si no ha sido borrada visualmente
if ($conf_0 && strpos($conf_0->nombre, 'SLOT VACÍO') === false && strpos($conf_0->nombre, 'ERROR') === false && !in_array('maquina_0', $_SESSION['maquinas_borradas'])) {
    $maq_0_activa = true;
}

$maq_7_activa = false;
$conf_7 = Maquina::obtenerPorId('maquina_7');
if ($conf_7 && strpos($conf_7->nombre, 'SLOT VACÍO') === false && strpos($conf_7->nombre, 'ERROR') === false && !in_array('maquina_7', $_SESSION['maquinas_borradas'])) {
    $maq_7_activa = true;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planta - <?php echo $esJefe ? 'JEFE' : 'EMPLEADO'; ?></title>
    <link rel="stylesheet" href="../../Lib/Estilos/estilo_planta.css">
    <style>
        .user-corner {
            position: absolute; top: 10px; right: 10px; 
            font-family: sans-serif; font-size: 0.9em;
            background: rgba(255,255,255,0.8); padding: 5px 10px; border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="main-container">
        
        <div class="user-corner">
            Usuario: <strong><?php echo $usuario->getNombre(); ?></strong> 
            <a href="../Acceso/Acceso_proyecto_clases.php" style="color:red; margin-left:5px;">(Salir)</a>
        </div>

        <div class="header">
            <img src="../../Lib/Imagenes/Logo_aceitunas.jpg" alt="Aceituna">
            <h1>SISTEMA <?php echo $esJefe ? 'JEFE' : 'EMPLEADO'; ?></h1>
        </div>
        <hr>
        
        <div class="machines-grid">
            <?php
			// --- DIBUJADO DE LA GRILLA ---
            // Usamos la función dibujarMaquina() para cada celda.

            // --- FILA SUPERIOR ---
            if ($maq_0_activa) {
                dibujarMaquina('maquina_0', $esJefe);
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
                echo '<div class="arrow down-arrow" style="grid-column-start: 1;">&darr;</div>';
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
            }
            
            // FILA 1 (Máquinas 1, 2, 3)
            dibujarMaquina('maquina_1', $esJefe);
            echo '<div class="arrow">&rarr;</div>'; 
            dibujarMaquina('maquina_2', $esJefe);
            echo '<div class="arrow">&rarr;</div>'; 
            dibujarMaquina('maquina_3', $esJefe);

            // --- FLECHA BAJADA ---
            echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
            echo '<div class="arrow down-arrow">&darr;</div>'; 

             // FILA 2 (Máquinas 6, 5, 4 - Sentido Inverso)
            dibujarMaquina('maquina_6', $esJefe);
            echo '<div class="arrow">&larr;</div>';
            dibujarMaquina('maquina_5', $esJefe);
            echo '<div class="arrow">&larr;</div>';
            dibujarMaquina('maquina_4', $esJefe);

            // --- FILA INFERIOR ---
            if ($maq_7_activa) {
                echo '<div class="arrow down-arrow" style="grid-column-start: 1;">&darr;</div>';
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
                dibujarMaquina('maquina_7', $esJefe);
                echo '<div></div>'; echo '<div></div>'; echo '<div></div>'; echo '<div></div>';
            }
            ?>
        </div>

        <div class="buttons">
            <?php if($esJefe): ?>
                <a href="../../Capa_negocio/Maquinas/anadir_paso1.php">
                    <button type="button" class="btn-aceptar">Añadir Máquina</button>
                </a>
                <a href="../../Capa_negocio/Planta_produccion/reset_vista.php">
                    <button type="button" class="btn-aceptar">Restaurar Máquinas</button>
                </a>
            <?php endif; ?>
            
            <a href="../Acceso/Acceso_proyecto_clases.php"><button type="button" class="btn-cancelar">Cerrar Sesión</button></a>
        </div>
    </div>
</body>
</html>