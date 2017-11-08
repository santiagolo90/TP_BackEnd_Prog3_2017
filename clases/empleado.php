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

    public function InsertarEmpleadoParametros()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            //$cadenaConsulta = "INSERT into empleado (nombre,sexo,email,clave,turno,perfil)values('".$_POST["nombre"]."','".$_POST["sexo"]."','".$_POST["email"]."','".$_POST["clave"]."','".$_POST["turno"]."','".$_POST["perfil"]."')";
            $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into empleado (nombre,sexo,email,clave,turno,perfil)values(:nombre,:sexo,:email,:clave,:turno,:perfil)");
            //$consulta =$objetoAccesoDato->RetornarConsulta($cadenaConsulta);
            $consulta->bindValue(':nombre',$this->_nombre, PDO::PARAM_STR);
            $consulta->bindValue(':sexo', $this->_sexo, PDO::PARAM_STR);
            $consulta->bindValue(':email', $this->_email, PDO::PARAM_STR);
            $consulta->bindValue(':clave', $this->_clave, PDO::PARAM_STR);
            $consulta->bindValue(':turno', $this->_turno, PDO::PARAM_STR);
            $consulta->bindValue(':perfil', $this->_perfil, PDO::PARAM_STR);
            $consulta->execute();	
            //return $objetoAccesoDato->RetornarUltimoIdInsertado();
			return $consulta->fetchAll(PDO::FETCH_CLASS, "empleado");	
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