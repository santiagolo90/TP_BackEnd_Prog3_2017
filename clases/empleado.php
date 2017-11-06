<?php
include_once "AccesoDatos.php";
class empleado
{
    public $_id;
    public $_nombre;
    public $_sexo;
    public $_email;
    public $_clave;
    public $_turno;
    public $_perfil;
    

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

    public function GuardarEmpleado()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $cadenaConsulta = "INSERT into empleado (nombre,sexo,email,clave,turno,perfil)values('".$_POST["nombre"]."','".$_POST["sexo"]."','".$_POST["email"]."','".$_POST["clave"]."','".$_POST["turno"]."','".$_POST["perfil"]."')";
			$consulta =$objetoAccesoDato->RetornarConsulta($cadenaConsulta);
			$consulta->execute();	
			return $consulta->fetchAll(PDO::FETCH_CLASS, "empleado");	
    }

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
    
    public  function BorrarEmpleado()
	{
 		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("
			delete 
			from empleado			
			WHERE id=:id");	
			$consulta->bindValue(':id',$this->_id, PDO::PARAM_INT);		
			$consulta->execute();
			return $consulta->rowCount();
	}
}



?>