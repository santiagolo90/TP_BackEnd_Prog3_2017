<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require 'vendor/autoload.php';
require 'clases/AccesoDatos.php';
require 'clases/estacionamiento.php';
require 'clases/empleado.php';
require 'clases/vehiculo.php';
//require '/clases/MWparaCORS.php';
require 'clases/MWparaAutentificar.php';


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
  $response->getBody()->write("GET => Bienvenido!!! ,a SlimFramework");
  return $response;

});

$app->group('/alta', function () {
  
      $this->post('/usuario[/]', \empleado::class . ':GuardarEmpleado');
      $this->post('/vehiculo[/]', \vehiculo::class . ':GuardarVehiculo');
  });
$app->group('/baja', function () {
        $this->delete('[/]', \empleado::class . ':BorrarUno');
  });

/*LLAMADA A METODOS DE INSTANCIA DE UNA CLASE*/
$app->group('/login', function () {

    $this->get('/', \estacionamiento::class . ':TraerEmpleado');
 /*
  $this->get('/', \cdApi::class . ':traerTodos')->add(\MWparaCORS::class . ':HabilitarCORSTodos');
 
  $this->get('/{id}', \cdApi::class . ':traerUno')->add(\MWparaCORS::class . ':HabilitarCORSTodos');

  $this->post('/', \cdApi::class . ':CargarUno');

  $this->delete('/', \cdApi::class . ':BorrarUno');

  $this->put('/', \cdApi::class . ':ModificarUno');
     
})->add(\MWparaAutentificar::class . ':VerificarUsuario')->add(\MWparaCORS::class . ':HabilitarCORS8080');
*/
});
$app->run();