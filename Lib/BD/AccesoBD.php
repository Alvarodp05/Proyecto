<?php
include_once 'config.php';

class AccesoBD {
    private $conexion;
    private $resultado; // Variable para guardar resultados de consultas genéricas

    public function __construct() {
        $this->conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($this->conexion->connect_error) {
            die("Error de conexión: " . $this->conexion->connect_error);
        }
        $this->conexion->set_charset("utf8");
    }

    // --- MÉTODOS GENÉRICOS AÑADIDOS PARA COMPATIBILIDAD ---
    public function LanzarConsulta($sql) {
        $this->resultado = $this->conexion->query($sql);
        return $this->resultado;
    }

    public function RegistrosConsulta() {
        $filas = [];
        if ($this->resultado) {
            while ($obj = $this->resultado->fetch_object()) {
                $filas[] = $obj;
            }
        }
        return $filas;
    }

    public function NumeroRegistros() {
        return $this->resultado ? $this->resultado->num_rows : 0;
    }

    public function desconectar() {
        $this->conexion->close();
    }
    
    public function getConexion() {
        return $this->conexion;
    }

    // --- MÉTODOS ESPECÍFICOS DEL PROYECTO (MANTENIDOS) ---

    public function obtenerListaMaquinas() {
        $sql = "SELECT id_maquina, nombre, imagen, link, orden FROM maquinas ORDER BY orden ASC";
        $result = $this->conexion->query($sql);
        $lista = [];
        while ($row = $result->fetch_assoc()) {
            $lista[$row['id_maquina']] = $row['id_maquina']; 
        }
        return $lista;
    }

    public function obtenerDatosMaquina($id_maquina) {
        $sql = "SELECT * FROM maquinas WHERE id_maquina = '$id_maquina'";
        $result = $this->conexion->query($sql);
        if ($result->num_rows == 0) return null;
        $maquina = $result->fetch_assoc();

        $datos = [
            'machine_id' => $maquina['id_maquina'],
            'machine_name' => $maquina['nombre'],
            'description' => $maquina['descripcion'],
            'link' => $maquina['link'],
            'imagen' => $maquina['imagen'],
            'parameters' => [],
            'stock' => []
        ];

        $sql_param = "SELECT * FROM parametros WHERE id_maquina = '$id_maquina'";
        $res_param = $this->conexion->query($sql_param);
        while ($row = $res_param->fetch_assoc()) {
            $datos['parameters'][$row['id_parametro']] = [
                'label' => $row['nombre'], 'units' => $row['unidades'],
                'alarm_c_low' => $row['alarm_c_min'], 'alarm_c_high' => $row['alarm_c_max'],
                'alarm_p_low' => $row['alarm_p_min'], 'alarm_p_high' => $row['alarm_p_max'],
                'rand_min' => $row['rand_min'], 'rand_max' => $row['rand_max'],
                'valor_actual' => $row['valor_actual'], 'valor_ayer' => $row['valor_ayer'],
                'valor_antier' => $row['valor_antier']
            ];
        }

        $sql_stock = "SELECT * FROM stock WHERE id_maquina = '$id_maquina'";
        $res_stock = $this->conexion->query($sql_stock);
        while ($row = $res_stock->fetch_assoc()) {
            $datos['stock'][$row['id_stock']] = [
                'label' => $row['nombre'], 'units' => 'Uds',
                'alarm_c_low' => $row['alarm_c_min'], 'alarm_c_high' => $row['alarm_c_max'],
                'alarm_p_low' => $row['alarm_p_min'], 'alarm_p_high' => $row['alarm_p_max'],
                'rand_min' => $row['rand_min'], 'rand_max' => $row['rand_max'],
                'valor_actual' => $row['valor_actual'], 'valor_ayer' => $row['valor_ayer']
            ];
        }
        return $datos;
    }

    public function actualizarValor($tabla, $id_maquina, $id_item, $columna, $valor) {
        $campo_id = ($tabla == 'parametros') ? 'id_parametro' : 'id_stock';
        
        // --- LÓGICA CRÍTICA PARA ALEATORIOS Y VACÍOS ---
        // Si el valor es null O es una cadena vacía (''), ponemos NULL en SQL
        if ($valor === null || $valor === '') {
            $sql = "UPDATE $tabla SET $columna = NULL WHERE id_maquina = '$id_maquina' AND $campo_id = '$id_item'";
        } else {
            // Si hay valor real, aseguramos que sea string, limpiamos y convertimos coma a punto
            $valor_str = (string)$valor; 
            $valor_clean = $this->conexion->real_escape_string(str_replace(',', '.', $valor_str));
            
            $sql = "UPDATE $tabla SET $columna = '$valor_clean' WHERE id_maquina = '$id_maquina' AND $campo_id = '$id_item'";
        }
		return $this->conexion->query($sql);
    }
    
    public function actualizarEstadoMaquina($id_maquina, $nuevo_estado) {
        $sql = "UPDATE maquinas SET link = '$nuevo_estado' WHERE id_maquina = '$id_maquina'";
        return $this->conexion->query($sql);
    }

    public function guardarInfoGeneral($id, $nombre, $desc, $img, $link = null, $orden = null) {
        $id = $this->conexion->real_escape_string($id);
        $nombre = $this->conexion->real_escape_string($nombre);
        $desc = $this->conexion->real_escape_string($desc);
        $img = $this->conexion->real_escape_string($img);
        $orden_sql = ($orden !== null) ? $orden : 0;
        $link_sql = ($link !== null) ? $link : '#';

        $sql = "INSERT INTO maquinas (id_maquina, nombre, descripcion, imagen, link, orden) 
                VALUES ('$id', '$nombre', '$desc', '$img', '$link_sql', '$orden_sql') 
                ON DUPLICATE KEY UPDATE nombre='$nombre', descripcion='$desc', imagen='$img'";
        if ($link !== null) { $sql .= ", link='$link'"; }
        return $this->conexion->query($sql);
    }

    public function limpiarDetallesMaquina($id_maquina) {
        $this->conexion->query("DELETE FROM parametros WHERE id_maquina = '$id_maquina'");
        $this->conexion->query("DELETE FROM stock WHERE id_maquina = '$id_maquina'");
    }

    public function insertarParametro($id_maq, $id_p, $nombre, $units, $c_min, $c_max, $p_min, $p_max, $r_min, $r_max) {
        $c_min = str_replace(',', '.', $c_min); $c_max = str_replace(',', '.', $c_max);
        $p_min = str_replace(',', '.', $p_min); $p_max = str_replace(',', '.', $p_max);
        $r_min = str_replace(',', '.', $r_min); $r_max = str_replace(',', '.', $r_max);
        $sql = "INSERT INTO parametros (id_maquina, id_parametro, nombre, unidades, alarm_c_min, alarm_c_max, alarm_p_min, alarm_p_max, rand_min, rand_max) 
                VALUES ('$id_maq', '$id_p', '$nombre', '$units', '$c_min', '$c_max', '$p_min', '$p_max', '$r_min', '$r_max')";
        return $this->conexion->query($sql);
    }

    public function insertarStock($id_maq, $id_s, $nombre, $c_min, $c_max, $p_min, $p_max, $r_min, $r_max) {
        $c_min = str_replace(',', '.', $c_min); $c_max = str_replace(',', '.', $c_max);
        $p_min = str_replace(',', '.', $p_min); $p_max = str_replace(',', '.', $p_max);
        $r_min = str_replace(',', '.', $r_min); $r_max = str_replace(',', '.', $r_max);
        $sql = "INSERT INTO stock (id_maquina, id_stock, nombre, alarm_c_min, alarm_c_max, alarm_p_min, alarm_p_max, rand_min, rand_max) 
                VALUES ('$id_maq', '$id_s', '$nombre', '$c_min', '$c_max', '$p_min', '$p_max', '$r_min', '$r_max')";
        return $this->conexion->query($sql);
    }

    public function resetearMaquina($id_maquina, $nombre_vacio) {
        $sql = "UPDATE maquinas SET nombre = '$nombre_vacio', descripcion = 'Este slot está disponible.', imagen = '../../Lib/Imágenes/maquina_default.jpg', link = '#' WHERE id_maquina = '$id_maquina'";
        $this->limpiarDetallesMaquina($id_maquina);
        return $this->conexion->query($sql);
    }
}
?>