<?php
include_once "AccesoDatos.php";
class vehiculo
{
    public $patente;
    public $color;
    public $marca;
    public $foto;
    public $idEmpleadoIngreso;
    public $fechaHoraIngreso;
    public $idCochera;
    public $idEmpleadoSalida;
    public $fechaHoraSalida;
    public $tiempoTrans;
    public $importe;

    public function InsertarVehiculoParametros()
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
            $consulta =$objetoAccesoDato->RetornarConsulta("INSERT into estacionados (patente,color,marca,foto,idEmpleadoIngreso,fechaHoraIngreso,idCochera)values(:patente,:color,:marca,:foto,:idEmpleadoIngreso,:fechaHoraIngreso,:idCochera)");
            $consulta->bindValue(':patente',$this->patente, PDO::PARAM_STR);
            $consulta->bindValue(':color', $this->color, PDO::PARAM_STR);
            $consulta->bindValue(':marca', $this->marca, PDO::PARAM_STR);
            $consulta->bindValue(':foto', $this->foto, PDO::PARAM_STR);
            $consulta->bindValue(':idEmpleadoIngreso', $this->idEmpleadoIngreso, PDO::PARAM_STR);
            $consulta->bindValue(':fechaHoraIngreso', $this->fechaHoraIngreso, PDO::PARAM_STR);
            $consulta->bindValue(':idCochera', $this->idCochera, PDO::PARAM_STR);
			$consulta->execute();	
			return $consulta->fetchAll(PDO::FETCH_CLASS, "vehiculo");	
    }

    public static function TraerVehiculoPatente($patenteAux) 
	{
			$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
			$consulta =$objetoAccesoDato->RetornarConsulta("SELECT * from estacionados where patente = '$patenteAux'");
			$consulta->execute();
            $vehiculoAux = $consulta->fetchObject('vehiculo');
            if($consulta->rowCount() == 0){
                return false;   
            }
			return $vehiculoAux;		
    }


    public static function ExisteVehiculo($patenteAux) 
	{
        //devuelve null si el idEmpleadoSalida no existe
		$objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
		$consulta =$objetoAccesoDato->RetornarConsulta("SELECT idEmpleadoSalida from estacionados where patente = '$patenteAux'");
        $consulta->execute();
        return $consulta->fetchColumn();		
    }
    public static function ExisteVehiculo2($patente)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT * FROM estacionados WHERE patente = '$patente' AND fechaHoraSalida is NULL");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_CLASS, "vehiculo");
    }
    
    public static function RegistarSaludaVehiculo($patenteAux,$auxEmpID,$FHSalidaAux,$tiempoAux,$importeAux)
    {
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso();

        $consulta =$objetoAccesoDato->RetornarConsulta("UPDATE estacionados SET idEmpleadoSalida=:idEmpleadoSalida, fechaHoraSalida=:fechaHoraSalida,tiempoTrans=:tiempoTrans, importe=:importe  where patente = '$patenteAux'");
        $consulta->bindValue(':idEmpleadoSalida', $auxEmpID, PDO::PARAM_INT);
        $consulta->bindValue(':fechaHoraSalida', $FHSalidaAux, PDO::PARAM_STR);
        $consulta->bindValue(':tiempoTrans', $tiempoAux, PDO::PARAM_STR);
        $consulta->bindValue(':importe', $importeAux, PDO::PARAM_STR);
        return $consulta->execute();

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
    
    //Trae todos los movimientos por patente
    public static function TraerEstacionadosPatente($patenteAux) 
	{
        $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
        $consulta =$objetoAccesoDato->RetornarConsulta("SELECT idCochera,fechaHoraIngreso,fechaHoraSalida,importe from estacionados where patente = '$patenteAux'");
        $consulta->execute();
        $vehiculoAux = $consulta->fetchAll(PDO::FETCH_CLASS);
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $vehiculoAux;		
	
    }

    //Trae todos los movimientos por patente filtrado por fecha de ingreso
    public static function TraerEstacionadosPatenteFechaIngreso($patenteAux,$desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($desde !="" && $hasta == "") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera,fechaHoraIngreso,fechaHoraSalida,importe FROM estacionados WHERE patente = '$patenteAux' and fechaHoraIngreso >=:desde");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera,fechaHoraIngreso,fechaHoraSalida,importe FROM estacionados WHERE patente = '$patenteAux' and fechaHoraIngreso <=:hasta");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera,fechaHoraIngreso,fechaHoraSalida,importe FROM estacionados WHERE patente = '$patenteAux' and fechaHoraIngreso BETWEEN :desde AND :hasta");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            $ahora= date("Y-m-d 23:59:59");
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera,fechaHoraIngreso,fechaHoraSalida,importe FROM estacionados WHERE patente = '$patenteAux' and fechaHoraIngreso <=:hasta");
            $consulta->bindValue(":hasta", $ahora, PDO::PARAM_STR);
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

    //Trae todos los movimientos por patente filtrado por fecha de ingreso
    public static function TraerEstacionadosPatenteFechaSalida($patenteAux,$desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($desde !="" && $hasta == "") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera,fechaHoraIngreso,fechaHoraSalida,importe FROM estacionados WHERE patente = '$patenteAux' and fechaHoraSalida >=:desde");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera,fechaHoraIngreso,fechaHoraSalida,importe FROM estacionados WHERE patente = '$patenteAux' and fechaHoraSalida <=:hasta");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera,fechaHoraIngreso,fechaHoraSalida,importe FROM estacionados WHERE patente = '$patenteAux' and fechaHoraSalida BETWEEN :desde AND :hasta");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            $ahora= date("Y-m-d 23:59:59");
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT idCochera,fechaHoraIngreso,fechaHoraSalida,importe FROM estacionados WHERE patente = '$patenteAux' and fechaHoraSalida <=:hasta");
            $consulta->bindValue(":hasta", $ahora, PDO::PARAM_STR);
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

    //Trae el total facturado entre fechas
    public static function TraerFacturacionFechas($desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($desde !="" && $hasta == "") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(importe) as CantVehiculos , SUM(importe) as $ FROM estacionados WHERE fechaHoraSalida >=:desde");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(importe) as CantVehiculos ,SUM(importe)as $ FROM estacionados WHERE fechaHoraSalida <=:hasta");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(importe) as CantVehiculos , SUM(importe)as $ FROM estacionados WHERE fechaHoraSalida BETWEEN :desde AND :hasta");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            $ahora= date("Y-m-d 23:59:59");
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(importe) as CantVehiculos ,SUM(importe)as $ FROM estacionados WHERE fechaHoraSalida <=:hasta");
            $consulta->bindValue(":hasta", $ahora, PDO::PARAM_STR);
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

    //Trae Cantidad de cocheras usadas por tipo entre fechas
    public static function TraerCantidadCocherasTipoFechas($tipo,$desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($desde !="" && $hasta == "") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM estacionados WHERE `fechaHoraIngreso` >=:desde AND `idCochera` in (SELECT id FROM `cocheras` where tipo=:tipo)");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM estacionados WHERE `fechaHoraIngreso` <=:hasta AND `idCochera` in (SELECT id FROM `cocheras` where tipo=:tipo)");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM estacionados WHERE `fechaHoraIngreso` BETWEEN :desde AND :hasta AND `idCochera` in (SELECT id FROM `cocheras` where tipo=:tipo)");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            $ahora= date("Y-m-d 23:59:59");
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT count(*) cant FROM estacionados WHERE `fechaHoraIngreso` <=:hasta AND `idCochera` in (SELECT id FROM `cocheras` where tipo=:tipo)");
            $consulta->bindValue(":hasta", $ahora, PDO::PARAM_STR);
            $consulta->bindValue(":tipo", $tipo, PDO::PARAM_STR);
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

    //Trae cantidad de patentes que ingresaron sin repetir
    public static function TraerTotalPatentesIngresoSinRepetirFechas($desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($desde !="" && $hasta == "") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT COUNT(DISTINCT(patente)) as cant FROM estacionados WHERE `fechaHoraIngreso` >=:desde");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT COUNT(DISTINCT(patente)) as cant FROM estacionados WHERE `fechaHoraIngreso` <=:hasta");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT COUNT(DISTINCT(patente)) as cant FROM estacionados WHERE `fechaHoraIngreso` BETWEEN :desde AND :hasta");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            $ahora= date("Y-m-d 23:59:59");
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT COUNT(DISTINCT(patente)) as cant FROM estacionados WHERE `fechaHoraIngreso` <=:hasta");
            $consulta->bindValue(":hasta", $ahora, PDO::PARAM_STR);
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

    //Trae la cantidad de veces que ingreso una patente
    public static function TraerCantidadPatenteIngresoFechas($patenteAux,$desde,$hasta)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        if ($desde !="" && $hasta == "") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT COUNT(*) as cantVisita FROM estacionados WHERE patente=:patente AND `fechaHoraIngreso` >=:desde");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":patente", $patenteAux, PDO::PARAM_STR);
        }
        if ($desde ==""&& $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT COUNT(*) as cantVisita FROM estacionados WHERE patente=:patente AND`fechaHoraIngreso` <=:hasta");
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->bindValue(":patente", $patenteAux, PDO::PARAM_STR);
        }
        if ($desde !="" && $hasta !="") {
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT COUNT(*) as cantVisita FROM estacionados WHERE patente=:patente AND `fechaHoraIngreso` BETWEEN :desde AND :hasta");
            $consulta->bindValue(":desde", $desde, PDO::PARAM_STR);
            $consulta->bindValue(":hasta", $hasta, PDO::PARAM_STR);
            $consulta->bindValue(":patente", $patenteAux, PDO::PARAM_STR);
        }
        if ($desde =="" && $hasta =="") {
            $ahora= date("Y-m-d 23:59:59");
            $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT COUNT(*) as cantVisita FROM estacionados WHERE patente=:patente AND `fechaHoraIngreso` <=:hasta");
            $consulta->bindValue(":hasta", $ahora, PDO::PARAM_STR);
            $consulta->bindValue(":patente", $patenteAux, PDO::PARAM_STR);
        }  
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

    //Trae el promedio facturado por mes
    public static function TraePromedioImporteMes($mes)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT AVG(importe) as prom,COUNT(*) as cant FROM estacionados WHERE MONTH(fechaHoraSalida)=:mes");
        $consulta->bindValue(":mes", $mes, PDO::PARAM_STR); 
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

    public static function TraePromedioPatenteMes($mes,$patenteAux)
    {
        $objetoAccesoDatos = AccesoDatos::dameUnObjetoAcceso();
        $consulta = $objetoAccesoDatos->RetornarConsulta("SELECT AVG(importe) as prom,COUNT(*) as cant FROM estacionados WHERE MONTH(fechaHoraSalida)=:mes and patente=:patente");
        $consulta->bindValue(":mes", $mes, PDO::PARAM_STR); 
        $consulta->bindValue(":patente", $patenteAux, PDO::PARAM_STR);
        $consulta->setFetchMode(PDO::FETCH_ASSOC);
        $consulta->execute();
        if($consulta->rowCount() == 0){
            return false;   
        }
        return $consulta->fetchAll();
    }

}

?>