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
        $clave= password_hash($ArrayDeParametros['clave'],PASSWORD_BCRYPT);
        $turno= $ArrayDeParametros['turno'];
        $perfil= $ArrayDeParametros['perfil'];
        $alta= date("Y-m-d H:i:s");
        $estado= $ArrayDeParametros['estado'];
        

        $empleadoAux = new empleado();

        $empleadoAux->nombre = strtolower($nombre);
        if ($empleadoAux->nombre== "" || !isset($empleadoAux->nombre)) {
            throw new Exception('Error: nombre no puede esta vacio');
        }
        if (foto::validarNombre($empleadoAux->nombre) == false) {
            throw new Exception('Error: Nombre solo puede contener letas y numeros');
        }
        $empleadoAux->sexo = strtolower($sexo);
        if ($empleadoAux->sexo== "" || !isset($empleadoAux->sexo)) {
            throw new Exception('Error: sexo no puede esta vacio');
        }
        $empleadoAux->email = strtolower($email);
        if ($empleadoAux->email== "" || !isset($empleadoAux->email)) {
            throw new Exception('Error: email no puede esta vacio');
        }
        $empleadoAux->clave = $clave;
        if ($empleadoAux->clave== "" || !isset($empleadoAux->clave)) {
            throw new Exception('Error: clave no puede esta vacio');
        }
        $empleadoAux->turno = strtolower($turno);
        if ($empleadoAux->turno== "" || !isset($empleadoAux->turno)) {
            throw new Exception('Error: turno no puede esta vacio');
        }
        $empleadoAux->perfil = strtolower($perfil);
        if ($empleadoAux->perfil== "" || !isset($empleadoAux->perfil)) {
            throw new Exception('Error: perfil no puede esta vacio');
        }
        $empleadoAux->alta = $alta;
        $empleadoAux->estado = strtolower($estado);
        if ($empleadoAux->estado== "" || !isset($empleadoAux->estado)) {
            throw new Exception('Error: estado no puede esta vacio');
        }
        //$foto = $_FILES['foto'];
        $e = empleado::TraerEmail($empleadoAux->email);

        
        if ($e == null) {
            $foto = $this->obtenerArchivo($nombre);
            
            if($foto != NULL)
            {
                move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
                
                if (filesize($foto)>500000) {
                    $empleadoAux->foto =foto::tamImagen($foto,$nombre);
                    
                }
                else{
                    //throw new Exception('Error: imagen mayor a 0.5 MB');
                    $empleadoAux->foto = $foto;
                }
                
                $empleadoAux->InsertarEmpleadoParametros();
                if (foto::marcaDeAgua($empleadoAux->email) == true) {
                    $response->getBody()->write("Se dio de alta al empleado: ".$nombre);
                }
                
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
        if(!isset($_FILES['foto']))
        {
            throw new Exception('Error: No existe foto');
        }
        if ( 0 < $_FILES['foto']['error'] ) {
			return null;
		}
		else {
            $foto = $_FILES['foto']['name'];
			
            $extension= explode(".", $foto);
            $tipo = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
            if($tipo != "jpg" && $tipo != "jpeg" && $tipo != "png") {
                throw new Exception('Error: de formato, solo se acepta jpg jpeg png');
            }

            $nombreNuevo = 'fotosEmpleados/'.$nombre.".".strtolower($extension[1]);
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
             //$response->withJson($todosEmpleados, 200);
            if ($todosEmpleados ==false) {
                return $response->withJson("No hay empleados suspendidos");
            } 
            return $response->withJson($todosEmpleados, 200);
        }
        else {
            $todosEmpleados = empleado::TraerTodoLosEmpleados();
		    return $response->withJson($todosEmpleados, 200);  
            
        }

    }

    public function BorrarUno($request, $response, $args) 
    {
            $ArrayDeParametros = $request->getParsedBody(); //para delete urlencoded
            if (!isset($ArrayDeParametros['id'])) {
                throw new Exception('Error al borrar: Debe ingresar ID de empleado');
            }
            $id=$ArrayDeParametros['id'];

            $empBorrar = empleado::TraerEmpleadoID($id);
            if ($empBorrar == false) {
                throw new Exception('Error al borrar: No existe empleado con id: '.$id);
            }

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
            if (!isset($ArrayDeParametros['id'])) {
                throw new Exception('Error al suspender: Debe ingresar ID de empleado');
            }
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
            if (!isset($ArrayDeParametros['id'])) {
                throw new Exception('Error al activar: Debe ingresar ID de empleado');
            }
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
            if (!isset($ArrayDeParametros['id'])) {
                throw new Exception('Error al modificar: Debe ingresar ID de empleado');
            }
            $id= $ArrayDeParametros['id'];
            $objDelaRespuesta= new stdclass();
            $empModificar = empleado::TraerEmpleadoID($id);

            if ($empModificar != false) {
                $objDelaRespuesta->msj = "se modifico empleado con id ".$id;
                if (isset($ArrayDeParametros['nombre'])) {
                    $nombre = strtolower($ArrayDeParametros['nombre']);
                    $empModificar->nombre = $nombre;
                    if ($empModificar->nombre== "" || !isset($empModificar->nombre)) {
                        throw new Exception('Error: nombre no puede esta vacio');
                    }
                    if (foto::validarNombre($empModificar->nombre) == false) {
                        throw new Exception('Error: Nombre solo puede contener letas y numeros');
                    }
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->nombre =$nombre;
                }
                if (isset($ArrayDeParametros['sexo'])) {
                    $sexo = strtolower($ArrayDeParametros['sexo']);
                    $empModificar->sexo = $sexo;
                    if ($empModificar->sexo== "" || !isset($empModificar->sexo)) {
                        throw new Exception('Error: sexo no puede esta vacio');
                    }
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->sexo =$sexo;
                }
                if (isset($ArrayDeParametros['email'])) {
                    $email = strtolower($ArrayDeParametros['email']);
                    $empModificar->email = $email;
                    if ($empModificar->email== "" || !isset($empModificar->email)) {
                        throw new Exception('Error: email no puede esta vacio');
                    }
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->email =$email;
                }
                if (isset($ArrayDeParametros['clave'])) {
                    $clave = password_hash($ArrayDeParametros['clave'],PASSWORD_BCRYPT);
                    $empModificar->clave = $clave;
                    if ($empModificar->clave== "" || !isset($empModificar->clave)) {
                        throw new Exception('Error: clave no puede esta vacio');
                    }
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->clave =$clave;
                }
                if (isset($ArrayDeParametros['turno'])) {
                    $turno = strtolower($ArrayDeParametros['turno']);
                    $empModificar->turno = $turno;
                    if ($empModificar->turno== "" || !isset($empModificar->turno)) {
                        throw new Exception('Error: turno no puede esta vacio');
                    }
                    $empModificar->ModificarEmpleadoID($id);
                    $objDelaRespuesta->turno =$turno;
                }
                if (isset($ArrayDeParametros['perfil'])) {
                    $perfil = strtolower($ArrayDeParametros['perfil']);
                    $empModificar->perfil = $perfil;
                    if ($empModificar->perfil== "" || !isset($empModificar->perfil)) {
                        throw new Exception('Error: perfil no puede esta vacio');
                    }
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
                        if (filesize($foto)>500000) {
                            $empModificar->foto =foto::tamImagen($foto,$nombreViejo);
                            
                        }
                        else{
                            //throw new Exception('Error: imagen mayor a 0.5 MB');
                            $empModificar->foto = $foto;
                        }
                        
                        $empModificar->ModificarEmpleadoID($id);
                        if (foto::marcaDeAgua($empModificar->email) == true) {
                            $objDelaRespuesta->foto =$foto;
                        }
                        /*
                        move_uploaded_file($_FILES['foto']['tmp_name'], $foto);
                        $empModificar->foto = $foto;
                        $empModificar->ModificarEmpleadoID($id);
                        $objDelaRespuesta->foto =$foto;
                        */
                    }
                    $objDelaRespuesta->foto =$foto;
                }
                if (isset($ArrayDeParametros['estado'])) {
                    $estado = strtolower($ArrayDeParametros['estado']);
                    $empModificar->estado = $estado;
                    if ($empModificar->estado== "" || !isset($empModificar->estado)) {
                        throw new Exception('Error: estado no puede esta vacio');
                    }
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
                    if ($desde== "") {
                        throw new Exception('Error: desde no puede esta vacio');
                    }
                    $hasta= $ArrayDeParametros['hasta'];
                    if ($hasta== "") {
                        throw new Exception('Error: hasta no puede esta vacio');
                    }
                    if ($desde > $hasta) {
                        throw new Exception('Error: desde no puede ser mayor que hasta');
                    }
                    $objDelaRespuesta->cantIngresos =empleado::operacionesUsuarioEntradaFecha($empleadoAux->id,$desde,$hasta);
                    $objDelaRespuesta->cantSalidas =empleado::operacionesUsuarioSalidaFecha($empleadoAux->id,$desde,$hasta);
                    $objDelaRespuesta->msj ="Operaciones desde ".$desde." hasta ".$hasta;
                }
                if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                    $desde= $ArrayDeParametros['desde'];
                    if ($desde== "") {
                        throw new Exception('Error: desde no puede esta vacio');
                    }
                    $objDelaRespuesta->cantIngresos =empleado::operacionesUsuarioEntradaFecha($empleadoAux->id,$desde,"");
                    $objDelaRespuesta->cantSalidas =empleado::operacionesUsuarioSalidaFecha($empleadoAux->id,$desde,"");
                    $objDelaRespuesta->msj ="Operaciones desde ".$desde." hasta hoy";
                }
                if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                    $hasta= $ArrayDeParametros['hasta'];
                    if ($hasta== "") {
                        throw new Exception('Error: hasta no puede esta vacio');
                    }
                    $objDelaRespuesta->cantIngresos =empleado::operacionesUsuarioEntradaFecha($empleadoAux->id,"",$hasta);
                    $objDelaRespuesta->cantSalidas =empleado::operacionesUsuarioSalidaFecha($empleadoAux->id,"",$hasta);
                    $objDelaRespuesta->msj ="Operaciones desde el inicio de actividades hasta ".$hasta;
                }
                if (!isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                    $objDelaRespuesta->cantIngresos =empleado::operacionesUsuarioEntradaFecha($empleadoAux->id,"","");
                    $objDelaRespuesta->cantSalidas =empleado::operacionesUsuarioSalidaFecha($empleadoAux->id,"","");
                    $objDelaRespuesta->msj ="Operaciones desde el inicio de actividades hasta hoy";
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
                    if ($desde== "") {
                        throw new Exception('Error: desde no puede esta vacio');
                    }
                    $hasta= $ArrayDeParametros['hasta'];
                    if ($hasta== "") {
                        throw new Exception('Error: hasta no puede esta vacio');
                    }
                    if ($desde > $hasta) {
                        throw new Exception('Error: desde no puede ser mayor que hasta');
                    }
                    $objDelaRespuesta->ingresos = historico::loginUsuarioFechas($empleadoAux->id,$desde,$hasta);
                }
                if (isset($ArrayDeParametros['desde']) && !isset($ArrayDeParametros['hasta'])) {
                    $desde= $ArrayDeParametros['desde'];
                    if ($desde== "") {
                        throw new Exception('Error: desde no puede esta vacio');
                    }
                    $objDelaRespuesta->ingresos = historico::loginUsuarioFechas($empleadoAux->id,$desde,"");
                }
                if (!isset($ArrayDeParametros['desde']) && isset($ArrayDeParametros['hasta'])) {
                    $hasta= $ArrayDeParametros['hasta'];
                    if ($hasta== "") {
                        throw new Exception('Error: hasta no puede esta vacio');
                    }
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
