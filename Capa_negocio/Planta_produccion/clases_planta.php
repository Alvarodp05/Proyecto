<?php
// Incluimos el acceso a la BD
include_once (__DIR__ . "/../../Lib/BD/AccesoBD.php");

/**
 * Clase Maquinas (Contenedor)
 * Lista las máquinas disponibles desde la Base de Datos.
 */
class Maquinas
{
    public $lista_archivos = [];

    public function __construct() {
        $bd = new AccesoBD();
        $this->lista_archivos = $bd->obtenerListaMaquinas();
    }
}

/**
 * Clase Maquina_Config
 * Carga la estructura desde la Base de Datos.
 */
class Maquina_Config
{
    public $machine_id;
    public $machine_name;
    public $description;
    public $link;
    public $imagen;
    public $parameters = [];
    public $stock = [];
    
    public function __construct($machine_id)
    {
        $bd = new AccesoBD();
        $data = $bd->obtenerDatosMaquina($machine_id);

        if ($data) {
            $this->machine_id = $data['machine_id'];
            $this->machine_name = $data['machine_name'];
            $this->description = $data['description'];
            $this->link = $data['link'];
            $this->imagen = $data['imagen'];
            $this->parameters = $data['parameters'];
            $this->stock = $data['stock'];
        } else {
            $this->machine_name = "ERROR: $machine_id no encontrada";
        }
    }
}

/**
 * Clase Maquina_Valores
 * Gestiona los valores y Alarmas.
 */
class Maquina_Valores
{
    public $valores_actuales = [];
    public $valores_ayer = [];
    public $valores_antier = [];

    public $alarmas_correctivas = [];
    public $alarmas_preventivas = [];
    public $stock_seguridad = [];

    private $bd;
    private $machine_id;

    public function __construct(Maquina_Config $config)
    {
        $this->bd = new AccesoBD();
        $this->machine_id = $config->machine_id;

        // 1. Generar valores (o leer guardados)
        $this->inicializarValores($config);
        
        // 2. Superposición de Sesión (Legacy, por seguridad)
        $this->aplicarSuperposicionSesion();
        
        // 3. Comprobamos alarmas
        $this->comprobarAlarmas($config);
    }

    private function inicializarValores(Maquina_Config $config)
    {
        // SEMÁFORO: ¿Acabamos de venir del botón guardar?
        $acabo_de_guardar = isset($_SESSION['flag_acabo_de_guardar']) && $_SESSION['flag_acabo_de_guardar'] === true;

        // --- PARAMETROS ---
        foreach ($config->parameters as $key => $conf) {
            
            // Si acabamos de guardar, mantenemos ese valor fijo (lectura de BD).
            // Si es un refresco normal, simulamos nuevo valor (escritura en BD).
            if ($acabo_de_guardar && $conf['valor_actual'] !== null) {
                $this->valores_actuales[$key] = $conf['valor_actual'];
                $this->valores_ayer[$key] = $conf['valor_ayer'];
                $this->valores_antier[$key] = $conf['valor_antier'];
            } else {
                // SIMULACIÓN
                $min = (int)$conf['rand_min']; $max = (int)$conf['rand_max'];
                if ($max <= $min) $max = $min + 1;

                $val = rand($min, $max);
                $ayer = rand($min, $max);
                $antier = rand($min, $max);

                $this->valores_actuales[$key] = $val;
                $this->valores_ayer[$key] = $ayer;
                $this->valores_antier[$key] = $antier;

                $this->bd->actualizarValor('parametros', $this->machine_id, $key, 'valor_actual', $val);
                $this->bd->actualizarValor('parametros', $this->machine_id, $key, 'valor_ayer', $ayer);
                $this->bd->actualizarValor('parametros', $this->machine_id, $key, 'valor_antier', $antier);
            }
        }
        
        // --- STOCK (¡CORREGIDO!) ---
        foreach ($config->stock as $key => $conf) {
            
            // APLICAMOS LA MISMA LÓGICA QUE A PARÁMETROS
            if ($acabo_de_guardar && $conf['valor_actual'] !== null) {
                // Mantenemos el valor guardado
                $this->valores_actuales[$key] = $conf['valor_actual'];
                $this->valores_ayer[$key] = $conf['valor_ayer'];
            } else {
                // Simulamos nuevo valor (Inventario fluctuante)
                $min = (int)$conf['rand_min']; $max = (int)$conf['rand_max'];
                if ($max <= $min) $max = $min + 1;

                $val = rand($min, $max);
                $ayer = rand($min, $max);

                $this->valores_actuales[$key] = $val;
                $this->valores_ayer[$key] = $ayer;

                $this->bd->actualizarValor('stock', $this->machine_id, $key, 'valor_actual', $val);
                $this->bd->actualizarValor('stock', $this->machine_id, $key, 'valor_ayer', $ayer);
            }
        }

        // Apagamos el semáforo para el próximo refresco
        if ($acabo_de_guardar) {
            unset($_SESSION['flag_acabo_de_guardar']);
        }
    }

    private function aplicarSuperposicionSesion() {
        if (isset($_SESSION['superponer_valores'])) {
            foreach ($_SESSION['superponer_valores'] as $key => $valor) {
                $this->valores_actuales[$key] = $valor;
            }
            unset($_SESSION['superponer_valores']);
        }
    }

    private function comprobarAlarmas(Maquina_Config $config) {
        foreach ($config->parameters as $key => $conf) {
            $valor = floatval($this->valores_actuales[$key]);
            $label = $conf['label'];
            
            if ($valor < $conf['alarm_c_low'] || $valor > $conf['alarm_c_high']) {
                $this->alarmas_correctivas[] = $label;
            } elseif (($valor > $conf['alarm_c_low'] && $valor < $conf['alarm_p_low']) || 
                      ($valor < $conf['alarm_c_high'] && $valor > $conf['alarm_p_high'])) {
                $this->alarmas_preventivas[] = $label;
            }
        }
        
        foreach ($config->stock as $key => $conf) {
            $valor = floatval($this->valores_actuales[$key]);
            $label = $conf['label'];
            
            if ($valor < $conf['alarm_c_low']) {
                $this->stock_seguridad[] = "Falta stock: " . $label;
            }
        }
    }
}
?>