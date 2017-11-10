<?php
use \Firebase\JWT\JWT;
require_once 'vendor/autoload.php';
require_once 'empleado.php';


class loginApi extends login
{
    public function VerificarLoginApi($request, $response)
    {
        $ArrayDeParametros = $request->getParsedBody();
        //error aca
        $email = $ArrayDeParametros['email'];
        $clave = $ArrayDeParametros['clave'];
        $newResponse = login::existeEmpleado($email,$clave);
        if($newResponse == True)
        {
            return AutentificadorJWT::CrearToken($clave);
        }
        else
        {
            return "No se encontro usuario o contraseña";
        }
        $response->getBody()->write($newResponse);
        return $response;
    }
}