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
            $usuarioBuscado = empleado::TraerEmpleadoEmailClave($email,$clave);
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
}