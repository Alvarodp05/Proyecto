<?php
// Proyecto/Capa_negocio/Stock/clases_stock.php
require_once(__DIR__ . "/../../Lib/BD/AccesoBD.php");

class Stock
{
    // Propiedades
    public $id;
    public $id_stock; // Identificador de texto (ej: stock_1)
    public $id_maquina;
    public $nombre;
    public $valor_actual;
    public $unidades;
    
    public $alarm_c_min;
    public $alarm_c_max;
    public $alarm_p_min;
    public $alarm_p_max;
    public $rand_min;
    public $rand_max;
    
    public $valor_ayer;
    public $valor_antier;

    // 1. OBTENER
    public static function obtenerPorMaquina($id_maquina) {
        $bd = new AccesoBD();
        $sql = "SELECT * FROM stock WHERE id_maquina = :id";
        $bd->prepararConsulta($sql);
        $bd->lanzarConsultaPreparada([':id' => $id_maquina]);
        $lista = $bd->registrosConsulta('Stock');
        $bd->desconectar();
        return $lista;
    }

    // 2. ACTUALIZAR MANUAL (Usa id_stock)
    public static function actualizarManual($postStock) {
        if (!is_array($postStock)) return;
        
        $bd = new AccesoBD();
        $sql = "UPDATE stock SET valor_actual = :val WHERE id_stock = :id";
        $bd->prepararConsulta($sql);
        
        foreach ($postStock as $id_txt => $valor) {
            $valor_limpio = str_replace(',', '.', $valor);
            if ($valor_limpio !== '' && is_numeric($valor_limpio)) {
                $bd->lanzarConsultaPreparada([':val' => $valor_limpio, ':id' => $id_txt]);
            }
        }
        $bd->desconectar();
    }

    // 3. SIMULAR (Usa id_stock)
    public static function simular($stockList) {
        $bd = new AccesoBD();
        $sql = "UPDATE stock SET valor_actual = :val WHERE id_stock = :id"; 
        $bd->prepararConsulta($sql);
        
        foreach ($stockList as $s) {
            $s = (object)$s;
            $min = (isset($s->rand_min) && is_numeric($s->rand_min)) ? (int)$s->rand_min : 0;
            $max = (isset($s->rand_max) && is_numeric($s->rand_max)) ? (int)$s->rand_max : 100;
            if ($min > $max) { $temp = $min; $min = $max; $max = $temp; }
            
            $aleatorio = mt_rand((int)$min, (int)$max);
            
            if (isset($s->id_stock)) {
                $bd->lanzarConsultaPreparada([':val' => $aleatorio, ':id' => $s->id_stock]);
            }
        }
        $bd->desconectar();
    }

    // 4. BORRAR (ESTA ES LA QUE TE FALTABA)
    public static function borrarPorMaquina($id_maquina) {
        $bd = new AccesoBD();
        $bd->prepararConsulta("DELETE FROM stock WHERE id_maquina = :id");
        $bd->lanzarConsultaPreparada([':id' => $id_maquina]);
        $bd->desconectar();
    }
    
    // 5. GUARDAR DESDE WIZARD
    public static function guardarDesdeSesion($id_maquina, $datosStock) {
        $bd = new AccesoBD();
        // Limpiamos primero
        $bd->prepararConsulta("DELETE FROM stock WHERE id_maquina = :id");
        $bd->lanzarConsultaPreparada([':id' => $id_maquina]);

        if (!empty($datosStock)) {
            $sqlInsS = "INSERT INTO stock (id_stock, id_maquina, nombre, valor_actual, alarm_c_min, alarm_c_max, alarm_p_min, alarm_p_max, rand_min, rand_max) 
                        VALUES (:ids, :idm, :lbl, :val, :cmin, :cmax, :pmin, :pmax, :rmin, :rmax)";
            $bd->prepararConsulta($sqlInsS);
            
            foreach ($datosStock as $key => $s) {
                $id_final = !empty($key) ? $key : uniqid();
                $val_ini = isset($s['rand_min']) ? $s['rand_min'] : 0;
                $bd->lanzarConsultaPreparada([
                    ':ids' => $id_final, ':idm' => $id_maquina, ':lbl' => $s['label'], ':val' => $val_ini, 
                    ':cmin' => $s['alarm_c_low'] ?? 0, ':cmax' => $s['alarm_c_high'] ?? 1000,
                    ':pmin' => $s['alarm_p_low'] ?? 0, ':pmax' => $s['alarm_p_high'] ?? 1000,
                    ':rmin' => $s['rand_min'] ?? 0, ':rmax' => $s['rand_max'] ?? 100
                ]);
            }
        }
        $bd->desconectar();
    }
}
?>