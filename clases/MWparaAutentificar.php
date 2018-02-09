<?php

require_once "AutentificadorJWT.php";
class MWparaAutentificar
{
 /**
   * @api {any} /MWparaAutenticar/  Verificar Usuario
   * @apiVersion 0.1.0
   * @apiName VerificarUsuario
   * @apiGroup MIDDLEWARE
   * @apiDescription  Por medio de este MiddleWare verifico las credeciales antes de ingresar al correspondiente metodo 
   *
   * @apiParam {ServerRequestInterface} request  El objeto REQUEST.
 * @apiParam {ResponseInterface} response El objeto RESPONSE.
 * @apiParam {Callable} next  The next middleware callable.
   *
   * @apiExample Como usarlo:
   *    ->add(\MWparaAutenticar::class . ':VerificarUsuario')
   */
	public function VerificarAdmin($request, $response, $next) {
         /*
		  if($request->isGet())
		  {
			 $response->getBody()->write('<p>NO necesita credenciales para los get </p>');
		      //$response = $next($request, $response);
		  }
		  else
		  {
			  */
				$objDelaRespuesta= new stdclass();
				if(isset($request->getHeader('token')[0]))
        		{
            		$arrayConToken = $request->getHeader('token');
            		$token=$arrayConToken[0];
       			} 
        		else 
        		{
            		$objDelaRespuesta->acceso = "Acceso denegado a este sitio falta token.";
            		return $response->withJson($objDelaRespuesta, 403);
				}
				
				try 
        		{
            		AutentificadorJWT::VerificarToken($token);
            		$objDelaRespuesta->esValido=true;
        		} 
        		catch (Exception $e) 
        		{
            		$objDelaRespuesta->esValido=false;
            		$objDelaRespuesta->error = $e->getMessage();
            		return $response->withJson($objDelaRespuesta, 403);
				}
				
				if ($objDelaRespuesta->esValido) 
				{
					$payload=AutentificadorJWT::ObtenerData($token);
			
					if (strtolower($payload->perfil)=="admin") 
					{
          				$response = $next($request, $response);
					} 
					else 
					{
            			$objDelaRespuesta->respuesta="Acceso permitido solo a administradores";
            			return $response->withJson($objDelaRespuesta, 403);
          			}
				} 
		     
		  //}
		  //$response->getBody()->write('<p>vuelvo del verificador de credenciales</p>');
		  return $response;   
	}


	public function VerificarUser($request, $response, $next) {
         
		if(!$request->isGet())
		{
			$objDelaRespuesta= new stdclass();
			if(isset($request->getHeader('token')[0]))
			{
					$arrayConToken = $request->getHeader('token');
					$token=$arrayConToken[0];
			} 
			else 
			{
					$objDelaRespuesta->acceso = "falta token.";
					return $response->withJson($objDelaRespuesta, 403);
			}

			try{
					$datosToken = AutentificadorJWT::ObtenerData($token);
					$request = $request->withAttribute('datos', $datosToken);
					return $next($request, $response);
			}
			catch(Exception $e){
					return $response->withJson("Token invalido: ".$e->getMessage(), 511);
			}
			/*
			if($datosToken->perfil=='user'){	
					$request = $request->withAttribute('datos', $datosToken);
					return $next($request, $response);
			}else{
					return $response->withJson("Esta funcion es solo para administradores",400);
			}*/
		}
		//$response->getBody()->write('<p>vuelvo del verificador de credenciales</p>');
		//return $response;   
}


}