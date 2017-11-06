<?php

include_once "AccesoDatos.php";

 class estacionamiento
 {
     


    public static function TraerEmpleado()
	{
        $email = $_POST["email"];

			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM empleado WHERE email = '$email'");
			$consulta->execute();			
			return $consulta->fetchAll(PDO::FETCH_CLASS, "estacionamiento");		
	}
 }
 


?>