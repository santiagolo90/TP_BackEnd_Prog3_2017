<?php
require_once 'AccesoDatos.php';
require_once 'vendor/PHPExcel-1.8/Classes/PHPExcel.php';
class excel{

    public function traerTodosEmpleadosExcel($request, $response, $args) 
	{
        $todosEmpleados = empleado::TraerTodoLosEmpleados();
        $objPHPExcel = new PHPExcel();
        $num = 1;
        //echo($todosEmpleados[1]->nombre);
        //var_dump($todosEmpleados);

        $objPHPExcel->getActiveSheet()->setCellValue('A'.$num, "id");
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$num, "nombre");
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$num, "email");
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$num, "clave");
        $objPHPExcel->getActiveSheet()->setCellValue('E'.$num, "turno");
        $objPHPExcel->getActiveSheet()->setCellValue('F'.$num, "perfil");
        $objPHPExcel->getActiveSheet()->setCellValue('G'.$num, "foto");
        $objPHPExcel->getActiveSheet()->setCellValue('H'.$num, "alta");
        $objPHPExcel->getActiveSheet()->setCellValue('I'.$num, "estado");


        for ($i=0; $i < count($todosEmpleados); $i++) 
        {
            $num++;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $todosEmpleados[$i]->id);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$num, $todosEmpleados[$i]->nombre);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$num, $todosEmpleados[$i]->email);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$num, $todosEmpleados[$i]->clave);
            $objPHPExcel->getActiveSheet()->setCellValue('E'.$num, $todosEmpleados[$i]->turno);
            $objPHPExcel->getActiveSheet()->setCellValue('F'.$num, $todosEmpleados[$i]->perfil);
            $objPHPExcel->getActiveSheet()->setCellValue('G'.$num, $todosEmpleados[$i]->foto);
            $objPHPExcel->getActiveSheet()->setCellValue('H'.$num, $todosEmpleados[$i]->alta);
            $objPHPExcel->getActiveSheet()->setCellValue('I'.$num, $todosEmpleados[$i]->estado);
        }
        $objPHPExcel->getActiveSheet()->setTitle('Empleados');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        if ($objWriter == false) {
            throw new Exception('Error al exportar listado a xlsx');
        }
        else{
            $ahora = date("Ymd-His");
            $nombre = "Listado_Empleados-".$ahora.".xlsx";
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$nombre.'');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output'); 
            exit;
        }
/*
        $objWriter->save("Listado_Empleados.xlsx");
        if ($objWriter) {
            $ahora = date("Ymd-His");
            $extension = pathinfo("Listado_Empleados.xlsx", PATHINFO_EXTENSION);
            rename("Listado_Empleados.xlsx" , "exportados/excel/"."Listado_Empleados"."-".$ahora.".".$extension);
            echo "Listado de emplados exportado correctamente";
        }
*/

        
    }

    public function loginExcel($request, $response, $args) 
	{
        $login = historico::traerTodosLogin();
        $objPHPExcel = new PHPExcel();
        $num = 1;

        $objPHPExcel->getActiveSheet()->setCellValue('A'.$num, "id");
        $objPHPExcel->getActiveSheet()->setCellValue('B'.$num, "email");
        $objPHPExcel->getActiveSheet()->setCellValue('C'.$num, "fecha");
        $objPHPExcel->getActiveSheet()->setCellValue('D'.$num, "hora");


        for ($i=0; $i < count($login); $i++) 
        {
            $num++;
            $objPHPExcel->getActiveSheet()->setCellValue('A'.$num, $login[$i]['id']);
            $objPHPExcel->getActiveSheet()->setCellValue('B'.$num, empleado::TraerEmpleadoID($login[$i]['idEmpleado'])->email);
            $objPHPExcel->getActiveSheet()->setCellValue('C'.$num, $login[$i]['fecha']);
            $objPHPExcel->getActiveSheet()->setCellValue('D'.$num, $login[$i]['hora']);

        }
        $objPHPExcel->getActiveSheet()->setTitle('login');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        if ($objWriter == false) {
            throw new Exception('Error al exportar listado a xlsx');
        }
        else{
            $ahora = date("Ymd-His");
            $nombre = "Listado_Login-".$ahora.".xlsx";
            //$objWriter->save("Listado_Login.xlsx");
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="'.$nombre.'');
            header('Cache-Control: max-age=0');
            $objWriter->save('php://output'); 
            exit;
        }
            //$extension = pathinfo("Listado_Login.xlsx", PATHINFO_EXTENSION);
            //rename("Listado_Login.xlsx" , "exportados/excel/"."Listado_Login"."-".$ahora.".".$extension);
            //echo "Listado de login exportado correctamente";
            //$objWriter->save('php://output'); 
            //exit;
    }
    

}
?>