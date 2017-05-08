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


    $balance    = new Balance();
    $apartados  = new Apartados();
    $estado     = 1;


    $apartados_pasivos = $apartados->get_apartados_obj_pasivo($estado);
    $apartados_activo  = $apartados->get_apartados_obj_activo($estado);
    $apartados_capital = $apartados->get_apartados_obj_capital($estado);
    
    $totalCapital =0;
    $totalActivo  =0;
    $totalPasivo  =0;

					// filename for download
    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("Auribox")
				->setTitle("Póliza")
				->setSubject("Office 2007 XLSX")
				->setDescription("Pólizas XLSX, generado usando clases PHP.")
				->setKeywords("office 2007 openxml php")
				->setCategory("Contabilidad");
    $filename = "balance_".$periodo[0]." a ".$periodo[1]." ". date('Ymd') . ".xls";
    $sheet    = $objPHPExcel->getActiveSheet();

    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Balance General correspondiente de: '.$periodo[0]." a ".$periodo[1]);

    $sheet->mergeCells('A1:H1');
    $sheet->getStyle('A1:H1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

    //Activo Circulante
    $total=0;
    $i=3;
    foreach($apartados_activo as $activo){
            $datos= $balance->get_balance($activo->rowid,$estado); 
               $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $activo->apartado )
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
                $totalActivo+=$total;
            $i++;
	}

    $total=0;
    $i=3;
    foreach($apartados_pasivos as $pasivo){
            $datos= $balance->get_balance($pasivo->rowid,$estado); 
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('D'.$i,  $pasivo->apartado  )
                        ->setCellValue('D'.($i+1), 'Grupo');
            $sheet->mergeCells('D'.$i.':E'.$i);
            $sheet->getStyle('D'.$i.':E'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $i++;
            $suma  =0;
            $total =0;
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
            $totalPasivo+=$total;   
        $i++;
    }
    $total=0;

    foreach($apartados_capital as $capital){
            $datos= $balance->get_balance($capital->rowid,$estado); 
            $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('D'.$i,  $capital->apartado  )
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
            $totalCapital+=$total;   
        $i++;
    }
    
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('G3', 'Totales' )
                ->setCellValue('G4', 'Total Pasivo' )
                ->setCellValue('G5', 'Total Activo' )
                ->setCellValue('G6', 'Capital Contable' )
                ->setCellValue('G7', 'Total pasivo + capital' )
                ->setCellValue('H4', number_format($totalPasivo,2,'.',',') )
                ->setCellValue('H5', number_format($totalActivo,2,'.',',') )
                ->setCellValue('H6', number_format($totalCapital,2,'.',',') )
                ->setCellValue('H7', number_format($totalPasivo+$totalCapital,2,'.',',') );
               
    $sheet->mergeCells('G3:H3');
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