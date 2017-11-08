<?php
include_once "empleado.php";

class empleadoApi extends empleado
{

    
/*
    public function __construct($id=NULL, $nombre=NULL, $sexo=NULL, $email=NULL, $clave=NULL, $turno=NULL, $perfil=NULL)
    {
        if($id !==NULL && $nombre !==NULL && $sexo !==NULL && $email !==NULL && $clave !==NULL && $turno !==NULL && $perfil !==NULL){
            
            $this->_id=$id;
            $this->_nombre = $nombre;
            $this->_sexo = $sexo;
            $this->_email = $email;
            $this->_clave = $clave;
            $this->_turno = $turno;
            $this->_perfil = $perfil;
        }
        
    }
*/


    public function BorrarUno($request, $response, $args) {
        $ArrayDeParametros = $request->getParsedBody();
        $id=$ArrayDeParametros['id'];
        $empleado= new empleado();
        $empleado->_id=$id;
        $cantidadDeBorrados=$empleado->BorrarEmpleado();

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
   $nombre= $ArrayDeParametros['nombre'];
   $sexo= $ArrayDeParametros['sexo'];
   $email= $ArrayDeParametros['email'];
   $clave= $ArrayDeParametros['clave'];
   $turno= $ArrayDeParametros['turno'];
   $perfil= $ArrayDeParametros['perfil'];
   
   $EmpleadoAux = new empleado();

   $EmpleadoAux->_nombre = $nombre;
   $EmpleadoAux->_sexo = $sexo;
   $EmpleadoAux->_email = $email;
   $EmpleadoAux->_clave = $clave;
   $EmpleadoAux->_turno = $turno;
   $EmpleadoAux->_perfil = $perfil;

   $EmpleadoAux->InsertarEmpleadoParametros();
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