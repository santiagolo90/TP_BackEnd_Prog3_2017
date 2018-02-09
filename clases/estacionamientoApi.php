<?php
include_once "vehiculo.php";
include_once "cochera.php";

class estacionamientoApi
{
    //​ Cuando​ ​ ingresa​ ​ el​ ​ vehículo​ ​ se​ ​ le​ ​ toma​ ​ la​ ​ patente,​ ​ color​ ​ y ​ ​ marca
    public function ingresarAuto($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $arrayConToken = $request->getHeader('token');
        $token=$arrayConToken[0];
		$datosToken = AutentificadorJWT::ObtenerData($token);

		if ($datosToken->estado =="suspendido") {
			 return $response->getBody()->write("Esta suspendido");
		}
		else {
			$patente= $ArrayDeParametros['patente'];
        	$color= $ArrayDeParametros['color'];
			$marca= $ArrayDeParametros['marca'];
			$especial = $ArrayDeParametros['especial'];

			$vehiculoAux = new vehiculo();
			$cocheraAux = new cochera();
			$accion = 'ocupada';
/*		
		if($especial != "si")
		{
			if($cocheraAux = cochera::TraerPrimerCocheraLibreNormal())
			{
			
            
			}
			else
			{
			return $response->withJson("No hay cocheras Disponibles");
			}
		}
		else 
		{
			if($cocheraAux = cochera::TraerPrimerCocheraLibreEspecial())
			{
			$accion = 'ocupada';
			}
			else
			{
			return $response->withJson("No hay cocheras especiales disponibles");
			}
		}
*/

			$existe = vehiculo::ExisteVehiculo2($patente);
			if (empty($existe)) 
			{

				$vehiculoAux->patente = strtolower($patente);
				$vehiculoAux->color = strtolower($color);
				$vehiculoAux->marca = strtolower($marca);
				$vehiculoAux->idEmpleadoIngreso = $datosToken->id;
				$vehiculoAux->fechaHoraIngreso = date("Y-m-d H:i:s");
				$foto = $this->obtenerArchivo($patente);
				
				if($foto != NULL)
				{
					move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
					$vehiculoAux->foto = $foto;
					$cocheraAux = estacionamientoApi::ocuparCochera(strtolower($especial));

						if ($cocheraAux == null) 
						{
							return $response->withJson("No hay cocheras Disponibles o ingreso mal el tipo especial si o no ");
						}
						else 
						{
							$vehiculoAux->idCochera = $cocheraAux->id;
							$vehiculoAux->InsertarVehiculoParametros();
							cochera::OcuparCochera($cocheraAux->id,$accion);
							$response->getBody()->write("El vehiculo con patente ".$patente." se estaciono correctamente en la cochera nº: ".$cocheraAux->numero);
						}

				}
				else
				{
					$response->getBody()->write("Error al guardar vehiculo.");
				}
			}
			else
			{
				$response->getBody()->write("El vehículo con ya esta estacionado");
			}
			return $response;
		}
   
	}
	
	public static function ocuparCochera($especial)
	{
		$c = new cochera();
		if ($especial =="no") {
			$c = cochera::TraerPrimerCocheraLibreNormal();
		}
		if ($especial =="si") {
			$c =  cochera::TraerPrimerCocheraLibreEspecial();
		}
		if ($c == null || $especial !="no" && $especial !="si") {
			return null;
		}
		return $c;

	}

    public function obtenerArchivo($patente) 
	{
        if ( 0 < $_FILES['foto']['error'] ) {
			return null;
		}
		else {
			$foto = $_FILES['foto']['name'];
			
			$extension= explode(".", $foto)  ;

            $nombreNuevo = 'fotosVehiculos/'.$patente.".".$extension[1];
            return $nombreNuevo;
		}
	}
	
	//Cuando sale el vehículo​ se​ ingresa​ la patente y se muestran los datos del vehículo con el importe a pagar.
	public function retirarAuto($request, $response, $args)
	{
		$ArrayDeParametros = $request->getParsedBody();

        $arrayConToken = $request->getHeader('token');
        $token=$arrayConToken[0];
        $datosToken = AutentificadorJWT::ObtenerData($token);

		$patente= $ArrayDeParametros['patente'];
		$vehiculoAux = vehiculo::TraerVehiculoPatente($patente);

		if($vehiculoAux != NULL) {
			//$patenteAux,$auxEmpID,$FHSalidaAux,$tiempoAux,$importeAux
			$fSalida =date("Y-m-d H:i:s");
			$tiempoSeg = (strtotime($fSalida) - strtotime($vehiculoAux->fechaHoraIngreso));
			
			$entrada = new DateTime($vehiculoAux->fechaHoraIngreso);
			$salida = new DateTime($fSalida);

			$arrayFecha = $salida->diff($entrada);
			$tiempoTrans = "Dias: ".$arrayFecha->days." HS: ".$arrayFecha->h;

			$importe = estacionamientoApi::calcularImporte($vehiculoAux->fechaHoraIngreso,$fSalida);

			$salida = vehiculo::RegistarSaludaVehiculo($vehiculoAux->patente,$datosToken->id,$fSalida,$tiempoTrans,$importe);

			$accion = 'libre';
			cochera::OcuparCochera($vehiculoAux->idCochera,$accion);

			$response->getBody()->write("Se registro la salida de la patente: ".$vehiculoAux->patente." Con importe: ".$importe);
		}
		else {
			$response->getBody()->write("Error al retirar vehiculo.");
		}

		return $response;
	}
	
	//public static function calcular($request, $response, $args)
	public static function calcularImporte($fechaEntrada,$fechaSalida)
	{
		//$ArrayDeParametros = $request->getParsedBody();
		//$salidaStr= $ArrayDeParametros['salida'];
		//$entradaStr= $ArrayDeParametros['entrada'];

		$entrada = new DateTime($fechaEntrada);
		$salida = new DateTime($fechaSalida);
		$importe = 0;

		$arrayFecha = $salida->diff($entrada);

		if ($arrayFecha->days >=1 ) {
			$importe = $arrayFecha->days * 170;
		}
		if ($arrayFecha->h ==12  ) {
			$importe = $importe + 90;
		}
		if ($arrayFecha->h >12 && $arrayFecha->h < 24 ) {
			$horasMenor = ($arrayFecha->h - 12) *10;
			$importe = $importe + $horasMenor + 90;
		}
		if ($arrayFecha->h >=1 && $arrayFecha->h < 12 ) {
			$horasMenor = $arrayFecha->h *10;
			$importe = $importe + $horasMenor;
		}
		if ($arrayFecha->i > 5) {
			$importe = $importe +10;
		}
		
		return $importe;
	}

	// A-En​ ​ que​ ​ cochera. B-Hora​ ​ de​ ​ inicio. C-​ ​ Hora​ ​ de​ ​ finalización. D-Cuanto​ ​ pagó
	public function ubicarAuto($request, $response, $args)
	{
		$ArrayDeParametros = $request->getParsedBody();
		$patente= $ArrayDeParametros['patente'];
		if ($patente=="") {
			$objDelaRespuesta->msj = "Debe ingresar una patente";
		}
		else {
		$vehiculoAux = vehiculo::TraerEstacionadosPatente($patente);
		$objDelaRespuesta= new stdclass();
		if ($vehiculoAux !=false ) {
			$objDelaRespuesta->msj = "Movimientos del vehiculo con patente: ".$patente;
			$objDelaRespuesta->v=$vehiculoAux;
			
		}
		else {
			return $response->withJson("No hay registro con esa patente");
		}
	}
		return $response->withJson($objDelaRespuesta);
	}

	// A-En​ ​ que​ ​ cochera. B-Hora​ ​ de​ ​ inicio. C-​ ​ Hora​ ​ de​ ​ finalización. D-Cuanto​ ​ pagó - Por fecha de ingreso
	public function ubicarAutoFechaIngreso($request, $response, $args)
    {
		$ArrayDeParametros = $request->getParsedBody();
		$patente= $ArrayDeParametros['patente'];
		$objDelaRespuesta= new stdclass();
		if ($patente=="") {
			$objDelaRespuesta->msj = "Debe ingresar una patente";
		}
		else {
        $objDelaRespuesta->msj = "Movimientos Ingresos del vehiculo con patente: ".$patente;
         
        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
            $hasta= $ArrayDeParametros['hasta'];
            $objDelaRespuesta->v = vehiculo::TraerEstacionadosPatenteFechaIngreso($patente ,$desde,$hasta);

        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                $desde= $ArrayDeParametros['desde'];
                $objDelaRespuesta->v = vehiculo::TraerEstacionadosPatenteFechaIngreso($patente,$desde,"");

        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                $hasta= $ArrayDeParametros['hasta'];
                $objDelaRespuesta->v = vehiculo::TraerEstacionadosPatenteFechaIngreso($patente,"",$hasta);

        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $objDelaRespuesta->v = vehiculo::TraerEstacionadosPatenteFechaIngreso($patente,"","");
        }
	}
        return $response->withJson($objDelaRespuesta, 200);  
	}
	
	// A-En​ ​ que​ ​ cochera. B-Hora​ ​ de​ ​ inicio. C-​ ​ Hora​ ​ de​ ​ finalización. D-Cuanto​ ​ pagó - Por fecha de Salida
	public function ubicarAutoFechaSalida($request, $response, $args)
    {
		$ArrayDeParametros = $request->getParsedBody();
		$patente= $ArrayDeParametros['patente'];
		$objDelaRespuesta= new stdclass();
		if ($patente=="") {
			$objDelaRespuesta->msj = "Debe ingresar una patente";
		}
		else {
        $objDelaRespuesta->msj = "Movimientos Salidas del vehiculo con patente: ".$patente;
         
        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
            $hasta= $ArrayDeParametros['hasta'];
            $objDelaRespuesta->v = vehiculo::TraerEstacionadosPatenteFechaSalida($patente ,$desde,$hasta);

        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                $desde= $ArrayDeParametros['desde'];
                $objDelaRespuesta->v = vehiculo::TraerEstacionadosPatenteFechaSalida($patente,$desde,"");

        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                $hasta= $ArrayDeParametros['hasta'];
                $objDelaRespuesta->v = vehiculo::TraerEstacionadosPatenteFechaSalida($patente,"",$hasta);

        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $objDelaRespuesta->v = vehiculo::TraerEstacionadosPatenteFechaSalida($patente,"","");
        }
	}
        return $response->withJson($objDelaRespuesta, 200);   
	}
	
	// A-En​ ​ que​ ​ cochera. B-Hora​ ​ de​ ​ inicio. C-​ ​ Hora​ ​ de​ ​ finalización. D-Cuanto​ ​ pagó - Ingreso y Salida juntos
	public function ubicarAutoFecha($request, $response, $args)
    {
		$ArrayDeParametros = $request->getParsedBody();
		$patente= $ArrayDeParametros['patente'];
		$objDelaRespuesta= new stdclass();
		if ($patente=="") {
			$objDelaRespuesta->msj = "Debe ingresar una patente";
		}
		else {
			
		
        $objDelaRespuesta->msj = "Movimientos del vehiculo con patente: ".$patente;
         
        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
			$hasta= $ArrayDeParametros['hasta'];
			$objDelaRespuesta->ingreso = vehiculo::TraerEstacionadosPatenteFechaIngreso($patente ,$desde,$hasta);
            $objDelaRespuesta->salida = vehiculo::TraerEstacionadosPatenteFechaSalida($patente ,$desde,$hasta);

        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
				$desde= $ArrayDeParametros['desde'];
				$objDelaRespuesta->ingreso = vehiculo::TraerEstacionadosPatenteFechaIngreso($patente ,$desde,"");
                $objDelaRespuesta->salida = vehiculo::TraerEstacionadosPatenteFechaSalida($patente,$desde,"");

        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
				$hasta= $ArrayDeParametros['hasta'];
				$objDelaRespuesta->ingreso = vehiculo::TraerEstacionadosPatenteFechaIngreso($patente ,"",$hasta);
                $objDelaRespuesta->salida = vehiculo::TraerEstacionadosPatenteFechaSalida($patente,"",$hasta);

        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
			$objDelaRespuesta->ingreso = vehiculo::TraerEstacionadosPatenteFechaIngreso($patente ,"","");
            $objDelaRespuesta->salida = vehiculo::TraerEstacionadosPatenteFechaSalida($patente,"","");
		}
		if ($objDelaRespuesta->ingreso == false ) 
            {
                $objDelaRespuesta->ingreso =("No hay movimientos en esas fechas");
            }     
        if ($objDelaRespuesta->salida == false) {
            $objDelaRespuesta->salida =("No hay movimientos en esas fechas");
        }  
	}
        return $response->withJson($objDelaRespuesta, 200);   
	}
	
	//a-0.50%​ ​ facturación​ ​ - ​ ​ cantidad​ ​ de​ ​ vehículo
	public function facturacionFechas($request, $response, $args)
	{
		$ArrayDeParametros = $request->getParsedBody();
		//$objDelaRespuesta= new stdclass();
		$total = array();
		$respuesta ="";

        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
			$hasta= $ArrayDeParametros['hasta'];
			//$objDelaRespuesta->msj = "Facturacion desde ".$desde." hasta ".$hasta;
			$total = vehiculo::TraerFacturacionFechas($desde,$hasta);
			
			$respuesta ="Facturacion desde ".$desde." hasta ".$hasta ." la cantidad de Vehiculos: ".$total[0]['CantVehiculos']." Por un total de $".$total[0]['$'];
        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
			$desde= $ArrayDeParametros['desde'];
			//$objDelaRespuesta->msj = "Facturacion desde ".$desde." hasta hoy";
			$total = vehiculo::TraerFacturacionFechas($desde,"");
			$respuesta = "Facturacion desde ".$desde." hasta hoy la cantidad de Vehiculos: ".$total[0]['CantVehiculos']." Por un total de $".$total[0]['$'];

        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
			$hasta= $ArrayDeParametros['hasta'];
			//$objDelaRespuesta->msj = "Facturacion desde el inicio de actividades hasta ".$hasta;
			$total = vehiculo::TraerFacturacionFechas("",$hasta);
			$respuesta = "Facturacion desde el inicio de actividades hasta ".$hasta." la cantidad de Vehiculos: ".$total[0]['CantVehiculos']." Por un total de $".$total[0]['$'];

        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
			//$objDelaRespuesta->msj = "Facturacion desde el inicio de actividades hasta hoy";
			$total= vehiculo::TraerFacturacionFechas("","");
			$respuesta = "Facturacion desde el inicio de actividades hasta hoy la cantidad de Vehiculos: ".$total[0]['CantVehiculos']." Por un total de $".$total[0]['$'];
		}
		if ($total[0]['CantVehiculos'] == 0) {
			return $response->withJson("No hubo facturacion entre las fechas seleccionadas");
		}
		else
		{
			return $response->withJson($respuesta);
		}
	}

	//b-0.75%​ ​ usos​ ​ de​ ​ cocheras​ ​ para​ ​ discapacitados​ ​ y ​ ​ no​ ​ .
	public function usoTipoCocheraFechas($request, $response, $args)
	{
		$ArrayDeParametros = $request->getParsedBody();
		$objDelaRespuesta= new stdclass();
		$objDelaRespuesta->msj = "Usos de cocheras por tipo";
        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
			$hasta= $ArrayDeParametros['hasta'];
			$objDelaRespuesta->especial = vehiculo::TraerCantidadCocherasTipoFechas("especial",$desde,$hasta);
            $objDelaRespuesta->normal = vehiculo::TraerCantidadCocherasTipoFechas("normal",$desde,$hasta);

        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
				$desde= $ArrayDeParametros['desde'];
				$objDelaRespuesta->especial = vehiculo::TraerCantidadCocherasTipoFechas("especial" ,$desde,"");
                $objDelaRespuesta->normal = vehiculo::TraerCantidadCocherasTipoFechas("normal",$desde,"");

        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
				$hasta= $ArrayDeParametros['hasta'];
				$objDelaRespuesta->especial = vehiculo::TraerCantidadCocherasTipoFechas("especial" ,"",$hasta);
                $objDelaRespuesta->normal = vehiculo::TraerCantidadCocherasTipoFechas("normal","",$hasta);

        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
			$objDelaRespuesta->especial = vehiculo::TraerCantidadCocherasTipoFechas("especial" ,"","");
            $objDelaRespuesta->normal = vehiculo::TraerCantidadCocherasTipoFechas("normal","","");
		}
		
		if ($objDelaRespuesta->especial[0]['cant'] == 0 ) 
            {
                $objDelaRespuesta->especial =("No hay movimientos en esas fechas");
            }     
        if ($objDelaRespuesta->normal[0]['cant']  == false) {
            $objDelaRespuesta->normal =("No hay movimientos en esas fechas");
        }  
	
        return $response->withJson($objDelaRespuesta, 200); 
	}
	
	//c-100%​ ​ cuántos​ ​ vehículos​ ​ sin repetir(distintos​ ​ se​ ​ estacionaron)
	public function cantidadesIngresosPatenteSinRepetir($request, $response, $args)
	{
		$ArrayDeParametros = $request->getParsedBody();
		$total = array();
		$respuesta ="";

        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
			$hasta= $ArrayDeParametros['hasta'];
			$total = vehiculo::TraerTotalPatentesIngresoSinRepetirFechas($desde,$hasta);
			
			$respuesta ="Desde ".$desde." hasta ".$hasta ." ingresaron la cantidad de Vehiculos: ".$total[0]['cant'];
        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
			$desde= $ArrayDeParametros['desde'];
			$total = vehiculo::TraerTotalPatentesIngresoSinRepetirFechas($desde,"");
			$respuesta = "Desde ".$desde." hasta hoy ingresaron la cantidad de Vehiculos: ".$total[0]['cant'];

        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
			$hasta= $ArrayDeParametros['hasta'];
			$total = vehiculo::TraerTotalPatentesIngresoSinRepetirFechas("",$hasta);
			$respuesta = "Desde el inicio de actividades hasta ".$hasta." ingresaron la cantidad de Vehiculos: ".$total[0]['cant'];

        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
			$total= vehiculo::TraerTotalPatentesIngresoSinRepetirFechas("","");
			$respuesta = "Desde el inicio de actividades hasta hoy ingresaron la cantidad de Vehiculos: ".$total[0]['cant'];
		}
		if ($total[0]['cant'] == 0) {
			return $response->withJson("No hubo ingresos entre las fechas seleccionadas");
		}
		else
		{
			return $response->withJson($respuesta);
		}

	}

	//c-100%​ ​ cantidades​ ​ de​ ​ veces​ ​ que​ ​ vino​ ​ el​ ​ mismo​ ​ vehículo,
	public function cantidadesIngresosMismaPatenteFechas($request, $response, $args)
	{
		$ArrayDeParametros = $request->getParsedBody();
		$patente= $ArrayDeParametros['patente'];
		$total = array();
		$respuesta ="";

        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
			$hasta= $ArrayDeParametros['hasta'];
			$total = vehiculo::TraerCantidadPatenteIngresoFechas($patente,$desde,$hasta);
			
			$respuesta ="Desde ".$desde." hasta ".$hasta ." el vehiculo con patente: ".$patente." ingreso ".$total[0]['cantVisita']." veces";
        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
			$desde= $ArrayDeParametros['desde'];
			$total = vehiculo::TraerCantidadPatenteIngresoFechas($patente,$desde,"");
			$respuesta = "Desde ".$desde." hasta hoy el vehiculo con patente: ".$patente." ingreso ".$total[0]['cantVisita']." veces";

        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
			$hasta= $ArrayDeParametros['hasta'];
			$total = vehiculo::TraerCantidadPatenteIngresoFechas($patente,"",$hasta);
			$respuesta = "Desde el inicio de actividades hasta ".$hasta." el vehiculo con patente: ".$patente." ingreso ".$total[0]['cantVisita']." veces";

        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
			$total= vehiculo::TraerCantidadPatenteIngresoFechas($patente,"","");
			$respuesta = "Desde el inicio de actividades hasta hoy el vehiculo con patente: ".$patente." ingreso ".$total[0]['cantVisita']." veces";
		}
		if ($total[0]['cantVisita'] == 0) {
			return $response->withJson("No hubo ingresos entre las fechas seleccionadas");
		}
		else
		{
			return $response->withJson($respuesta);
		}

	}

	//11-(2pt)​ Promedio​ mensual​ de​ datos: importe
	public function promedioImporteMes($request, $response, $args)
	{
		$ArrayDeParametros = $request->getParsedBody();
		$mes= $ArrayDeParametros['mes'];
		$respuesta;
		if ($mes >= 1 && $mes <= 12) {
			$promedio = vehiculo::TraePromedioImporteMes($mes);
			if ($mes == 1) {
				$mes2 = "enero";
			}
			if ($mes == 2) {
				$mes2 = "febrero";
			}
			if ($mes == 3) {
				$mes2 = "marzo";
			}
			if ($mes == 4) {
				$mes2 = "abril";
			}
			if ($mes == 5) {
				$mes2 = "mayo";
			}
			if ($mes == 6) {
				$mes2 = "junio";
			}
			if ($mes == 7) {
				$mes2 = "julio";
			}
			if ($mes == 8) {
				$mes2 = "agosto";
			}
			if ($mes == 9) {
				$mes2 = "septiembre";
			}
			if ($mes == 10) {
				$mes2 = "octubre";
			}
			if ($mes == 11) {
				$mes2 = "noviembre";
			}
			if ($mes == 12) {
				$mes2 = "diciembre";
			}
			if ($promedio[0]['prom'] =="") {
				$promedio[0]['prom'] = 0;
			}

			$respuesta = "El promedio facturado en ".$mes2." es de $".$promedio[0]['prom'];
		}
		else {
			$respuesta ="Error: debe ingresar un numero entre 1 y 12";
		}
		return $response->withJson($respuesta, 200); 

	}

	//11-(2pt)​ Promedio​ mensual​ de​ datos: Patente
	public function promedioPatenteMes($request, $response, $args)
	{
		$ArrayDeParametros = $request->getParsedBody();
		$mes= $ArrayDeParametros['mes'];
		$patente= $ArrayDeParametros['patente'];
		$respuesta;
		if ($mes >= 1 && $mes <= 12) {
			$promedio = vehiculo::TraePromedioPatenteMes($mes,$patente);
			if ($mes == 1) {
				$mes2 = "enero";
			}
			if ($mes == 2) {
				$mes2 = "febrero";
			}
			if ($mes == 3) {
				$mes2 = "marzo";
			}
			if ($mes == 4) {
				$mes2 = "abril";
			}
			if ($mes == 5) {
				$mes2 = "mayo";
			}
			if ($mes == 6) {
				$mes2 = "junio";
			}
			if ($mes == 7) {
				$mes2 = "julio";
			}
			if ($mes == 8) {
				$mes2 = "agosto";
			}
			if ($mes == 9) {
				$mes2 = "septiembre";
			}
			if ($mes == 10) {
				$mes2 = "octubre";
			}
			if ($mes == 11) {
				$mes2 = "noviembre";
			}
			if ($mes == 12) {
				$mes2 = "diciembre";
			}
			if ($promedio[0]['prom'] =="") {
				$promedio[0]['prom'] = 0;
			}

			$respuesta = "El promedio facturado en ".$mes2." por la pantente ".$patente." es de $".$promedio[0]['prom'];
		}
		else {
			$respuesta ="Error: debe ingresar un numero entre 1 y 12";
		}
		return $response->withJson($respuesta, 200); 

	}
	
}



?>