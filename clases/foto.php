<?php
require_once 'AccesoDatos.php';

class foto {

    public function backupFoto($request, $response, $args) 
    {
        $ArrayDeParametros = $request->getParsedBody(); //para delete urlencoded
        $email=$ArrayDeParametros['email'];
        if (!isset($ArrayDeParametros['email'])) {
            throw new Exception('Error: Debe enviar por POST email');
        }
        $empleadoAux = empleado::TraerEmpleadoEmail($email);

        $foto =$empleadoAux->foto;
        foto::copyFoto($foto,$empleadoAux->nombre);


    }

    public static function copyFoto($foto, $nombre)
    {
            $ahora = date("Ymd-His");
            $extension = pathinfo($foto, PATHINFO_EXTENSION);
            $nombreNuevo = $nombre."_".$ahora.".".$extension;
            
            if (copy($foto,"fotosEmpleados/backup/".$nombreNuevo)) {
                echo "La foto de ".$nombre." se guardo correctamente\n";
            } else {
                echo "Se ha producido un error al intentar copiar el fichero\n";
            }
    }

    public static function reNombrarFoto($request, $response, $args) 
    {   
        $ArrayDeParametros = $request->getParsedBody();
        $email=$ArrayDeParametros['email'];
        if (!isset($ArrayDeParametros['email'])) {
            throw new Exception('Error: Debe enviar por POST email');
        }

        $nombreNuevo= $ArrayDeParametros['nombreNuevo'];
        if (!isset($ArrayDeParametros['nombreNuevo']) || $nombreNuevo == "" ) {
            throw new Exception('Error: Debe enviar por POST nombreNuevo');
        }

        if (foto::validarNombre($nombreNuevo) == false) {
            throw new Exception('Error: nombreNuevo solo puede contener letas y numeros');
        }

        $empleadoAux = empleado::TraerEmpleadoEmail($email);
        if ($empleadoAux == false) {
            throw new Exception('Error: no existe empleado');
        }

        $id =$empleadoAux->id;
        $fotoVieja =$empleadoAux->foto;
        $extension = pathinfo($empleadoAux->foto, PATHINFO_EXTENSION);

        $fotoNueva = 'fotosEmpleados/'.$nombreNuevo.".".$extension;
        $empleadoAux->foto = $fotoNueva;
        if ($empleadoAux->ModificarEmpleadoID($id) < 0) {
            throw new Exception('Error al modificar empleado-foto');
        }
        rename($fotoVieja , $fotoNueva);
        return $response->withJson("Se modifico el nombre de la foto correctamente", 200);

    }

    public static function validarNombre($cadena)
    { 
        $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"; 
        for ($i=0; $i<strlen($cadena); $i++){ 
            if (strpos($permitidos, substr($cadena,$i,1))===false){ 
            //no es válido; 
            return false; 
            } 
        }  
        //si estoy aqui es que todos los caracteres son validos 
        return true; 
    }
    public static function validarNumero($cadena)
    { 
        $permitidos = "0123456789"; 
        for ($i=0; $i<strlen($cadena); $i++){ 
            if (strpos($permitidos, substr($cadena,$i,1))===false){ 
            //no es válido; 
            return false; 
            } 
        }  
        //si estoy aqui es que todos los caracteres son validos 
        return true; 
    }
    public static function validarLetras($cadena)
    { 
        $permitidos = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"; 
        for ($i=0; $i<strlen($cadena); $i++){ 
            if (strpos($permitidos, substr($cadena,$i,1))===false){ 
            //no es válido; 
            return false; 
            } 
        }  
        //si estoy aqui es que todos los caracteres son validos 
        return true; 
    } 
/*
    public static function marcaDeAgua($request, $response, $args)
    {
        $ArrayDeParametros = $request->getParsedBody();
        
        if (!isset($ArrayDeParametros['email'])) {
            throw new Exception('Error: Debe enviar por POST email');
        }
        $email=$ArrayDeParametros['email'];
        $empleadoAux = empleado::TraerEmpleadoEmail($email);

        if ($empleadoAux == false) {
            throw new Exception('Error: no existe empleado');
        }
        $id = $empleadoAux->id;
        $extension = pathinfo($empleadoAux->foto, PATHINFO_EXTENSION);
        $filename = pathinfo($empleadoAux->foto, PATHINFO_FILENAME);
        $fotoVieja = $empleadoAux->foto;

        // Cargar la estampa y la foto para aplicarle la marca de agua
        $estampa = imagecreatefrompng('IMG/logo.png');
        if ($extension === 'png') {
            $im =imagecreatefrompng($empleadoAux->foto);
        }
        if ($extension == 'jpg' || $extension == 'jpeg') {
            $im =imagecreatefromjpeg($empleadoAux->foto);
        }
        // Establecer los márgenes para la estampa y obtener el alto/ancho de la imagen de la estampa
        $margen_dcho = 10;
        $margen_inf = 10;
        $sx = imagesx($estampa);
        $sy = imagesy($estampa);

        // Copiar la imagen de la estampa sobre nuestra foto usando los índices de márgen y el
        // ancho de la foto para calcular la posición de la estampa. 
        imagecopy($im, $estampa, imagesx($im) - $sx - $margen_dcho, imagesy($im) - $sy - $margen_inf, 0, 0, imagesx($estampa), imagesy($estampa));
        
        header('Content-type: image/png');
        
        imagepng($im);
        $save = "IMG/". $filename.".".$extension;
        imagepng($im, $save);
        imagedestroy($im);
        unlink($fotoVieja);
        rename($save , 'fotosEmpleados/'.$filename.".png");
        $empleadoAux->foto = 'fotosEmpleados/'.$filename.".png";
        $empleadoAux->ModificarEmpleadoID($id);
        exit;

    }
*/
    public static function marcaDeAgua($emailAux)
    {
        //$ArrayDeParametros = $request->getParsedBody();
        
        if ($emailAux == "") {
            throw new Exception('Error: Debe enviar por POST email');
        }
        $email=$emailAux;
        $empleadoAux = empleado::TraerEmpleadoEmail($email);

        if ($empleadoAux == false) {
            throw new Exception('Error: no existe empleado');
        }
        $id = $empleadoAux->id;
        $extension = pathinfo($empleadoAux->foto, PATHINFO_EXTENSION);
        $filename = pathinfo($empleadoAux->foto, PATHINFO_FILENAME);
        $fotoVieja = $empleadoAux->foto;

        // Cargar la estampa y la foto para aplicarle la marca de agua
        $estampa = imagecreatefrompng('IMG/logo.png');
        if ($extension === 'png') {
            $im =imagecreatefrompng($empleadoAux->foto);
        }
        if ($extension == 'jpg' || $extension == 'jpeg') {
            $im =imagecreatefromjpeg($empleadoAux->foto);
        }
        // Establecer los márgenes para la estampa y obtener el alto/ancho de la imagen de la estampa
        $margen_dcho = 10;
        $margen_inf = 10;
        $sx = imagesx($estampa);
        $sy = imagesy($estampa);

        // Copiar la imagen de la estampa sobre nuestra foto usando los índices de márgen y el
        // ancho de la foto para calcular la posición de la estampa. 
        imagecopy($im, $estampa, imagesx($im) - $sx - $margen_dcho, imagesy($im) - $sy - $margen_inf, 0, 0, imagesx($estampa), imagesy($estampa));
        
        header('Content-type: image/png');
        
        //imagepng($im);
        $save = "IMG/". $filename.".".$extension;
        imagepng($im, $save);
        imagedestroy($im);
        unlink($fotoVieja);
        rename($save , 'fotosEmpleados/'.$filename.".png");
        $empleadoAux->foto = 'fotosEmpleados/'.$filename.".png";
        $empleadoAux->ModificarEmpleadoID($id);
        return true;

    }
    //para ver si la imagen tiene marca de agua
    public static function verImagen($request, $response, $args)
    {
        if (empty($args))
        {
            throw new Exception('Error: debe agregar email');
        }
            $email = $args['email'];

        $empleadoAux = empleado::TraerEmpleadoEmail($email);
        if ($empleadoAux == false) {
            throw new Exception('Error: no existe empleado');
        }
        $extension = pathinfo($empleadoAux->foto, PATHINFO_EXTENSION);
        if ($extension === 'png') {
            $im =imagecreatefrompng($empleadoAux->foto);
            header('Content-type: image/png');
            imagepng($im);
        }
        if ($extension == 'jpg' || $extension == 'jpeg') {
            $im =imagecreatefromjpeg($empleadoAux->foto);
            header('Content-type: image/jpeg');
            imagejpeg($im);
        }
        imagedestroy($im);
        exit;
    }
    
    //modifica el tamaño a menor 0.5 MB
    public static function tamImagen($imagenAux,$nombre)
    {
        list($ancho, $alto) = getimagesize($imagenAux);
        $nuevo_ancho = 640;
        $nuevo_alto = 480;

        $extension = pathinfo($imagenAux, PATHINFO_EXTENSION);

        // Redimensionar
        $imagen_p = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
        if ($extension == 'png') {
            $imagen =imagecreatefrompng($imagenAux);
            imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
            $save = 'fotosEmpleados/'.$nombre.".".$extension;
            imagepng($imagen_p, $save);
        }
        if ($extension == 'jpg' || $extension == 'jpeg') {
            $imagen = imagecreatefromjpeg($imagenAux);
            imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
            $save = 'fotosEmpleados/'.$nombre.".".$extension;
            imagejpeg($imagen_p, $save);
        }
        return $save;
    }

    //de aca para bajo todo para estacionamientoApi.php
    public static function tamImagenGlobal($imagenAux,$nombre,$directorio)
    {
        list($ancho, $alto) = getimagesize($imagenAux);
        $nuevo_ancho = 640;
        $nuevo_alto = 480;

        $extension = pathinfo($imagenAux, PATHINFO_EXTENSION);

        // Redimensionar
        $imagen_p = imagecreatetruecolor($nuevo_ancho, $nuevo_alto);
        if ($extension == 'png') {
            $imagen =imagecreatefrompng($imagenAux);
            imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
            $save = $directorio.$nombre.".".$extension;
            imagepng($imagen_p, $save);
        }
        if ($extension == 'jpg' || $extension == 'jpeg') {
            $imagen = imagecreatefromjpeg($imagenAux);
            imagecopyresampled($imagen_p, $imagen, 0, 0, 0, 0, $nuevo_ancho, $nuevo_alto, $ancho, $alto);
            $save = $directorio.$nombre.".".$extension;
            imagejpeg($imagen_p, $save);
        }
        return $save;
    }

    public static function verImagenAuto($request, $response, $args)
    {
        if (empty($args))
        {
            throw new Exception('Error: debe agregar email');
        }
            $patente = $args['patente'];

        $vehiculoAux = vehiculo::TraerVehiculoPatente($patente);
        if ($vehiculoAux == false) {
            throw new Exception('Error: no existe patente');
        }
        $extension = pathinfo($vehiculoAux->foto, PATHINFO_EXTENSION);
        if ($extension === 'png') {
            $im =imagecreatefrompng($vehiculoAux->foto);
            header('Content-type: image/png');
            imagepng($im);
        }
        if ($extension == 'jpg' || $extension == 'jpeg') {
            $im =imagecreatefromjpeg($vehiculoAux->foto);
            header('Content-type: image/jpeg');
            imagejpeg($im);
        }
        imagedestroy($im);
        exit;
    }

    public static function vaciarPapelera($request, $response, $args)
    {
        $borrados = array();
        $objDelaRespuesta= new stdclass();
        $files = glob("fotosEmpleados/papelera/*.png"); // obtiene todos los archivos
        foreach($files as $file)
        {
            $objDelaRespuesta->msj="Se vacio la pepelara con los siguientes archivos";
            if(is_file($file)) // si se trata de un archivo
            array_push($borrados, $file);
            unlink($file); // lo elimina
        }
        $objDelaRespuesta->Borrados=$borrados;
        return $response->withJson($objDelaRespuesta, 200);
    }
}
?>