<!DOCTYPE html>
<html lang="es">

<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<script src="https://code.jquery.com/jquery-3.2.1.js"></script>
<script src="js/index.js"></script>
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
<title>TP Estacionamiento</title>
</head>

<div class="container">
<nav class="navbar navbar-inverse navbar-fixed-top">
  <div class="container-fluid">
    <div class="navbar-header">
      <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
        <span class="icon-bar"></span>
        <span class="icon-bar"></span>
        <span class="icon-bar"></span> 
      </button>
      <a class="navbar-brand" href="#">Estacionamiento</a>
    </div>
    <div class="collapse navbar-collapse" id="myNavbar">
      <ul class="nav navbar-nav navbar-right">
        <li class="active"><a href="#">Home</a></li>
        <li><a href="#">Descripcion</a></li>
        <li><a href="#inscripcion">Inscripcion</a></li>
        <li><a href="#listado">Listado</a></li>
      </ul>

    </div>
  </div>
</nav>

<body style="padding-top:65px">
  <section>
    <div class="well well-lg row">


      <div class="col-md-6">
        <p class="lead">
        <?php
        use \Psr\Http\Message\ServerRequestInterface as Request;
        use \Psr\Http\Message\ResponseInterface as Response;

        require 'vendor/autoload.php';
        require 'clases/AccesoDatos.php';
        require 'clases/estacionamiento.php';
        require 'clases/empleado.php';
        require 'clases/empleadoApi.php';
        require 'clases/vehiculo.php';
        require 'clases/vehiculoApi.php';
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
                  $response->getBody()->write("Bienvenido '$_GET[email]'!!! ,a SlimFramework");
                  return $response;
        });

        $app->group('/alta', function () {
  
              $this->post('/empleado[/]', \empleadoApi::class . ':CargarUno');
              $this->post('/vehiculo[/]', \vehiculoApi::class . ':CargarUno');
        });
        $app->group('/baja', function () {
          //x-www-form-urlencoded
                $this->delete('/empleado[/]', \empleadoApi::class . ':BorrarUno');
                $this->delete('/vehiculo[/]', \vehiculoApi::class . ':BorrarUno');
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
?>
        </p>


      </div>
    </div>
  </section>


</body>

</html>