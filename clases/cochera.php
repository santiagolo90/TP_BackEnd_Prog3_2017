<?php
include_once "AccesoDatos.php";
class cochera
{
    public $id;
    public $piso;
    public $numero;
    public $estado;
    public $tipo;
    
    public function InsertarEmpleadoParametros()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            //$cadenaConsulta = "INSERT into empleado (nombre,sexo,email,clave,turno,perfil)values('".$_POST["nombre"]."','".$_POST["sexo"]."','".$_POST["email"]."','".$_POST["clave"]."','".$_POST["turno"]."','".$_POST["perfil"]."')";
            $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into cocheras (piso,numero,estado,tipo)values(:piso,:numero,:estado,:tipo)");
            //$consulta =$objetoAccesoDato->RetornarConsulta($cadenaConsulta);
            $consulta->bindValue(':piso',$this->piso, PDO::PARAM_INT);
            $consulta->bindValue(':numero', $this->numero, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->execute();	
            return $objetoAccesoDato->RetornarUltimoIdInsertado();
			//return $consulta->fetchAll(PDO::FETCH_CLASS, "empleado");	
    }

    public static function TraerTodasLasCocheras()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select * from cocheras");
			$consulta->execute();			
			return $consulta->fetchAll(PDO::FETCH_CLASS, "cochera");		
    }

    public static function TraerCocheraEstado($estado) 
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select piso,numero,estado,tipo from cocheras where estado = '$estado'");
			$consulta->execute();
			$EmpAux= $consulta->fetchObject('cochera');
			return $EmpAux;		
    }
    public static function TraerCocheraID($id) 
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("select * from cocheras where id = '$id'");
			$consulta->execute();
            $cocheraAux= $consulta->fetchObject('cochera');
            if($consulta->rowCount() == 0){
                return false;   
            }
			return $cocheraAux;		
    }

    public static function TraerCocheraNumero($auxNumero) 
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("SELECT * from cocheras where numero = '$auxNumero'");
			$consulta->execute();
            $cocheraAux= $consulta->fetchObject('cochera');
            if($consulta->rowCount() == 0){
                return false;   
            }
            return $cocheraAux;	
	}
    
    public static function TraerPrimerCocheraLibreNormal()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM cocheras WHERE `estado`='libre' AND `tipo`='normal' LIMIT 1");
			$consulta->execute();
			$cocheraAux= $consulta->fetchObject('cochera');
			return $cocheraAux;		
    }

    public static function TraerPrimerCocheraLibreEspecial()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM cocheras WHERE `estado`='libre' AND `tipo`='especial' LIMIT 1");
			$consulta->execute();
			$cocheraAux= $consulta->fetchObject('cochera');
			return $cocheraAux;		
    }

    public static function OcuparCochera($auxID,$auxEST)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE cocheras set estado=:estado where id=$auxID");
        $consulta->bindValue(':estado', $auxEST, PDO::PARAM_STR);
        return $consulta->execute();

    }

   
    public static function BorrarCocheraID($auxID)
    {
       $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
       $consulta =$objetoAccesoDato->RetornarConsulta("DELETE FROM cocheras WHERE id=:id");		
       $consulta->bindValue(':id', $auxID, PDO::PARAM_INT);
       $consulta->execute();
       return $consulta->rowCount();
    }


    public function ModificarCochera($auxID)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE cocheras set piso=:piso,numero=:numero,estado=:estado,tipo=:tipo WHERE id=$auxID");
            //$consulta->bindValue(':id', $this->id, PDO::PARAM_INT);
            $consulta->bindValue(':piso', $this->piso, PDO::PARAM_INT);
            $consulta->bindValue(':numero', $this->numero, PDO::PARAM_INT);
            $consulta->bindValue(':estado', $this->estado, PDO::PARAM_STR);
            $consulta->bindValue(':tipo', $this->tipo, PDO::PARAM_STR);
            $consulta->execute();
            return $consulta->rowCount();
    }

    //Filtro de mas cocheras mas utilizadas
    public static function TraerMasUtilizada()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados group by idCochera ORDER BY cant desc ");
			$consulta->setFetchMode(PDO::FETCH_ASSOC);
            $consulta->execute();
            return $consulta->fetchAll();
  
    }

    //Filtro de mas cocheras mas utilizadas por ingresos
    public static function TraerMasUtilizadaFechaIngreso($desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($hasta == ""&& $desde !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraIngreso >=:desde group by idCochera ORDER BY cant desc");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraIngreso <=:hasta group by idCochera ORDER BY cant desc");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraIngreso BETWEEN :desde AND :hasta group by idCochera ORDER BY cant desc");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            return cochera::TraerMasUtilizada();
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }
    
    //Filtro de mas cocheras mas utilizadas por salidas
    public static function TraerMasUtilizadaFechaSalida($desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($hasta == ""&& $desde !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraSalida >=:desde group by idCochera ORDER BY cant desc");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraSalida <=:hasta group by idCochera ORDER BY cant desc");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraSalida BETWEEN :desde AND :hasta group by idCochera ORDER BY cant desc");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            return cochera::TraerMasUtilizada();
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
	}

    //Filtro de mas cocheras menos utilizadas
    public static function TraerMenosUtilizada()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados group by idCochera ORDER BY cant asc ");
			$consulta->setFetchMode(PDO::FETCH_ASSOC);
            $consulta->execute();
            return $consulta->fetchAll();
  
    }

    //Filtro de cocheras menos utilizadas por ingresos
    public static function TraerMenosUtilizadaFechaIngreso($desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($hasta == ""&& $desde !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraIngreso >=:desde group by idCochera ORDER BY cant asc");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraIngreso <=:hasta group by idCochera ORDER BY cant asc");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraIngreso BETWEEN :desde AND :hasta group by idCochera ORDER BY cant asc");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            return cochera::TraerMenosUtilizada();
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

    //Filtro de cocheras menos utilizadas por salidas
    public static function TraerMenosUtilizadaFechaSalida($desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($desde !="" && $hasta == "") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraSalida >=:desde group by idCochera ORDER BY cant asc");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraSalida <=:hasta group by idCochera ORDER BY cant asc");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraSalida BETWEEN :desde AND :hasta group by idCochera ORDER BY cant asc");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera, count(*) cant  FROM estacionados WHERE fechaHoraSalida <=:hasta group by idCochera ORDER BY cant asc");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
	}

    public static function TraerNuncaUtilizada()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("SELECT id,piso,numero,tipo FROM `cocheras` WHERE id NOT in (select idCochera from estacionados)");
			$consulta->setFetchMode(PDO::FETCH_ASSOC);
            $consulta->execute();
            return $consulta->fetchAll();
  
    }

    //Filtro de nunca cocheras mas utilizadas por ingresos
    public static function TraerNuncaUtilizadaFechaIngreso($desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($hasta == ""&& $desde !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT id,piso,numero,tipo FROM cocheras WHERE id NOT in (select idCochera from estacionados WHERE fechaHoraIngreso >=:desde)");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT id,piso,numero,tipo FROM cocheras WHERE id NOT in (select idCochera from estacionados WHERE fechaHoraIngreso <=:hasta)");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT id,piso,numero,tipo FROM cocheras WHERE id NOT in (select idCochera from estacionados WHERE fechaHoraIngreso BETWEEN :desde AND :hasta )");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            return cochera::TraerNuncaUtilizada();
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }
    



}
?>