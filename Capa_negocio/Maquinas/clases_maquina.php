<?php
// Proyecto/Capa_negocio/Maquinas/clases_maquina.php
require_once(__DIR__ . "/../../Lib/BD/AccesoBD.php");
// Importamos las clases colaboradoras
require_once(__DIR__ . "/../Parametros/clases_parametro.php");
require_once(__DIR__ . "/../Stock/clases_stock.php");

class Maquina
{
    public $id_maquina;
    public $nombre;
    public $descripcion;
    public $imagen;
    public $link;
    public $orden;
    
    public $parametros = [];
    public $stock = [];

    public function __construct() {}

    public static function obtenerPorId($id) {
        $bd = new AccesoBD();
        
        $sql = "SELECT * FROM maquinas WHERE id_maquina = :id";
        $bd->prepararConsulta($sql);
        $bd->lanzarConsultaPreparada([':id' => $id]);
        $obj = $bd->obtenerFila('Maquina');
        
        if ($obj) {
            // DELEGAMOS LA CARGA DE DATOS
            $obj->parametros = Parametro::obtenerPorMaquina($id);
            $obj->stock = Stock::obtenerPorMaquina($id);
        }
        $bd->desconectar();
        return $obj;
    }

    // --- GUARDADO MANUAL (CORREGIDO PARA USAR DELEGACIÓN) ---
    public function actualizarValoresManuales($postParams, $postStock) {
        // Delegamos a las clases estáticas que ahora tienen el SQL correcto
        Parametro::actualizarManual($postParams);
        Stock::actualizarManual($postStock);
    }

    // --- SIMULACIÓN ---
    public function simularValores() {
        Parametro::simular($this->parametros);
        Stock::simular($this->stock);
    }

    // --- BORRADO ---
    public static function borrarDefinitivamente($id) {
        // Borramos hijos primero usando sus propias clases
        Stock::borrarPorMaquina($id);
        Parametro::borrarPorMaquina($id);

        $bd = new AccesoBD();
        $bd->prepararConsulta("DELETE FROM maquinas WHERE id_maquina = :id");
        $bd->lanzarConsultaPreparada([':id' => $id]);
        $bd->desconectar();
    }
    
    // --- CAMBIAR ESTADO ---
    public static function cambiarEstado($id, $nuevoEstado) {
        $bd = new AccesoBD();
        $sql = "UPDATE maquinas SET link = :estado WHERE id_maquina = :id";
        $bd->prepararConsulta($sql);
        $bd->lanzarConsultaPreparada([':estado' => $nuevoEstado, ':id' => $id]);
        $bd->desconectar();
    }

    // --- GUARDAR DESDE SESIÓN (WIZARD) ---
    public static function guardarDesdeSesion($datosSesion) {
        $bd = new AccesoBD();
        
        // 1. Update Maquina
        $sqlMaq = "UPDATE maquinas SET nombre = :nom, descripcion = :desc WHERE id_maquina = :id";
        if (isset($datosSesion['imagen'])) {
             $sqlMaq = "UPDATE maquinas SET nombre = :nom, descripcion = :desc, imagen = :img WHERE id_maquina = :id";
        }
        $bd->prepararConsulta($sqlMaq);
        $paramsMaq = [':nom' => $datosSesion['machine_name'], ':desc' => $datosSesion['description'], ':id' => $datosSesion['machine_id']];
        if (isset($datosSesion['imagen'])) $paramsMaq[':img'] = $datosSesion['imagen'];
        $bd->lanzarConsultaPreparada($paramsMaq);
        $bd->desconectar();

        // 2. Delegar guardado de hijos (Borrado previo e inserción)
        Parametro::guardarDesdeSesion($datosSesion['machine_id'], $datosSesion['parameters'] ?? []);
        Stock::guardarDesdeSesion($datosSesion['machine_id'], $datosSesion['stock'] ?? []);
    }
    
    public function guardar() {
        $bd = new AccesoBD();
        $sql = "INSERT INTO maquinas (id_maquina, nombre, descripcion, imagen, orden, link) VALUES (:id, :nom, :desc, :img, :ord, :link)";
        $bd->prepararConsulta($sql);
        $bd->lanzarConsultaPreparada([
            ':id' => $this->id_maquina,
            ':nom' => $this->nombre,
            ':desc' => $this->descripcion,
            ':img' => $this->imagen,
            ':ord' => $this->orden,
            ':link' => 'ESTADO_A'
        ]);
        $bd->desconectar();
    }
    
    // Auxiliares de sesión
    public static function ocultarEnSesion($id) {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (!isset($_SESSION['maquinas_borradas'])) $_SESSION['maquinas_borradas'] = [];
        if (!in_array($id, $_SESSION['maquinas_borradas'])) $_SESSION['maquinas_borradas'][] = $id;
    }
    
    public static function resetearTodo() {
        if (session_status() == PHP_SESSION_NONE) session_start();
        $_SESSION['maquinas_borradas'] = [];
    }
}
?>