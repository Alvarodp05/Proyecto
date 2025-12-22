<?php
/* Usuario_clases.php */
require_once "../../Lib/BD/AccesoBD.php"; 

class usuario 
{
    var $nombre_usuario; 
    var $clave_usuario; 
    var $rol; 

    /* Asignar datos al objeto */
    function nombres($nombre, $clave, $rol) { 
        $this->nombre_usuario = $nombre;
        $this->clave_usuario = $clave; // Aquí se guardará el HASH ($2y$10$...)
        $this->rol = $rol; 
    }

    /* 1. Cargar usuarios de la BD */
    function obtenerUsuarios() {
        $usuarios_array = [];
        
        // Instancia tu clase de Acceso a Datos (ahora se llama AccesoBD, no BaseDatos)
        $bd = new AccesoBD();

        // Pedimos los datos
        $sql = "SELECT nombre, clave, rol FROM usuarios";
        $bd->LanzarConsulta($sql);
        
        $registros = $bd->RegistrosConsulta();

        // Llenamos el array de objetos
        if ($bd->NumeroRegistros() > 0) {
            foreach ($registros as $fila) {
                $nuevoUsuario = new usuario();
                $nuevoUsuario->nombres($fila->nombre, $fila->clave, $fila->rol);
                $usuarios_array[$nuevoUsuario->nombre_usuario] = $nuevoUsuario;
            }
        }

        $bd->desconectar();
        return $usuarios_array; 
    }

    // --- MÉTODOS DE COMPATIBILIDAD CON TU LOGIN ANTIGUO ---

    function obtenerJefes() {
        // Obtenemos todos y filtramos
        $todos = $this->obtenerUsuarios();
        $jefes = [];
        foreach($todos as $u) {
            if ($u->rol == 'jefe') {
                $jefes[$u->nombre_usuario] = $u;
            }
        }
        return $jefes;
    }

    function obtenerEmpleados() {
        // Obtenemos todos y filtramos
        $todos = $this->obtenerUsuarios();
        $empleados = [];
        foreach($todos as $u) {
            if ($u->rol == 'empleado') {
                $empleados[$u->nombre_usuario] = $u;
            }
        }
        return $empleados;
    }

    /* 2. Validar usando Seguridad Real (PASSWORD_VERIFY) */
    function validarUsuario($username, $password, $listaUsuarios) {
        // Verificar si existe el usuario en la lista cargada
        if (isset($listaUsuarios[$username])) {
            
            // Recuperamos el hash que vino de la base de datos
            $hash_guardado = $listaUsuarios[$username]->clave_usuario;
            
            // password_verify compara la contraseña escrita (plana) con el hash
            if (password_verify($password, $hash_guardado)) {
                return true; // Contraseña correcta
            }
        }
        return false; // Usuario no existe o clave incorrecta
    }

    // Wrappers para que tu formulario Acceso_proyecto_clases.php funcione igual
    function esJefeValido($user, $pass, $listaJefes) {
        // Verificamos credenciales
        if ($this->validarUsuario($user, $pass, $listaJefes)) {
            // Verificamos que sea jefe (doble check)
            return ($listaJefes[$user]->rol === 'jefe');
        }
        return false;
    }

    function esEmpleadoValido($user, $pass, $listaEmpleados) {
        if ($this->validarUsuario($user, $pass, $listaEmpleados)) {
            return ($listaEmpleados[$user]->rol === 'empleado');
        }
        return false;
    }
}
?>