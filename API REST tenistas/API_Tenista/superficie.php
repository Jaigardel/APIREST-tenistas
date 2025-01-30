<?php
 /**
 * Ejercicio - API
 *
 * @author Escriba aquí su nombre
 */

  require_once("../utiles/funciones.php");
  require_once("../utiles/config.php");
  require_once("../utiles/utils.php");

  // ESCRIBA AQUI EL CÓDIGO PHP NECESARIO
  $conexion = conectarPDO($dbInfo);

  if ($_SERVER['REQUEST_METHOD'] == 'PUT')
    {
      
        // Transformamos el JSON de entrada de datos a un array asociativo
        $datos = json_decode(file_get_contents('php://input'), true);
        $idPista = $datos['id'];
        $campos = getParams($datos);
        $update = "UPDATE superficies SET $campos WHERE id='$idPista'";
        $consulta = $conexion->prepare($update);
        bindAllParams($consulta, $datos);
        $consulta->execute();
        if($consulta->rowCount() > 0){
          salidaDatos('Actualización correcta', array( 'HTTP/1.1 200 OK'));
        }else{
          salidaDatos('Actualización no completada', array( 'HTTP/1.1 200 OK'));
        }
        exit();
    }
      salidaDatos('No se encuentra la superficie recibida.Error en la ejecución de la consulta de
actualización.', array('Content-Type: application/json', 'HTTP/1.1 400 Bad Request'));
    
//En caso de que ninguna de las opciones anteriores se haya ejecutado

?>