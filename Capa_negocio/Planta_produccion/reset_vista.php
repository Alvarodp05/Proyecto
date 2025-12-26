<?php
// Proyecto/Capa_negocio/Planta_produccion/reset_vista.php

// 1. CARGA DE CLASES (Siempre antes de session_start)
require_once("../Usuario/clase_usuario.php");
require_once("../Maquinas/clases_maquina.php");

// 2. SESIÓN
session_start();

// 3. LLAMADA A LA CLASE
// Ahora coincide exactamente con el nombre en clases_maquina.php
Maquina::resetearTodo();

// 4. REDIRECCIÓN
header("Location: ../../Capa_usuario/Planta_produccion/plantaproduccion_plantilla.php");
exit();
?>