<?php
include_once "cochera.php";

class cocheraApi// extends empleado
{
    //CargoUno
    public function CargarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        
        $piso= $ArrayDeParametros['piso'];
        $numero= $ArrayDeParametros['numero'];
        $estado= $ArrayDeParametros['estado'];
        $tipo= $ArrayDeParametros['tipo'];
        
        $cocheraAux = new cochera();

        $cocheraAux->piso = $piso;
        $cocheraAux->numero = $numero;
        $cocheraAux->estado = strtolower($estado);
        $cocheraAux->tipo = strtolower($tipo);

        $c = cochera::TraerCocheraNumero($cocheraAux->numero);
        if ($c == false) {
            if($cocheraAux->InsertarEmpleadoParametros() != null)
            {
                $response->getBody()->write("Se dio de alta la cochera nº: ".$numero);
            }
            else
            {
                $response->getBody()->write("Error al guardar cochera.");
            }
        }
        else
        {
            $response->getBody()->write("El numero de cochera ya existe.");
        }

        return $response;  
    }

    //TraigoTodos
    public function traerTodos($request, $response, $args) 
	{
			$todasCocheras = cochera::TraerTodasLasCocheras();
			$response = $response->withJson($todasCocheras, 200);  
			return $response;
    }
    
    //Borra
    public function BorrarUno($request, $response, $args) 
    {
            $ArrayDeParametros = $request->getParsedBody(); //para delete urlencoded
            $numero=$ArrayDeParametros['numero'];
            $cocheraBorrar = cochera::TraerCocheraNumero($numero);
            $objDelaRespuesta= new stdclass();
            if ($cocheraBorrar != false) {
                if(cochera::BorrarCocheraID($cocheraBorrar->id)>0)
                {       
                    $objDelaRespuesta->resultado="Se borro con exito a : ".$cocheraBorrar->numero."";
                }
                else
                {
                    $objDelaRespuesta->resultado="Error al Borrar la cochera";
                }
            }
            else {
                $objDelaRespuesta->resultado="No existe el numero de la cochera";
            }
            return $response->withJson($objDelaRespuesta, 200);  
    }

    //Modifica
    public function modificarUno($request, $response, $args) 
    {
            $ArrayDeParametros = $request->getParsedBody();
            $id= $ArrayDeParametros['id'];
            $objDelaRespuesta= new stdclass();
            $cocheraModificar = cochera::TraerCocheraID($id);

            if ($cocheraModificar != false) {
                $objDelaRespuesta->msj = "se modifico cochera con id ".$id;
                if (isset($ArrayDeParametros['piso'])) {
                    $piso = $ArrayDeParametros['piso'];
                    $cocheraModificar->piso = $piso;
                    $cocheraModificar->ModificarCochera($id);
                    $objDelaRespuesta->piso =$piso;
                }
                if (isset($ArrayDeParametros['numero'])) {
                    $numero = $ArrayDeParametros['numero'];
                    $cocheraModificar->numero = $numero;
                    $cocheraModificar->ModificarCochera($id);
                    $objDelaRespuesta->numero =$numero;
                }
                if (isset($ArrayDeParametros['estado'])) {
                    $estado = strtolower($ArrayDeParametros['estado']);
                    $cocheraModificar->estado = $estado;
                    $cocheraModificar->ModificarCochera($id);
                    $objDelaRespuesta->estado =$estado;
                }
                if (isset($ArrayDeParametros['tipo'])) {
                    $tipo = strtolower($ArrayDeParametros['tipo']);
                    $cocheraModificar->tipo = $tipo;
                    $cocheraModificar->ModificarCochera($id);
                    $objDelaRespuesta->tipo =$tipo;
                }
            }
            else {
                $objDelaRespuesta->error = "Error no existe el ID de la cochera";
            }
            return $response->withJson($objDelaRespuesta, 200);
            
    }

    //Trae cocheras mas usadas por ingresos y salidas 
    public function traerMax($request, $response, $args) 
    {
        $objDelaRespuesta->msj = "Cochera mas usada";
        $cocheraMax = cochera::TraerMasUtilizada();
        $maximo = $cocheraMax[0]['cant'];
        
        for ($i=0; $i < count($cocheraMax) ; $i++) {
            if ($cocheraMax[$i]['cant'] == $maximo) {
                $objDelaRespuesta->cochera[$i] =$cocheraMax[$i];
            }
            
        }
        return $response->withJson($objDelaRespuesta, 200);
    }

    //Trae cocheras mas usadas por ingresos
    public function traerMaxFechasIngreso($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        $objDelaRespuesta->msj = "Cocheras Con mas Ingresos";
         
        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
            $hasta= $ArrayDeParametros['hasta'];
            $cocheraMax = cochera::TraerMasUtilizadaFechaIngreso($desde,$hasta);
            $maximo = $cocheraMax[0]['cant'];
        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                $desde= $ArrayDeParametros['desde'];
                $cocheraMax = cochera::TraerMasUtilizadaFechaIngreso($desde,"");
                $maximo = $cocheraMax[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                $hasta= $ArrayDeParametros['hasta'];
                $cocheraMax = cochera::TraerMasUtilizadaFechaIngreso("",$hasta);
                $maximo = $cocheraMax[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $cocheraMax = cochera::TraerMasUtilizadaFechaIngreso("","");
            $maximo = $cocheraMax[0]['cant'];
        }
        for ($i=0; $i < count($cocheraMax) ; $i++) 
            {
                if ($cocheraMax[$i]['cant'] == $maximo) {
                $objDelaRespuesta->cochera[$i] =$cocheraMax[$i];
                }
            
            }
        return $response->withJson($objDelaRespuesta, 200);

        
    }

    //Trae cocheras mas usadas por salidas
    public function traerMaxFechasSalida($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        $objDelaRespuesta->msj = "Cocheras Con mas Salidas";
         
        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
            $hasta= $ArrayDeParametros['hasta'];
            $cocheraMax = cochera::TraerMasUtilizadaFechaSalida($desde,$hasta);
            $maximo = $cocheraMax[0]['cant'];
        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                $desde= $ArrayDeParametros['desde'];
                $cocheraMax = cochera::TraerMasUtilizadaFechaSalida($desde,"");
                $maximo = $cocheraMax[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                $hasta= $ArrayDeParametros['hasta'];
                $cocheraMax = cochera::TraerMasUtilizadaFechaSalida("",$hasta);
                $maximo = $cocheraMax[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $cocheraMax = cochera::TraerMasUtilizadaFechaSalida("","");
            $maximo = $cocheraMax[0]['cant'];
        }
        for ($i=0; $i < count($cocheraMax) ; $i++) 
            {
                if ($cocheraMax[$i]['cant'] == $maximo) {
                $objDelaRespuesta->cochera[$i] =$cocheraMax[$i];
                }
            
            }
        if ($cocheraMax == false) {
            return $response->withJson("No hay movimientos en esas fechas");
        }    
        return $response->withJson($objDelaRespuesta, 200);
    }

    //Igual que traerMaxFechasIngreso traerMaxFechasSalida pero en una sola funcion
    public function traerMaxFechas($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        $objDelaRespuesta->msj = "Cocheras Con mas ingresos y salidas";
         
        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
            $hasta= $ArrayDeParametros['hasta'];
            $cocheraMaxIngreso = cochera::TraerMasUtilizadaFechaIngreso($desde,$hasta);
            $cocheraMaxSalidas = cochera::TraerMasUtilizadaFechaSalida($desde,$hasta);
            $maximoIngreso = $cocheraMaxIngreso[0]['cant'];
            $maximoSalida = $cocheraMaxSalidas[0]['cant'];
        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $desde= $ArrayDeParametros['desde'];
            $cocheraMaxIngreso = cochera::TraerMasUtilizadaFechaIngreso($desde,"");
            $cocheraMaxSalidas = cochera::TraerMasUtilizadaFechaSalida($desde,"");
            $maximoIngreso = $cocheraMaxIngreso[0]['cant'];
            $maximoSalida = $cocheraMaxSalidas[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
            $hasta= $ArrayDeParametros['hasta'];
            $cocheraMaxIngreso = cochera::TraerMasUtilizadaFechaIngreso("",$hasta);
            $cocheraMaxSalidas = cochera::TraerMasUtilizadaFechaSalida("",$hasta);
            $maximoIngreso = $cocheraMaxIngreso[0]['cant'];
            $maximoSalida = $cocheraMaxSalidas[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $cocheraMax = cochera::TraerMasUtilizada();
            $maximo = $cocheraMax[0]['cant'];
        
            for ($i=0; $i < count($cocheraMax) ; $i++) {
                if ($cocheraMax[$i]['cant'] == $maximo) {
                    $objDelaRespuesta->cochera[$i] =$cocheraMax[$i];
                }
            
            }
            
            return $response->withJson($objDelaRespuesta, 200);
            
        }
        for ($i=0; $i < count($cocheraMaxIngreso) ; $i++) 
            {
                if ($cocheraMaxIngreso[$i]['cant'] == $maximoIngreso) {
                $objDelaRespuesta->cocheraIngreso[$i] =$cocheraMaxIngreso[$i];
                }
            
            }
        for ($i=0; $i < count($cocheraMaxSalidas) ; $i++) 
            {
                if ($cocheraMaxSalidas[$i]['cant'] == $maximoSalida) {
                $objDelaRespuesta->cocheraSalidas[$i] =$cocheraMaxSalidas[$i];
                }
            
            }
        if ($cocheraMaxIngreso == false ) 
            {
                $objDelaRespuesta->cocheraIngreso =("No hay movimientos en esas fechas");
            }     
        if ($cocheraMaxSalidas == false) {
            $objDelaRespuesta->cocheraSalidas =("No hay movimientos en esas fechas");
        }    
        return $response->withJson($objDelaRespuesta, 200);
    }

    //Trae cocheras menos usadas por ingresos y salidas 
    public function traerMin($request, $response, $args) 
    {
        $objDelaRespuesta->msj = "Cochera menos usada";
        $cocheraMin = cochera::TraerMenosUtilizada();
        $minimo = $cocheraMin[0]['cant'];

        for ($i=0; $i <count($cocheraMin) ; $i++) { 
            if ($cocheraMin[$i]['cant'] == $minimo) {
                $objDelaRespuesta->cochera[$i] =$cocheraMin[$i];
            }
        }
        return $response->withJson($objDelaRespuesta, 200);

    }

    //Trae cocheras menos usadas por ingresos
    public function traerMinFechasIngreso($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        $objDelaRespuesta->msj = "Cocheras con Menos Ingresos";
         
        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
            $hasta= $ArrayDeParametros['hasta'];
            $cocheraMin = cochera::TraerMenosUtilizadaFechaIngreso($desde,$hasta);
            $minimo = $cocheraMin[0]['cant'];
        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                $desde= $ArrayDeParametros['desde'];
                $cocheraMin = cochera::TraerMenosUtilizadaFechaIngreso($desde,"");
                $minimo = $cocheraMin[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                $hasta= $ArrayDeParametros['hasta'];
                $cocheraMin = cochera::TraerMenosUtilizadaFechaIngreso("",$hasta);
                $minimo = $cocheraMin[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $cocheraMin = cochera::TraerMenosUtilizadaFechaIngreso("","");
            $minimo = $cocheraMin[0]['cant'];
        }
        for ($i=0; $i < count($cocheraMin) ; $i++) 
            {
                if ($cocheraMin[$i]['cant'] == $minimo) {
                $objDelaRespuesta->cochera[$i] =$cocheraMin[$i];
                }
            
            }
        return $response->withJson($objDelaRespuesta, 200);

        
    }

    //Trae cocheras mas usadas por salidas
    public function traerMinFechasSalida($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        $objDelaRespuesta->msj = "Cocheras Con menos Salidas";
         
        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
            $hasta= $ArrayDeParametros['hasta'];
            $cocheraMin = cochera::TraerMenosUtilizadaFechaSalida($desde,$hasta);
            $minimo = $cocheraMin[0]['cant'];
        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                $desde= $ArrayDeParametros['desde'];
                $cocheraMin = cochera::TraerMenosUtilizadaFechaSalida($desde,"");
                $minimo = $cocheraMin[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                $hasta= $ArrayDeParametros['hasta'];
                $cocheraMin = cochera::TraerMenosUtilizadaFechaSalida("",$hasta);
                $minimo = $cocheraMin[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $cocheraMin = cochera::TraerMenosUtilizadaFechaSalida("","");
            $minimo = $cocheraMin[0]['cant'];
        }
        for ($i=0; $i < count($cocheraMin) ; $i++) 
            {
                if ($cocheraMin[$i]['cant'] == $minimo) {
                $objDelaRespuesta->cochera[$i] =$cocheraMin[$i];
                }
            
            }
        if ($cocheraMin == false) {
            return $response->withJson("No hay movimientos en esas fechas");
        }    
        return $response->withJson($objDelaRespuesta, 200);
    }

    //Igual que traerMinFechasIngreso traerMinFechasSalida pero en una sola funcion
    public function traerMinFechas($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        $objDelaRespuesta->msj = "Cocheras con menos ingresos y salidas";
         
        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
            $hasta= $ArrayDeParametros['hasta'];
            $cocheraMinIngreso = cochera::TraerMenosUtilizadaFechaIngreso($desde,$hasta);
            $cocheraMinSalidas = cochera::TraerMenosUtilizadaFechaSalida($desde,$hasta);
            $minimoIngreso = $cocheraMinIngreso[0]['cant'];
            $minimoSalida = $cocheraMinSalidas[0]['cant'];
        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $desde= $ArrayDeParametros['desde'];
            $cocheraMinIngreso = cochera::TraerMenosUtilizadaFechaIngreso($desde,"");
            $cocheraMinSalidas = cochera::TraerMenosUtilizadaFechaSalida($desde,"");
            $minimoIngreso = $cocheraMinIngreso[0]['cant'];
            $minimoSalida = $cocheraMinSalidas[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
            $hasta= $ArrayDeParametros['hasta'];
            $cocheraMinIngreso = cochera::TraerMenosUtilizadaFechaIngreso("",$hasta);
            $cocheraMinSalidas = cochera::TraerMenosUtilizadaFechaSalida("",$hasta);
            $minimoIngreso = $cocheraMinIngreso[0]['cant'];
            $minimoSalida = $cocheraMinSalidas[0]['cant'];
        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $ahora= date("Y-m-d");
            $cocheraMinIngreso = cochera::TraerMenosUtilizadaFechaIngreso("",$ahora);
            $cocheraMinSalidas = cochera::TraerMenosUtilizadaFechaSalida("",$ahora);
            $minimoIngreso = $cocheraMinIngreso[0]['cant'];
            $minimoSalida = $cocheraMinSalidas[0]['cant'];
            
        }

        for ($i=0; $i < count($cocheraMinIngreso) ; $i++) 
            {
                if ($cocheraMinIngreso[$i]['cant'] == $minimoIngreso) {
                $objDelaRespuesta->cocheraIngreso[$i] =$cocheraMinIngreso[$i];
                }
            
            }
        for ($i=0; $i < count($cocheraMinSalidas) ; $i++) 
            {
                if ($cocheraMinSalidas[$i]['cant'] == $minimoSalida) {
                $objDelaRespuesta->cocheraSalidas[$i] =$cocheraMinSalidas[$i];
                }
            
            }
        if ($cocheraMinIngreso == false ) 
            {
                $objDelaRespuesta->cocheraIngreso =("No hay movimientos en esas fechas");
            }     
        if ($cocheraMinSalidas == false) {
            $objDelaRespuesta->cocheraSalidas =("No hay movimientos en esas fechas");
        }    
        return $response->withJson($objDelaRespuesta, 200);
    }


    public function traerNunca($request, $response, $args) 
    {
        $objDelaRespuesta->msj = "Cocheras que nunca se usaron";
        $objDelaRespuesta->cochera = cochera::TraerNuncaUtilizada();
        return $response->withJson($objDelaRespuesta, 200);

    }

    //Trae cocheras menos usadas por ingresos
    public function traerNuncaFechaIngreso($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        $objDelaRespuesta->msj = "Cocheras que nunca se usaron entre fechas";
         
        if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) 
        {
            $desde= $ArrayDeParametros['desde'];
            $hasta= $ArrayDeParametros['hasta'];
            $objDelaRespuesta->cochera = cochera::TraerNuncaUtilizada($desde,$hasta);

        }
        if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                $desde= $ArrayDeParametros['desde'];
                $objDelaRespuesta->cochera = cochera::TraerNuncaUtilizada($desde,"");

        }
        if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                $hasta= $ArrayDeParametros['hasta'];
                $objDelaRespuesta->cochera = cochera::TraerNuncaUtilizada("",$hasta);

        }
        if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
            $objDelaRespuesta->cochera = cochera::TraerNuncaUtilizada("","");
        }

        return $response->withJson($objDelaRespuesta, 200);

        
    }



}
