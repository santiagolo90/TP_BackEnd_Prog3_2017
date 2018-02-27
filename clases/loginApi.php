<?php
require_once 'empleado.php';
require_once 'AutentificadorJWT.php';
/*
5- (2pts) (POST) Ingresar ID y si este existe devolver un JWT con todos los datos de la bicicleta.
*/
class loginApi
{

    public function login($request, $response, $args) 
    {
        $token="";
        $ArrayDeParametros = $request->getParsedBody();
        
        if(isset( $ArrayDeParametros['email'])&& isset( $ArrayDeParametros['clave']) )
        {
            $email = $ArrayDeParametros['email'];
            $clave = $ArrayDeParametros['clave'];
            
            $empAux = empleado::TraerEmpleadoEmail($email);
            $claveValida = password_verify($clave, $empAux->clave);
            if ($claveValida) {

                $usuarioBuscado = empleado::TraerEmpleadoEmail($email);
            }
            else {
                 $usuarioBuscado = false;
                 $empAux =false;
            }
            //var_dump($usuarioBuscado);
            $objRespuesta = new stdClass();
            //$objRespuesta->Datos= null;
            $objRespuesta->msj = null;
            $objRespuesta->Token = null;
               
                if($usuarioBuscado)
                {
                    if ($usuarioBuscado->estado != "suspendido") 
                    {
                    
                        $token= AutentificadorJWT::CrearToken(array(
                            'id'=> $usuarioBuscado->id,
                            'nombre'=> $usuarioBuscado->nombre,
                            'sexo'=> $usuarioBuscado->sexo,
                            'email'=> $usuarioBuscado->email,
                            'turno'=> $usuarioBuscado->turno,
                            'perfil'=> $usuarioBuscado->perfil,
                            'foto'=> $usuarioBuscado->foto,
                            'alta'=> $usuarioBuscado->alta,
                            'estado'=> $usuarioBuscado->estado));

                        $datos= AutentificadorJWT::ObtenerData($token);
                        //$objRespuesta->Token = $token;
                        //$objRespuesta->Datos =$datos;
                        $f= date("Y-m-d");
                        $h= date("H:i:s");
                        historico::registrarLogin($usuarioBuscado->id,$f,$h);
                        $objRespuesta->msj ="Bienvenido ".$datos->nombre;
                        $objRespuesta->Token = $token;
                        return $response->withJson($objRespuesta ,200);
                    }
                    else {
                        return $response->withJson("Usuario Suspendido");
                    }
                }
                else
                {
                    return $response->withJson("Error en email o clave");
                    //$newResponse = $response->withJson( $retorno ,409); 
                }
        }
        else
        {
            return $response->withJson("Falta email y clave");
        }
    }

    public function datosToken($request, $response, $args) {
        $arrayConToken = $request->getHeader('token');
        $token=$arrayConToken[0];
        try{
			$datosToken = AutentificadorJWT::ObtenerData($token);
		}
		catch(Exception $e){
			return $response->withJson($e->getMessage(), 511);
		}
		return $response->withJson( $datosToken ,200);

    }

}