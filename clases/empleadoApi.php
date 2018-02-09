<?php
include_once "empleado.php";
include_once "historico.php";

class empleadoApi extends empleado
{
    //CargoUno
    public function CargarUno($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        //$objDelaRespuesta= new stdclass();
        
        $nombre= $ArrayDeParametros['nombre'];
        $sexo= $ArrayDeParametros['sexo'];
        $email= $ArrayDeParametros['email'];
        $clave= $ArrayDeParametros['clave'];
        $turno= $ArrayDeParametros['turno'];
        $perfil= $ArrayDeParametros['perfil'];
        $alta= date("Y-m-d H:i:s");
        $estado= $ArrayDeParametros['estado'];
        

        $empleadoAux = new empleado();

        $empleadoAux->nombre = strtolower($nombre);
        $empleadoAux->sexo = strtolower($sexo);
        $empleadoAux->email = strtolower($email);
        $empleadoAux->clave = $clave;
        $empleadoAux->turno = strtolower($turno);
        $empleadoAux->perfil = strtolower($perfil);
        $empleadoAux->alta = $alta;
        $empleadoAux->estado = strtolower($estado);

        $e = empleado::TraerEmail($empleadoAux->email);

        
        if ($e == null) {
            
            $foto = $this->obtenerArchivo($nombre);
            if($foto != NULL)
            {
                move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
                $empleadoAux->foto = $foto;
                $empleadoAux->InsertarEmpleadoParametros();
                $response->getBody()->write("Se dio de alta al empleado: ".$nombre);
            }
            else
            {
                $response->getBody()->write("Error al guardar empleado.");
            }
        }
        else {
            return $response->withJson("El emplado ya existe ",404);
        }

        return $response;
        
    }
    //Manejo de la Imagen
    public function obtenerArchivo($nombre) 
	{
        if ( 0 < $_FILES['foto']['error'] ) {
			return null;
		}
		else {
			$foto = $_FILES['foto']['name'];
			
			$extension= explode(".", $foto)  ;

            $nombreNuevo = 'fotosEmpleados/'.$nombre.".".$extension[1];
            return $nombreNuevo;
		}
    }

    //TraigoTodos
    public function traerTodos($request, $response, $args) 
	{
        //$arrayConToken = $request->getHeader('token');
        //$token=$arrayConToken[0];
        //$datosToken = AutentificadorJWT::ObtenerData($token);
        //return $response->write('Yay, ' . $datosToken->sexo);
        $suspendido = $request->getAttribute('suspendidos');
        if (!empty($args) ){
            $todosEmpleados = empleado::TraerTodoLosEmpleadosSuspendidos();
             $response->withJson($todosEmpleados, 200);

            if ($todosEmpleados ==false) {
                return $response->withJson("No hay empleados suspendidos");
            } 
            return $response;
        }
        else {
            $todosEmpleados = empleado::TraerTodoLosEmpleados();
		    return $response->withJson($todosEmpleados, 200);  
            
        }

    }

    public function BorrarUno($request, $response, $args) 
    {
            $ArrayDeParametros = $request->getParsedBody(); //para delete urlencoded
            $id=$ArrayDeParametros['id'];
            $empBorrar = empleado::TraerEmpleadoID($id);

            $nombreViejo =$empBorrar->nombre;
            $fotovieja =$empBorrar->foto;

            $objDelaRespuesta= new stdclass();
            if(empleado::BorrarEmpleadoID($id)>0)
            {       
                empleadoApi::fotoPapelera($fotovieja,$nombreViejo);
                $objDelaRespuesta->resultado="Se borro con exito a : ".$empBorrar->nombre."";
            }
            else
            {
                $objDelaRespuesta->resultado="Error al Borrar el empleado";
            }
            $newResponse = $response->withJson($objDelaRespuesta, 200);  
            return $newResponse;
    }

    public static function fotoPapelera($fotoVieja, $nombre)
    {
            $ahora = date("Ymd-His");
            $extension = pathinfo($fotoVieja, PATHINFO_EXTENSION);
            rename($fotoVieja , "fotosEmpleados/papelera/".trim($nombre)."-".$ahora.".".$extension);
    }
    

    public function suspenderUno($request, $response, $args) 
    {
            $ArrayDeParametros = $request->getParsedBody();
            $id= $ArrayDeParametros['id'];
            $objDelaRespuesta= new stdclass();
            //$precio= $ArrayDeParametros['precio'];
            $empModificar = new empleado();
            $empModificar = empleado::TraerEmpleadoID($id);
            
            if ($empModificar != NULL ) 
            {
                $accion = 'suspendido';
                empleado::SuspenderEmpleadoParametros($id,$accion);
                    //$empModificar->SuspenderEmpleadoParametros();
                    $objDelaRespuesta->resultado="Se suspendio a : ".$empModificar->nombre;
            }
            else 
            {
                $objDelaRespuesta->resultado="Error al suspender: debe ingresar un ID valido";
            }
            $newResponse = $response->withJson($objDelaRespuesta, 200);
            return $newResponse; 
            
    }
    public function activarUno($request, $response, $args) 
    {
            $ArrayDeParametros = $request->getParsedBody();
            $id= $ArrayDeParametros['id'];
            $objDelaRespuesta= new stdclass();
            //$precio= $ArrayDeParametros['precio'];
            $empModificar = new empleado();
            $empModificar = empleado::TraerEmpleadoID($id);
            
            if ($empModificar != NULL ) 
            {
                $accion = "activo";
                empleado::SuspenderEmpleadoParametros($id,$accion);
                    //$empModificar->SuspenderEmpleadoParametros();
                    $objDelaRespuesta->resultado="Se activo a : ".$empModificar->nombre;
            }
            else 
            {
                $objDelaRespuesta->resultado="Error al activar: debe ingresar un ID valido";
            }
            $newResponse = $response->withJson($objDelaRespuesta, 200);
            return $newResponse; 
            
    }

    public function modificarUno($request, $response, $args) 
    {
            $ArrayDeParametros = $request->getParsedBody();
            $id= $ArrayDeParametros['id'];
            $objDelaRespuesta= new stdclass();
            $empModificar = empleado::TraerEmpleadoID($id);

            if ($empModificar != false) {
                $objDelaRespuesta->msj = "se modifico empleado con id ".$id;
                if (isset($ArrayDeParametros['nombre'])) {
                    $nombre = strtolower($ArrayDeParametros['nombre']);
                    $empModificar->nombre = $nombre;
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->nombre =$nombre;
                }
                if (isset($ArrayDeParametros['sexo'])) {
                    $sexo = strtolower($ArrayDeParametros['sexo']);
                    $empModificar->sexo = $sexo;
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->sexo =$sexo;
                }
                if (isset($ArrayDeParametros['email'])) {
                    $email = strtolower($ArrayDeParametros['email']);
                    $empModificar->email = $email;
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->email =$email;
                }
                if (isset($ArrayDeParametros['clave'])) {
                    $clave = strtolower($ArrayDeParametros['clave']);
                    $empModificar->clave = $clave;
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->clave =$clave;
                }
                if (isset($ArrayDeParametros['turno'])) {
                    $turno = strtolower($ArrayDeParametros['turno']);
                    $empModificar->turno = $turno;
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->turno =$turno;
                }
                if (isset($ArrayDeParametros['perfil'])) {
                    $perfil = strtolower($ArrayDeParametros['perfil']);
                    $empModificar->perfil = $perfil;
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->perfil =$perfil;
                }
                if (isset($_FILES['foto']['name'])) {
                    $fotovieja = $empModificar->foto;
                    $nombreViejo = $empModificar->nombre;
                    empleadoApi::fotoPapelera($fotovieja,$nombreViejo);
                    $foto = $this->obtenerArchivo($nombreViejo);
                    if($foto != NULL)
                    {
                        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
                        $empModificar->foto = $foto;
                        $empModificar->ModificarEmpleadoID($id);
                        $objDelaRespuesta->foto =$foto;
                    }
                    $objDelaRespuesta->foto =$foto;
                    /*
                    $perfil = strtolower($ArrayDeParametros['perfil']);
                    $empModificar->perfil = $perfil;
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->perfil =$perfil;
                    */
                }
                if (isset($ArrayDeParametros['estado'])) {
                    $estado = strtolower($ArrayDeParametros['estado']);
                    $empModificar->estado = $estado;
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->estado =$estado;
                }
            }
            else {
                $objDelaRespuesta->error = "Error no existe el ID del empleado";
            }
            return $response->withJson($objDelaRespuesta, 200);
            
    }

    public function operacionesEmpleado($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        if (!empty($args)){
            $email = $args['email'];
        
            if ($empleadoAux = empleado::TraerEmpleadoEmail($email)){
                $objDelaRespuesta->empleado=$empleadoAux->nombre;
                if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                    $desde= $ArrayDeParametros['desde'];
                    $hasta= $ArrayDeParametros['hasta'];
                    $objDelaRespuesta->cantIngresos =empleado::operacionesUsuarioEntradaFecha($empleadoAux->id,$desde,$hasta);
                    $objDelaRespuesta->cantSalidas =empleado::operacionesUsuarioSalidaFecha($empleadoAux->id,$desde,$hasta);
                }
                if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                    $desde= $ArrayDeParametros['desde'];
                    $objDelaRespuesta->cantIngresos =empleado::operacionesUsuarioEntradaFecha($empleadoAux->id,$desde,"");
                    $objDelaRespuesta->cantSalidas =empleado::operacionesUsuarioSalidaFecha($empleadoAux->id,$desde,"");
                }
                if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                    $hasta= $ArrayDeParametros['hasta'];
                    $objDelaRespuesta->cantIngresos =empleado::operacionesUsuarioEntradaFecha($empleadoAux->id,"",$hasta);
                    $objDelaRespuesta->cantSalidas =empleado::operacionesUsuarioSalidaFecha($empleadoAux->id,"",$hasta);
                }
                if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                    $objDelaRespuesta->cantIngresos =empleado::operacionesUsuarioEntradaFecha($empleadoAux->id,"","");
                    $objDelaRespuesta->cantSalidas =empleado::operacionesUsuarioSalidaFecha($empleadoAux->id,"","");
                }
                return $response->withJson($objDelaRespuesta,200);
            }
            else{
                return $response->withJson("El emplado no existe ",206);
            }
        }
        else {
            return $response->withJson("Error: email en blanco ",404);
        }

    }

    public function loginEmpleado($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        $objDelaRespuesta= new stdclass();
        if (!empty($args)){
            $email = $args['email'];
        
            if ($empleadoAux = empleado::TraerEmpleadoEmail($email)){
                $objDelaRespuesta->empleado=$empleadoAux->nombre;
                if (isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                    $desde= $ArrayDeParametros['desde'];
                    $hasta= $ArrayDeParametros['hasta'];
                    $objDelaRespuesta->ingresos = historico::loginUsuarioFechas($empleadoAux->id,$desde,$hasta);
                }
                if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                    $desde= $ArrayDeParametros['desde'];
                    $objDelaRespuesta->ingresos = historico::loginUsuarioFechas($empleadoAux->id,$desde,"");
                }
                if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                    $hasta= $ArrayDeParametros['hasta'];
                    $objDelaRespuesta->ingresos = historico::loginUsuarioFechas($empleadoAux->id,"",$hasta);
                }
                if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                    $objDelaRespuesta->ingresos = historico::loginUsuarioFechas($empleadoAux->id,"","");
                }
                
                return $response->withJson($objDelaRespuesta,200);
            }
            else{
                return $response->withJson("El emplado no existe ",206);
            }
        }
        else {
            return $response->withJson("Error: email en blanco ",404);
        }

    }



}
