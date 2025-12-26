<?php
require_once("../../Lib/BD/AccesoBD.php"); //Se conecta al archivo de accesoBD

class Usuario //Se crea la clase usuario
{
    // Propiedades que coinciden con la tabla de los usuarios en ña BD
    private $nombre_usuario; // Primary Key en la BD
    private $clave;
    private $rol; // 'jefe' o 'empleado' 


    // Getters
    public function getNombre() { return $this->nombre_usuario; } //Se obtiene el nombre del usuario
    public function getRol() { return $this->rol; }//Lo mismo para el rol, por seguridad no se pone la clave ya que no es necesario

    //Verifica si el usuario y contraseña son correctos y devuelve true si el login es exitoso, false si falla.
    public function login($nombre, $password) 
    {
        $bd = new AccesoBD();//Nueva variable bd cuya clase es un AccesoBD

        //Se busca el usuario por su nombre
        $sql = "SELECT * FROM usuarios WHERE nombre = :nombre";//Se establece que es cuando el nombre se igual a un nombre que se define mas adelante
        $bd->prepararConsulta($sql); //Se prepara la consulta
        $bd->lanzarConsultaPreparada([':nombre' => $nombre]); //Se lanza la consulta preparada y se define que el nombre es el nombre de entrada del input

        // Verificamos si existe
        if ($bd->numeroRegistros() > 0) { //Si es mayor que 0 es pq hay alguien en la BD con ese nombre
            // Obtenemos los datos (asumiendo que devuelve un objeto genérico)
            $datos = $bd->obtenerFila(); //una vez se comprueba que existe, se obtienen todos los datos de la fila donde coincide el username

            // Se verifica la contraseña encriptada (Hash)
            // Si las claves no están encriptadas aún en la BD se debe abrir primero generar hashes para que encripte las claves y actualice la BD con las contraseñas encriptadas
            if (password_verify($password, $datos->clave)) { //Si la contraseña del input coincide con la clave de la BD
                
                // Login correcto
                $this->nombre_usuario = $datos->nombre; //Se establece que el nombre es el de la BD 
                $this->rol = $datos->rol;//Lo mismo para el rol
                $this->clave = $datos->clave; // Opcional guardarla
                
                $bd->desconectar(); //Se desconecta de la BD
                return true; //Devuelve un true para el exito del login
            }
        }

        $bd->desconectar(); //Si la clave no coincide simplemente se desconecta de la BD
        return false;//Y se devuelve un false
    }

    public function esJefe()//Se crea una funcion que en caso de que sea jefe se llamara
    {
        return $this->rol === 'jefe';//Lo que hace es establecer el rol como jefe
    }

    public function esEmpleado()//Exactamente lo mismo para el caso de los empleados
    {
        return $this->rol === 'empleado';
    }
}
?>