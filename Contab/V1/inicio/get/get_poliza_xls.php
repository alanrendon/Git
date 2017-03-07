<?php
	$url[0] = "../";
	require_once "../class/poliza.class.php";
	require_once "../class/asiento.class.php";
    require_once  "../class/PHPExcel.php";
    require_once "../class/cat_cuentas.class.php";


		if (isset($_GET['descarga'])) {
			$idPoliza        = (int)$_GET['descarga'];
			$polizas         = new Poliza();
			$asiento         = new Asiento();
            $cuenta         = new Cuenta();
			$arreglo_Polizas = $polizas->getPolizaId($idPoliza);			
			if ($arreglo_Polizas>0) {
				
                $arrasiento = $asiento->get_asientoPoliza($idPoliza);
					// filename for download
                $objPHPExcel = new PHPExcel();
                $objPHPExcel->getProperties()->setCreator("Auribox")
							 ->setTitle("Póliza")
							 ->setSubject("Office 2007 XLSX")
							 ->setDescription("Pólizas XLSX, generado usando clases PHP.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Contabilidad");
				$filename = "poliza_".$idPoliza."_" . date('Ymd') . ".xls";

                foreach($arreglo_Polizas as $poliza){
                    
                    if ( $poliza['societe_type'] == 1 ){
                        $tipoDoctorelacionado =  $poliza['facnumber'];
                    }else if( $poliza['societe_type'] == 2 ){
                        $tipoDoctorelacionado = $poliza['ref'];
                    }else{
                         $tipoDoctorelacionado = "No hay docto.";
                    }
                    
                    (!empty($poliza['numcheque']))?$no_cheque=$poliza['numcheque']:$no_cheque='N/A';
                    (!empty($poliza['anombrede']))?$a_nombre=$poliza['anombrede']:$a_nombre='N/A';
                    $sheet = $objPHPExcel->getActiveSheet();
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A1', 'Póliza: '.$poliza['cons'])
                    ->setCellValue('A2', 'Fecha: '.$poliza['fecha'])
                    ->setCellValue('B2', "Concepto: ". ($poliza['concepto']))
                    ->setCellValue('D2', 'Documento Relacionado: '.$tipoDoctorelacionado)
                    ->setCellValue('A3', 'No. Cheque: '.$no_cheque)
                    ->setCellValue('A4', 'A nombre de: '.$a_nombre);
                    $sheet->mergeCells('A3:B3');
                    $sheet->mergeCells('A4:B4');
                    $sheet->mergeCells('A1:D1');
                    $sheet->mergeCells('B2:C2');
                    $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    
                }
                $i=9;
                $total_debe = 0;
				$total_habe = 0;
                if(is_array($arrasiento)){
                    $sheet = $objPHPExcel->getActiveSheet();
                    $objPHPExcel->setActiveSheetIndex(0)
                         ->setCellValue('A7', 'ASIENTOS')
                         ->setCellValue('A8', 'No.')
                         ->setCellValue('B8', 'Cuenta')
                         ->setCellValue('C8', 'Debe')
                         ->setCellValue('D8', 'Haber');
                      $sheet->mergeCells('A7:D7');
                    foreach($arrasiento as $key){
                        $total_debe += $key["debe"];
						$total_habe += $key["haber"];
                        $nom_cuenta = $cuenta->get_nom_cuenta($key["cuenta"]);
                        $objPHPExcel->setActiveSheetIndex(0)
                            ->setCellValue('A'.$i, $key["asiento"])
                            ->setCellValue('B'.$i, ($key["cuenta"].' - '.$nom_cuenta))
                            ->setCellValue('C'.$i, number_format($key["debe"],2,'.',','))
                            ->setCellValue('D'.$i,  number_format($key["haber"],2,'.',','));
                        $i++;
                    }
                    $sheet = $objPHPExcel->getActiveSheet();
                    $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$i, "Total: ")
                        ->setCellValue('C'.$i, number_format($total_debe,2,'.',','))
                        ->setCellValue('D'.$i, number_format($total_habe,2,'.',','));
                    $sheet->mergeCells('A'.$i.':B'.$i);
                    $sheet->getStyle('A7:D'.$i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }else{
                     $sheet = $objPHPExcel->getActiveSheet();
                    $objPHPExcel->setActiveSheetIndex(0)
                     ->setCellValue('A7', 'No hay asientos');
                    $sheet->mergeCells('A7:C7');
                    $sheet->getStyle('A7:C7')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                }
               
                
               $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
               $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);
               $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(30);
               $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getStyle('A1:D999')
                ->getAlignment()->setWrapText(true);

                // Set active sheet index to the first sheet, so Excel opens this as the first sheet
                $objPHPExcel->setActiveSheetIndex(0);

                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename="'.$filename.'"');
                header('Cache-Control: max-age=0');

                $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
                $objWriter->save('php://output');
                exit;
			}
		}
	
?>