<?php
// Ruta relativa desde Capa_negocio/Stock/
include_once (__DIR__ . "/../../Lib/BD/AccesoBD.php");

class Stock {
    public $id_maquina;
    public $id;
    public $label;
    public $units = 'Uds'; 
    
    // Configuración
    public $alarm_c_low;
    public $rand_min;
    public $rand_max;

    // Valores
    public $valor_actual;
    public $valor_ayer;

    // Estado
    public $alarma_activa = null; 

    public function __construct($id_maquina, $id_stock, $data) {
        $this->id_maquina = $id_maquina;
        $this->id = $id_stock;
        
        $this->label = $data['label'];
        $this->alarm_c_low = $data['alarm_c_low'];
        $this->rand_min = $data['rand_min'];
        $this->rand_max = $data['rand_max'];

        $this->valor_actual = $data['valor_actual'];
        $this->valor_ayer = $data['valor_ayer'];

        $this->gestionarValores();
        $this->comprobarAlarmas();
    }

    private function gestionarValores() {
        $acabo_de_guardar = isset($_SESSION['flag_acabo_de_guardar']) && $_SESSION['flag_acabo_de_guardar'] === true;

        if ($acabo_de_guardar && $this->valor_actual !== null) {
            // Mantener
        } else {
            // Simular
            $min = (int)$this->rand_min; 
            $max = (int)$this->rand_max;
            if ($max <= $min) $max = $min + 1;

            $this->valor_actual = rand($min, $max);
            $this->valor_ayer = rand($min, $max);

            $bd = new AccesoBD();
            $bd->actualizarValor('stock', $this->id_maquina, $this->id, 'valor_actual', $this->valor_actual);
            $bd->actualizarValor('stock', $this->id_maquina, $this->id, 'valor_ayer', $this->valor_ayer);
        }
    }

    private function comprobarAlarmas() {
        $val = floatval($this->valor_actual);
        if ($val < $this->alarm_c_low) {
            $this->alarma_activa = 'stock_bajo';
        } else {
            $this->alarma_activa = null;
        }
    }
}
?>