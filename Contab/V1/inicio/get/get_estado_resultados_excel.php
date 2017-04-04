<?php
	$url[0] = "../";

    require_once  "../class/PHPExcel.php";
    require_once "../class/balance_general.class.php";
    require_once "../class/conf_apartados.class.php";


    $arrayMeses = array('Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
       'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre');

    if(isset($_GET['inicio']) && isset($_GET['fin'])){
        $periodo[0] = $_GET['inicio'];
        $periodo[1] = $_GET['fin'];
    }else{
        $periodo[0] = date('Y/m/01');
        $periodo[1] = date('Y/m/t');
    }


    $apartados  = new Apartados();
    $balance    = new Balance();


    $ventas = $balance->get_balance(1,2);
    $costo_ventas = $balance->get_balance(2,2);

    $gasto_operacion = $balance->get_balance(3,2);
    $gasto_admon = $balance->get_balance(4,2);
    $gasto_ventas = $balance->get_balance(5,2);


    $estado = 2;

    $apartados_ventas = $apartados->get_apartados_obj_ventas($estado);
    $apartados_costos = $apartados->get_apartados_obj_costo_ventas($estado);
    $apartados_gastos = $apartados->get_apartados_obj_gastos($estado);


    $totalVentas=0;
    $totalGastos=0;
    $totalCostos=0;

    $cont=0;

    $totalBruta=0;
    $totalOperacion=0;

    // filename for download
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Auribox")
				->setTitle("Póliza")
				->setSubject("Office 2007 XLSX")
				->setDescription("Pólizas XLSX, generado usando clases PHP.")
				->setKeywords("office 2007 openxml php")
				->setCategory("Contabilidad");
    $filename = "estado_resultados_".$periodo[0]." a ".$periodo[1]."_" . date('Ymd') . ".xls";

    $sheet = $objPHPExcel->getActiveSheet();
    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Estado de resultados correspondiente de: '.$periodo[0]." a ".$periodo[1]);

    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    
    //Ventas
    $total=0;
    $i=3;
    foreach($apartados_ventas as $ventas){
            $datos= $balance->get_balance($ventas->rowid,$estado); 
               $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $ventas->apartado )
                    ->setCellValue('A'.($i+1), 'Grupo');
                $sheet->mergeCells('A'.$i.':B'.$i);
                $sheet->getStyle('A'.$i.':B'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $i++;
        $suma=0;
        $total=0;
                foreach( $datos as $value ){
                    $i++;
                    $total+= $suma= $balance->get_cta_inicial($value['fk_codagr_ini'],$value['fk_codagr_fin'],$periodo);
                    $objPHPExcel->setActiveSheetIndex(0)
                                ->setCellValue('A'.$i, $value['grupo'])
                                ->setCellValue('B'.$i, number_format($suma,2,'.',','));

                }
                $i++;
                 $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, "Total: ")
                    ->setCellValue('B'.$i, "")
                    ->setCellValue('C'.$i, number_format($total,2,'.',','));   
                $totalVentas+=$total;
            $i++;
	}


    $total=0;
    $i=3;
    foreach($apartados_costos as $costos){
            $datos= $balance->get_balance($costos->rowid,$estado); 
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('D'.$i,  $costos->apartado  )
                        ->setCellValue('D'.($i+1), 'Grupo');
            $sheet->mergeCells('D'.$i.':E'.$i);
            $sheet->getStyle('D'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $i++;
             $suma=0;
        $total=0;
            foreach ($datos as $value ){
                 $i++;
                $total+= $suma= $balance->get_cta_inicial($value['fk_codagr_ini'],$value['fk_codagr_fin'],$periodo);
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('D'.$i, $value['grupo'])
                            ->setCellValue('E'.$i, number_format($suma,2,'.',','));
            }
            $i++;
             $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('D'.$i, "Total: ")
                ->setCellValue('E'.$i, " ")
                ->setCellValue('F'.$i, number_format($total,2,'.',','));   
            $totalCostos+=$total;   
        $i++;
    }

    $total=0;
    $i=3;

    foreach($apartados_gastos as $gastos){
            $datos= $balance->get_balance($gastos->rowid,$estado); 
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('G'.$i,  $gastos->apartado  )
                        ->setCellValue('G'.($i+1), 'Grupo');
            $sheet->mergeCells('G'.$i.':H'.$i);
            $sheet->getStyle('G'.$i.':H'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $i++;
             $suma=0;
        $total=0;
            foreach ($datos as $value ){
                 $i++;
                $total+= $suma= $balance->get_cta_inicial($value['fk_codagr_ini'],$value['fk_codagr_fin'],$periodo);
                $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('G'.$i, $value['grupo'])
                            ->setCellValue('H'.$i, number_format($suma,2,'.',','));
            }
            $i++;
             $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('G'.$i, "Total: ")
                ->setCellValue('H'.$i, " ")
                ->setCellValue('I'.$i, number_format($total,2,'.',','));   
            $totalGastos+=$total;   
        $i++;
    }

     $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('j3', 'Totales' )
                ->setCellValue('j4', 'Total Ventas' )
                ->setCellValue('j5', 'Total Costos' )
                ->setCellValue('j6', 'Total Gastos' )
                ->setCellValue('j7', 'Utilidad Bruta' )
                ->setCellValue('j8', 'Utilidad Operativa' )
                ->setCellValue('k4', number_format($totalVentas,2,'.',',') )
                ->setCellValue('k5', number_format($totalCostos,2,'.',',') )
                ->setCellValue('k6', number_format($totalGastos,2,'.',',') )
                ->setCellValue('k7', number_format($totalVentas-$totalCostos,2,'.',',') )
                ->setCellValue('k8', number_format(($totalVentas-$totalCostos)-$totalGastos,2,'.',',') );
    $sheet->mergeCells('j3:k3');
    $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
    $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(30);
    $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
    $objPHPExcel->getActiveSheet()->getStyle('A1:D999')->getAlignment()->setWrapText(true);

    // Set active sheet index to the first sheet, so Excel opens this as the first sheet
    $objPHPExcel->setActiveSheetIndex(0);

    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="'.$filename.'"');
    header('Cache-Control: max-age=0');

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
    $objWriter->save('php://output');
    exit;

	
?>