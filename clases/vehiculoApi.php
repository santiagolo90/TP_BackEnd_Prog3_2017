<?php
include_once "vehiculo.php";

class vehiculoApi extends vehiculo
{

    public function BorrarUno($request, $response, $args) {
        $ArrayDeParametros = $request->getParsedBody();
        $pat=$ArrayDeParametros['patente'];
        $vehiculo= new vehiculo();
        $vehiculo->_patente=$pat;
        $cantidadDeBorrados=$vehiculo->BorrarVehiculoPatente();

        $objDelaRespuesta= new stdclass();
       $objDelaRespuesta->cantidad=$cantidadDeBorrados;
       if($cantidadDeBorrados>0)
           {
                $objDelaRespuesta->resultado="algo borro!!!";
           }
           else
           {
               $objDelaRespuesta->resultado="no Borro nada!!!";
           }
       $newResponse = $response->withJson($objDelaRespuesta, 200);  
         return $newResponse;
   }


   public function CargarUno($request, $response, $args) {
    $ArrayDeParametros = $request->getParsedBody();
   //var_dump($ArrayDeParametros);
   $patente= $ArrayDeParametros['patente'];
   $color= $ArrayDeParametros['color'];
   $marca= $ArrayDeParametros['marca'];
   //$fecha_ing= $ArrayDeParametros['fecha_hora_ing'];
   
   $vehiculoAux = new vehiculo();

   $vehiculoAux->_patente = $patente;
   $vehiculoAux->_color = $color;
   $vehiculoAux->_marca = $marca;
   //$vehiculoAux->_clave = $fecha_ing;

   $vehiculoAux->InsertarVehiculoParametros();
/*
   $archivos = $request->getUploadedFiles();
   $destino="./fotos/";
   var_dump($archivos);
   var_dump($archivos['foto']);

   $nombreAnterior=$archivos['foto']->getClientFilename();
    $extension= explode(".", $nombreAnterior)  ;
   //var_dump($nombreAnterior);
   $extension=array_reverse($extension);

   $archivos['foto']->moveTo($destino.$titulo.".".$extension[0]);
    $response->getBody()->write("se guardo el cd");
*/
   return $response;
}
    

}



?>