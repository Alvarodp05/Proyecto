<?php
// 1. Incluimos dependencias (BD y Clases hermanas)
include_once (__DIR__ . "/../../Lib/BD/AccesoBD.php");
include_once (__DIR__ . "/../Parametros/clases_parametro.php");
include_once (__DIR__ . "/../Stock/clases_stock.php");

/**
 * Clase Maquina (Base)
 * Representa la estructura de datos traída de la BD.
 */
class Maquina {
    public $id;
    public $nombre;
    public $descripcion;
    public $link;
    public $imagen;
    
    public $lista_parametros = []; 
    public $lista_stock = [];

    public function __construct($id_maquina) {
        $bd = new AccesoBD();
        $data = $bd->obtenerDatosMaquina($id_maquina);

        if ($data) {
            $this->id = $data['machine_id'];
            $this->nombre = $data['machine_name'];
            $this->descripcion = $data['description'];
            $this->link = $data['link'];
            $this->imagen = $data['imagen'];
            
            // Instanciamos objetos de las otras carpetas
            foreach ($data['parameters'] as $id_p => $p_data) {
                $this->lista_parametros[$id_p] = new Parametro($id_maquina, $id_p, $p_data);
            }
            
            foreach ($data['stock'] as $id_s => $s_data) {
                $this->lista_stock[$id_s] = new Stock($id_maquina, $id_s, $s_data);
            }
            
        } else {
            $this->nombre = "ERROR: $id_maquina no encontrada";
            // Evitar errores si la máquina no existe
            $this->id = $id_maquina;
            $this->descripcion = "";
            $this->link = "#";
            $this->imagen = "";
        }
    }
}

/**
 * GestorMaquinas
 * Obtiene la lista de IDs para las plantas.
 */
class GestorMaquinas {
    public $lista_ids = [];

    public function __construct() {
        $bd = new AccesoBD();
        $this->lista_ids = $bd->obtenerListaMaquinas();
    }
}

// ============================================================
// CLASES DE COMPATIBILIDAD (Antes en clases_planta.php)
// ============================================================

/**
 * Maquinas (Alias para compatibilidad con plantas antiguas)
 */
class Maquinas extends GestorMaquinas {
    public $lista_archivos; 
    public function __construct() {
        parent::__construct();
        $this->lista_archivos = $this->lista_ids;
    }
}

/**
 * Maquina_Config (Alias extendido)
 * Prepara los datos en formato array para las vistas HTML actuales.
 */
class Maquina_Config extends Maquina {
    public $machine_id;
    public $machine_name;
    public $parameters; 
    public $stock;      

    public function __construct($id) {
        parent::__construct($id);
        $this->machine_id = $this->id;
        $this->machine_name = $this->nombre;
        
        // Convertimos objetos a arrays simples para las vistas
        $this->parameters = [];
        foreach($this->lista_parametros as $key => $obj) {
            $this->parameters[$key] = [
                'label' => $obj->label, 
                'units' => $obj->units,
                'valor_actual' => $obj->valor_actual
            ]; 
        }
        $this->stock = [];
        foreach($this->lista_stock as $key => $obj) {
            $this->stock[$key] = [
                'label' => $obj->label,
                'units' => $obj->units,
                'valor_actual' => $obj->valor_actual
            ];
        }
    }
}

/**
 * Maquina_Valores
 * Agrupa los valores y gestiona los arrays de alarmas para la vista.
 */
class Maquina_Valores {
    public $valores_actuales = [];
    public $valores_ayer = [];
    public $valores_antier = [];
    public $alarmas_correctivas = [];
    public $alarmas_preventivas = [];
    public $stock_seguridad = [];

    public function __construct(Maquina_Config $maquina) {
        // 1. Extraer valores de Parámetros
        foreach ($maquina->lista_parametros as $key => $p) {
            $this->valores_actuales[$key] = $p->valor_actual;
            $this->valores_ayer[$key] = $p->valor_ayer;
            $this->valores_antier[$key] = $p->valor_antier;
            
            if ($p->alarma_activa == 'correctiva') $this->alarmas_correctivas[] = $p->label;
            if ($p->alarma_activa == 'preventiva') $this->alarmas_preventivas[] = $p->label;
        }

        // 2. Extraer valores de Stock
        foreach ($maquina->lista_stock as $key => $s) {
            $this->valores_actuales[$key] = $s->valor_actual;
            $this->valores_ayer[$key] = $s->valor_ayer;
            
            if ($s->alarma_activa == 'stock_bajo') $this->stock_seguridad[] = "Falta stock: " . $s->label;
        }

        // 3. Limpiar el semáforo de guardado (Sesión)
        if (isset($_SESSION['flag_acabo_de_guardar']) && $_SESSION['flag_acabo_de_guardar'] === true) {
            unset($_SESSION['flag_acabo_de_guardar']);
        }
    }
}
?>