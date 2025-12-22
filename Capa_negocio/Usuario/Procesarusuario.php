<?php 
$Usuario=$_POST["usuario"]; /* todo tiene que acabar en ";" igual que en C++ ya que comparten muchas características */
$clave=$_POST["clave"];
$correo=$_POST["correo"];  /* "$" identifica una variable, que no constante, siempre empieza por caracter alfanumerico */  
echo ("$Usuario". "&nbsp;". "$clave"."&nbsp;"."$correo"); /* echo sirve para imprimir por pantalla (en el navegador) */ 
                  /*&nbsp sirve para poner un espacio SIN salto d línea*/
                  /* se puede usar tanto con parentesis () como sin ellos */
include_once("../../../Proyecto(pruebas)/prueba1usuario.php");
$usuarioEditar= new Usuario(); //almaceno las variables que recoge el formulario, podría hacerlo en otro archivo
$usuarioEditar->nombre=$Usuario;	 
$usuarioEditar->apellidos=$clave;	
$usuarioEditar->correo=$correo;	 
$usuarioEditar->GuardarUsuario();

header("Location:../../../Proyecto(pruebas)/listadousuarios.php"); //header sirve para redirigir a paginas en php
 

/*echo(rand(1,10));*/ /* "rand" genera un número aleatorio entre 1 y 10 */

/* "'" y "/" sirven para indicar que no ha acabado la cadena de caracteres */

?>
<!--se puede poner php tanto fuera como dentro del html -->