<?php
 /**
 * Ejercicio - API
 *
 * @author Escriba aquí su nombre
 */

  require_once("../utiles/config.php");
  require_once("../utiles/funciones.php");
  require_once("../utiles/utils.php");


  // ESCRIBA AQUI EL CÓDIGO PHP NECESARIO

  $conexion = conectarPDO($dbInfo);

  if ($_SERVER['REQUEST_METHOD'] == 'POST')
  {
      // Transformamos el JSON de entrada de datos a un array asociativo
      $datos = json_decode(file_get_contents('php://input'), true);
      $anno = $datos["anno"];
      $idTorneo = $datos["torneo_id"];
      $sql = "SELECT * FROM titulos WHERE anno=:anno AND torneo_id=:torneo_id";
      $consulta = $conexion->prepare($sql);
      $consulta->bindParam(':anno', $anno);
      $consulta->bindParam(':torneo_id', $idTorneo);
      $consulta->execute();
      if($consulta->rowCount() > 0){
        salidaDatos('Ya hay un tenista que ha ganado ese torneo en ese año', array( 'HTTP/1.1 200 OK'));
        $consulta = null;
      }else{
        $consulta = null;
        $insert = "INSERT INTO titulos(anno, tenista_id, torneo_id) VALUES (:anno, :tenista_id, :torneo_id)";
        $consulta = $conexion->prepare($insert);
        bindAllParams($consulta, $datos);
        $consulta->execute();
        $mensajeId = $conexion->lastInsertId();
        if($mensajeId==0) {
            salidaDatos("Operación realizada con éxito", array('HTTP/1.1 200 OK'));
        }
      }
      exit();
  }
    salidaDatos('No se ha podido realizar la operación.', array('Content-Type: application/json', 'HTTP/1.1 400 Bad Request'));
  


?>