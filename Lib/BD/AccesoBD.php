<?php
// Proyecto/Lib/BD/AccesoBD.php
require_once "config.php"; //Se llama al archivo de config

class AccesoBD //Se crea la clase acceso a la BD
{
    private $dbname; //se crea la variable nombre de la bd
    private $user; //se crea la variable usuario que accede a la bd
    private $password; //lo mismo para la clave
    private $dbh; // Database Handle (El "cable" de conexión)
    private $stmt; // Statement (La "caja" de la consulta preparada)

    /* * CONSTRUCTOR
     * Se ejecuta automáticamente al hacer 'new AccesoBD()'.
     * Carga las constantes definidas en config.php y llama a la función de conexión.
     */
    public function __construct() //Cuando escribes new AccesoBD en cualquier archivo llama esta funcion
    {
        $this->dbname = DB_NAME; //Se establece el nombre de la bd
        $this->user = DB_USER;
        $this->password = DB_PASS;
        $this->conectarBD(); //Llama a la funcion de conectar la BD
    } 

    /*
     * FUNCIÓN CONECTAR
     * Crea la conexión física con la base de datos usando la librería PDO.
     * Configura el modo de errores para que nos avise si falla algo en SQL.
     */
    private function conectarBD() //Esta es la funcion de conectar la BD
    {
        try {
            // Data Source Name: Define tipo de base de datos, host y nombre
            $dsn = "mysql:host=" . DB_HOST . ";dbname=$this->dbname;charset=utf8mb4";

            // Establecemos conexión usando PDO
            $this->dbh = new PDO($dsn, $this->user, $this->password);

            // Configurar los atributos de PDO en caso de errores o excepciones
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        } catch (PDOException $e) {
            echo "Error de conexión: " . $e->getMessage();
            exit();
        }
    }

    /*
     * PREPARAR CONSULTA
     * Recibe una sentencia SQL (ej: "SELECT * FROM usuarios WHERE id = :id")
     * y la deja lista en memoria, esperando recibir los valores seguros.
     * Esto es vital para evitar Hackeos (Inyección SQL).
     */
    public function prepararConsulta($cadena_sql)
    {
        $this->stmt = $this->dbh->prepare($cadena_sql);
    }

    /*
     * EJECUTAR CONSULTA
     * Coge el array de datos reales (ej: [':id' => 5]) y los combina con la consulta preparada.
     * Es el momento en el que la consulta realmente se envía a la base de datos.
     */
    public function lanzarConsultaPreparada($arrayDatos = array())
    {
        if (empty($arrayDatos)) {
            $this->stmt->execute();
        } else {
            $this->stmt->execute($arrayDatos);
        }
        return $this->stmt;
    }

    /*
     * OBTENER TODOS (LISTAS)
     * Devuelve TODAS las filas que encontró la consulta.
     * - Si pasas $nombreClase (ej: 'Usuario'), devuelve un array de objetos Usuario.
     * - Si no, devuelve objetos genéricos (stdClass).
     * Se usa para listados (ej: ver todas las máquinas de la planta).
     */
    public function registrosConsulta($nombreClase = '')
    {
        if (empty($nombreClase)) {
            return $this->stmt->fetchAll(PDO::FETCH_OBJ);
        } else {
            return $this->stmt->fetchAll(PDO::FETCH_CLASS, $nombreClase);
        }
    }
    
    /*
     * OBTENER UNO (DETALLE)
     * Devuelve SOLO LA PRIMERA fila encontrada.
     * Se usa cuando buscas por ID o verificas un Login (donde solo esperas un resultado).
     */
    public function obtenerFila($nombreClase = '')
    {
        if (empty($nombreClase)) {
            return $this->stmt->fetch(PDO::FETCH_OBJ);
        } else {
            $this->stmt->setFetchMode(PDO::FETCH_CLASS, $nombreClase);
            return $this->stmt->fetch();
        }
    }

    /*
     * CONTAR RESULTADOS
     * Devuelve el número de filas afectadas o encontradas por la última consulta.
     * Útil para saber si un usuario existe (devuelve 1) o no (devuelve 0).
     */
    public function numeroRegistros()
    {
        return $this->stmt->rowCount();
    }
    
    /*
     * DESCONECTAR
     * Cierra la conexión y libera la memoria. Aunque PHP lo hace solo al acabar el script,
     * es buena práctica hacerlo manualmente.
     */
    public function desconectar()
    {
        $this->dbh = null;
    }
}
?>