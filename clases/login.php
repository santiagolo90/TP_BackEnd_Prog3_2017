<?php
use \Firebase\JWT\JWT;
require_once 'vendor/autoload.php';
require_once 'empleado.php';

class login
{

    public static function existeEmpleado($email,$clave)
    {
        if(empleado::TraerEmpleadoEmail($email) !== NULL)
        {
            $objetoAccesoDato = AccesoDatos::dameUnObjetoAcceso(); 
            $consulta =$objetoAccesoDato->RetornarConsulta("select nombre,sexo,email,clave,turno,perfil from empleado where email = '$email' AND clave = '$clave'");
            $consulta->execute();
            $empLogin= $consulta->fetchObject('empleado');
            if($empLogin != NULL)
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        else
        {
            print_r("no se encontro Empleado");
        }

    }


}

?>