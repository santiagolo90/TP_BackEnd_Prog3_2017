<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require_once 'vendor/autoload.php';
require_once 'clases/AccesoDatos.php';
require_once 'clases/empleadoApi.php';
require_once 'clases/cocheraApi.php';
require_once 'clases/estacionamientoApi.php';
require_once 'clases/loginApi.php';
require_once 'clases/MWparaCORS.php';
require_once 'clases/MWparaAutentificar.php';


$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

/*
¡La primera línea es la más importante! A su vez en el modo de 
desarrollo para obtener información sobre los errores
 (sin él, Slim por lo menos registrar los errores por lo que si está utilizando
  el construido en PHP webserver, entonces usted verá en la salida de la consola 
  que es útil).

  La segunda línea permite al servidor web establecer el encabezado Content-Length, 
  lo que hace que Slim se comporte de manera más predecible.
*/

$app = new \Slim\App(["settings" => $config]);

$app->get('[/]', function (Request $request, Response $response) {    
  $response->getBody()->write("Bienvenido!!!");
  return $response;

})->add(\MWparaCORS::class . ':HabilitarCORSTodos');
//(POST email y clave)
$app->post('/Login[/]', \loginApi::class . ':login')->add(\MWparaCORS::class . ':HabilitarCORSTodos');


$app->group('/empleado', function () {

    //7-De​ los empleados c- alta suspenderlos​ y borrarlos (POST nombre sexo email clave turno perfil estado) (FILES foto)
    $this->post('/alta[/]', \empleadoApi::class . ':CargarUno');
    //Trae todo ( si le agrega una letra a args trae solo los suspendidos)
    $this->get('/[{suspendidos}]', \empleadoApi::class . ':traerTodos');
    //(POST id)
    $this->post('/borrar[/]', \empleadoApi::class . ':BorrarUno');
    //para buscar (POST id) a modificar cualquer dato del alta por post
    $this->post('/modificar[/]', \empleadoApi::class . ':modificarUno');
    //(POST id)
    $this->post('/suspender[/]', \empleadoApi::class . ':suspenderUno');
    //(POST id)
    $this->post('/activar[/]', \empleadoApi::class . ':activarUno');
    //7-b- Cantidad de operaciones por cada uno (POST desde, hasta args email)
    $this->post('/cantidadOperaciones/[{email}]', \empleadoApi::class . ':operacionesEmpleado');
    //7-a-los​ días y horarios​ que se Ingresaron​ al sistema (POST desde, hasta args email)
    $this->post('/historicoLogin/[{email}]', \empleadoApi::class . ':loginEmpleado');

})->add(\MWparaCORS::class . ':HabilitarCORSTodos')->add(\MWparaAutentificar::class . ':VerificarAdmin');

$app->group('/cochera', function () {
  //(POST piso estado tipo )
  $this->post('/alta[/]', \cocheraApi::class . ':CargarUno');
  $this->get('/', \cocheraApi::class . ':traerTodos');
  //(POST numero)
  $this->post('/borrar[/]', \cocheraApi::class . ':BorrarUno');
  //(POST id)
  $this->post('/modificar[/]', \cocheraApi::class . ':modificarUno');
  //8-De​ las cocheras La​ más​ y menos utilizada Si alguna no​ se usó.
  $this->post('/masUtilizada[/]', \cocheraApi::class . ':traerMax');
  // A (POST desde, hasta)
  $this->post('/masUtilizadaFechaIngreso[/]', \cocheraApi::class . ':traerMaxFechasIngreso');
  $this->post('/masUtilizadaFechaSalida[/]', \cocheraApi::class . ':traerMaxFechasSalida');
  $this->post('/masUtilizadaFecha[/]', \cocheraApi::class . ':traerMaxFechas');
  //B menos utilizada
  $this->post('/menosUtilizada[/]', \cocheraApi::class . ':traerMin');
  //B (POST desde, hasta)
  $this->post('/menosUtilizadaFechaIngreso[/]', \cocheraApi::class . ':traerMinFechasIngreso');
  $this->post('/menosUtilizadaFechaSalida[/]', \cocheraApi::class . ':traerMinFechasSalida');
  $this->post('/menosUtilizadaFecha[/]', \cocheraApi::class . ':traerMinFechas');
  //C Si alguna no​ se usó.
  $this->post('/nuncaUtilizada[/]', \cocheraApi::class . ':traerNunca');
  //C (POST desde, hasta)
  $this->post('/nuncaUtilizadaFecha[/]', \cocheraApi::class . ':traerNuncaFechaIngreso');

})->add(\MWparaCORS::class . ':HabilitarCORSTodos')->add(\MWparaAutentificar::class . ':VerificarAdmin');

$app->group('/estacionamiento', function () {

  $this->post('/ingresar', \estacionamientoApi::class . ':ingresarAuto')->add(\MWparaAutentificar::class . ':VerificarUser');
  $this->post('/retirar', \estacionamientoApi::class . ':retirarAuto')->add(\MWparaAutentificar::class . ':VerificarUser');
  //$this->post('/calcular', \estacionamientoApi::class . ':calcular');
  //9-​De​ los​ autos​ estacionados​ debo​ saber cochera,Hora de inicio​ yfinalización Cuanto​ pagó (Post patente)
  $this->post('/ubicar', \estacionamientoApi::class . ':ubicarAuto')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  //9-(POST desde, hasta, patente)
  $this->post('/ubicarFechaIngreso', \estacionamientoApi::class . ':ubicarAutoFechaIngreso')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  //9-(POST desde, hasta, patente)
  $this->post('/ubicarFechaSalida', \estacionamientoApi::class . ':ubicarAutoFechaSalida')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  //9-(POST desde, hasta, patente)
  $this->post('/ubicarFecha', \estacionamientoApi::class . ':ubicarAutoFecha')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  //10 a-0.50% facturación-cantidad de vehículo (POST desde, hasta)
  $this->post('/facturacionFechas', \estacionamientoApi::class . ':facturacionFechas')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  //10 b-0.75%​ usos de cocheras para discapacitados y no (POST desde, hasta incluye tipo "normal" "especial")
  $this->post('/usoTipoFechas', \estacionamientoApi::class . ':usoTipoCocheraFechas')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  //10 c-100%​ ​cuántos​ ​ vehículos​ ​ sin repetir(distintos​ ​ se​ ​ estacionaron) (POST desde, hasta)
  $this->post('/cantidadesPatentesTotal', \estacionamientoApi::class . ':cantidadesIngresosPatenteSinRepetir')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  //10 c-100%​ ​ cantidades​ ​ de​ ​ veces​ ​ que​ ​ vino​ ​ el​ ​ mismo​ ​ vehículo (POST desde, hasta, patente)
  $this->post('/cantidadIngresoPatente', \estacionamientoApi::class . ':cantidadesIngresosMismaPatenteFechas')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  //11-(2pt)​ Promedio​ mensual​ de​ datos: a-0,50%​ importe
  $this->post('/promedioImporte', \estacionamientoApi::class . ':promedioImporteMes')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  //11-b-0,75%​ patente
  $this->post('/promedioPatente', \estacionamientoApi::class . ':promedioPatenteMes')->add(\MWparaAutentificar::class . ':VerificarAdmin');
  //11-c-100% cochera​ y usuario​
  $this->post('/promedioCocheraUsuario', \estacionamientoApi::class . ':promedioCocheraUsuarioMes')->add(\MWparaAutentificar::class . ':VerificarAdmin');


})->add(\MWparaCORS::class . ':HabilitarCORSTodos');

$app->group('/exportar', function () {
//https://www.youtube.com/watch?v=1igW3bUaGmk
//PHPExcel

})->add(\MWparaCORS::class . ':HabilitarCORSTodos')->add(\MWparaAutentificar::class . ':VerificarAdmin');

$app->run();