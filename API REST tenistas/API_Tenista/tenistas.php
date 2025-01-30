<?php
 /**
 * Ejercicio - API 
 *
 * @author Escriba aquí su nombre
 */

  require_once("../utiles/funciones.php");
  require_once("../utiles/utils.php");
  require_once("../utiles/config.php");

  $conexion = conectarPDO($dbInfo);

  // ESCRIBA AQUI EL CÓDIGO PHP NECESARIO
  
  /*
    Datos del tenista y una nueva clave con los titulos que tiene, que es una estructura en la que aparecen los nombres de los títulos agrupados por años
  */
  if ($_SERVER['REQUEST_METHOD'] == 'GET')
    {
        if (isset($_GET['id'])){
        //Mostrar un mensaje
            $select = "SELECT 
                t.id, 
                t.nombre, 
                t.apellidos, 
                t.altura, 
                t.anno_nacimiento,
                (
                    SELECT GROUP_CONCAT(DISTINCT CONCAT(ti.anno, ' (', tor.nombre, ')') ORDER BY ti.anno SEPARATOR ', ')
                    FROM titulos ti
                    INNER JOIN torneos tor ON tor.id = ti.torneo_id
                    WHERE ti.tenista_id = t.id
                    GROUP BY ti.tenista_id
                ) AS titulos
            FROM 
                tenistas t
            WHERE 
                t.id = :id";
            $consulta = $conexion->prepare($select);
            $consulta->bindParam(':id', $_GET['id']);
            $consulta->execute();
            if ($consulta->rowCount() > 0) {
            $json = json_encode($consulta->fetch(PDO::FETCH_ASSOC));
            
            // Decodificar el JSON a un array de PHP
            $data = json_decode($json, true);
            
            // Usar preg_match_all para capturar los años y los títulos
            preg_match_all('/(\d{4}) \((.*?)\)/', $data['titulos'], $matches);
            
            // Inicializar el array de títulos por año
            $titulosPorAno = [];
            
            // Iterar sobre los resultados y organizar los títulos por año
            foreach ($matches[1] as $key => $year) {
                $titulo = $matches[2][$key];
                if (!isset($titulosPorAno[$year])) {
                    $titulosPorAno[$year] = [];
                }
                $titulosPorAno[$year][] = $titulo;
            }
            
            // Asignar el array resultante al campo "titulos" en el array original
            $data['titulos'] = $titulosPorAno;
            
            // Convertir el array de PHP de vuelta a JSON
            $jsonFinal = json_encode($data, JSON_PRETTY_PRINT);

              salidaDatos ($jsonFinal,
                array('Content-Type: application/json', 'HTTP/1.1 200 OK'));
              }   else {
                      salidaDatos('No se encuentra el tenista', array('HTTP/1.1 404 Not Found'));
                  }
        } else {

        }
      exit();
    }
  // ESCRIBA AQUI EL CÓDIGO PHP NECESARIO

  if ($_SERVER['REQUEST_METHOD'] == 'DELETE')
  {
      // Transformamos el JSON de entrada de datos a un array asociativo
      $datos = json_decode(file_get_contents('php://input'), true);
      $id = $datos['id'];
      $delete = "DELETE FROM tenistas where id=:id";
      $consulta = $conexion->prepare($delete);
      $consulta->bindParam(':id', $id);
      $consulta->execute();
      if ($consulta->rowCount() > 0) {
      salidaDatos('Borrado realizado', array( 'HTTP/1.1 200 OK'));
      }   else {
              salidaDatos('No se encuentra el tenista recibido', array('HTTP/1.1 404 Not Found'));
          }
      exit();
  }

  salidaDatos('No se ha podido realizar la operación.', array('Content-Type: application/json', 'HTTP/1.1 400 Bad Request'));
  

  // ESCRIBA AQUI EL CÓDIGO PHP NECESARIO

//En caso de que ninguna de las opciones anteriores se haya ejecutado

?>