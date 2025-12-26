<?php
// Proyecto/Capa_negocio/Parametros/clases_parametro.php
require_once(__DIR__ . "/../../Lib/BD/AccesoBD.php");

class Parametro
{
    public $id; 
    public $id_parametro; // Identificador de texto
    public $id_maquina;
    public $nombre;
    public $unidades;
    public $alarm_c_min;
    public $alarm_c_max;
    public $alarm_p_min;
    public $alarm_p_max;
    public $rand_min;
    public $rand_max;
    public $valor_actual;
    public $valor_ayer;
    public $valor_antier;

    public static function obtenerPorMaquina($id_maquina) {
        $bd = new AccesoBD();
        $sql = "SELECT * FROM parametros WHERE id_maquina = :id";
        $bd->prepararConsulta($sql);
        $bd->lanzarConsultaPreparada([':id' => $id_maquina]);
        $lista = $bd->registrosConsulta('Parametro');
        $bd->desconectar();
        return $lista;
    }

    public static function actualizarManual($postParams) {
        if (!is_array($postParams)) return;
        
        $bd = new AccesoBD();
        $sql = "UPDATE parametros SET valor_actual = :val WHERE id_parametro = :id";
        $bd->prepararConsulta($sql);
        
        foreach ($postParams as $id_txt => $valor) {
            $valor_limpio = str_replace(',', '.', $valor);
            if ($valor_limpio !== '' && is_numeric($valor_limpio)) {
                $bd->lanzarConsultaPreparada([':val' => $valor_limpio, ':id' => $id_txt]);
            }
        }
        $bd->desconectar();
    }

    public static function simular($parametros) {
        $bd = new AccesoBD();
        $sql = "UPDATE parametros SET valor_actual = :val WHERE id_parametro = :id";
        $bd->prepararConsulta($sql);
        
        foreach ($parametros as $p) {
            $p = (object)$p;
            $min = (float)($p->rand_min ?? 0);
            $max = (float)($p->rand_max ?? 100);
            if ($min >= $max) $max = $min + 0.1;
            $aleatorio = mt_rand((int)($min*100), (int)($max*100)) / 100;
            
            if (isset($p->id_parametro)) {
                $bd->lanzarConsultaPreparada([':val' => $aleatorio, ':id' => $p->id_parametro]);
            }
        }
        $bd->desconectar();
    }

    // ESTA FUNCION ES NECESARIA PARA EL BORRADO
    public static function borrarPorMaquina($id_maquina) {
        $bd = new AccesoBD();
        $bd->prepararConsulta("DELETE FROM parametros WHERE id_maquina = :id");
        $bd->lanzarConsultaPreparada([':id' => $id_maquina]);
        $bd->desconectar();
    }
    
    public static function guardarDesdeSesion($id_maquina, $datosParametros) {
        $bd = new AccesoBD();
        $bd->prepararConsulta("DELETE FROM parametros WHERE id_maquina = :id");
        $bd->lanzarConsultaPreparada([':id' => $id_maquina]);

        if (!empty($datosParametros)) {
            $sqlInsP = "INSERT INTO parametros (id_parametro, id_maquina, nombre, unidades, alarm_c_min, alarm_c_max, alarm_p_min, alarm_p_max, rand_min, rand_max, valor_actual) 
                        VALUES (:idp, :idm, :lbl, :uni, :cmin, :cmax, :pmin, :pmax, :rmin, :rmax, :valini)";
            $bd->prepararConsulta($sqlInsP);
            foreach ($datosParametros as $key => $p) {
                $id_final = !empty($key) ? $key : uniqid();
                $val_ini = isset($p['rand_min']) ? $p['rand_min'] : 0;
                $bd->lanzarConsultaPreparada([
                    ':idp' => $id_final, ':idm' => $id_maquina, ':lbl' => $p['label'], ':uni' => $p['units'] ?? '',
                    ':cmin' => $p['alarm_c_low'], ':cmax' => $p['alarm_c_high'], ':pmin' => $p['alarm_p_low'], ':pmax' => $p['alarm_p_high'],
                    ':rmin' => $p['rand_min'] ?? 0, ':rmax' => $p['rand_max'] ?? 100, ':valini' => $val_ini
                ]);
            }
        }
        $bd->desconectar();
    }
}
?>