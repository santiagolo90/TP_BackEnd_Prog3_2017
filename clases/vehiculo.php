<?php
include_once "AccesoDatos.php";
class vehiculo
{
    public $_patente;
    public $_color;
    public  $_marca;
    public $_fechaIng;

    public function __construct($patente= NULL,$color=NULL,$marca=NULL)
    {
        if($patente !==NULL && $color !==NULL && $marca !==NULL){
        $this->_patente = $patente;
        $this->_color = $color;
        $this->_marca = $marca;
        //$this->_fechaIng = date("Y-m-d H:i:s");
        }
        
    }

    public function InsertarVehiculoParametros()
	{
            $ahora = date("Y-m-d H:i:s");
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            //$cadenaConsulta = "INSERT into estacionados (patente,color,marca,fecha_hora_ing)values('".$_POST["patente"]."','".$_POST["color"]."','".$_POST["marca"]."','".$ahora."')";
            $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into estacionados (patente,color,marca,fecha_hora_ing)values(:patente,:color,:marca,:ahora)");
            //$consulta =$objetoAccesoDato->RetornarConsulta($cadenaConsulta);
            $consulta->bindValue(':patente',$this->_patente, PDO::PARAM_STR);
            $consulta->bindValue(':color', $this->_color, PDO::PARAM_STR);
            $consulta->bindValue(':marca', $this->_marca, PDO::PARAM_STR);
           $consulta->bindValue(':ahora', $ahora, PDO::PARAM_STR);
            //$consulta->bindValue(array(":ahora"=>date("Y-m-d H:i:s", strtotime($date)), PDO::PARAM_STR));
			$consulta->execute();	
			return $consulta->fetchAll(PDO::FETCH_CLASS, "vehiculo");	
    }


    public  function BorrarVehiculoPatente()
	{
 		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("
			delete 
			from estacionados			
			WHERE patente=:patente");	
			$consulta->bindValue(':patente',$this->_patente, PDO::PARAM_STR);		
			$consulta->execute();
			return $consulta->rowCount();
	}

}





?>