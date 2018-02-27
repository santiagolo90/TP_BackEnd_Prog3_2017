<?php
require_once 'AccesoDatos.php';
require_once 'vendor/fpdf181/fpdf.php';
class epdf {//extends FPDF{
/*
    function Header()
		{
			//$this->Image('images/logo.png', 5, 5, 30 );
			$this->SetFont('Arial','B',15);
			$this->Cell(30);
			$this->Cell(120,10, 'Reporte De Estados',0,0,'C');
			$this->Ln(20);
		}
		
	function Footer()
		{
			$pdf->SetY(-15);
			$pdf->SetFont('Arial','I', 8);
			$pdf->Cell(0,10, 'Pagina '.$pdf->PageNo().'/{nb}',0,0,'C' );
        }	
*/

    public function ubicaAutoPDF($request, $response, $args)
        {
            $ArrayDeParametros = $request->getParsedBody();
            //$patente= $ArrayDeParametros['patente'];
            $patente;
            $vehiculoAux = new vehiculo();
		    if (empty($args)) {
                $patente = "Todas";
                $vehiculoAux = vehiculo::TraerEstacionadosTodos();
            }
            else{
                $patente = $args['patente'];
                $vehiculoAux = vehiculo::TraerEstacionadosPatente($patente);
                if ($vehiculoAux == false) {
                    throw new Exception('No existe Patente: '.$patente);
                }
            }
            $pdf = new FPDF('P','mm','A4');
            $pdf->AddPage();
            
            //$pdf->Header();
            $pdf->SetFillColor(182,182,182);
            $pdf->SetFont('Arial','B',15);
			$pdf->Cell(30);
			$pdf->Cell(120,10, 'Movimientos del vehiculo con patente: '.$patente,1,0,'C',1);
            $pdf->Ln(20);
            
            //Cuerpo
            $pdf->SetFillColor(156,200,255);
            $pdf->SetFont('Arial','',10);
            $pdf->Ln(0);
            $pdf->Cell(25,6,utf8_decode('Nº de cochera'),1,0,'C',1);
            $pdf->Cell(25,6,'Patente',1,0,'C',1);
            //$pdf->Ln(0);
            $pdf->Cell(50,6,'Fecha de ingreso',1,0,'C',1);
            //$pdf->Ln(0);
            $pdf->Cell(50,6,'Fecha de Salida',1,0,'C',1);
            //$pdf->Ln(0);
            $pdf->Cell(45,6,'Importe',1,1,'C',1);
            //$pdf->Ln(5);
            $pdf->SetFont('Arial','',9);
            for ($i=0; $i <count($vehiculoAux) ; $i++) { 
                $pdf->Cell(25,6,cochera::TraerCocheraID($vehiculoAux[$i]->idCochera)->numero,1,0,'C');
                //
                $pdf->Cell(25,6,$vehiculoAux[$i]->patente,1,0,'C');
	            //$pdf->Ln(0);
        	    $pdf->Cell(50,6,$vehiculoAux[$i]->fechaHoraIngreso,1,0,'C');
	            //$pdf->Ln(0);
	            $pdf->Cell(50,6,$vehiculoAux[$i]->fechaHoraSalida,1,0,'C');
        	    //$pdf->Ln(0);
                $pdf->Cell(45,6,"$ ".$vehiculoAux[$i]->importe,1,1,'C');
                //$pdf->Cell(70,6,utf8_decode($row['']),1,1,'C');
	            //$pdf->Ln(5);
                }
            //Footer
            $pdf->AliasNbPages();
            $pdf->SetY(-10);
            $pdf->SetFont('Arial','I', 8);
            $pdf->Cell(0,-15, 'Pagina '.$pdf->PageNo().'/{nb}',0,0,'R' );
            $ahora = date("Ymd-His");
            $nombre = "ListadoEstacionados-".$ahora.".pdf";
            $pdf->Output('I',$nombre);
            exit;
        }
}
?>