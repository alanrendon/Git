<?php
	
	$res=@include("../../main.inc.php");
	require_once '../lib/Classes/PHPExcel.php';


	$TNR18 = new PHPExcel_Style();
	$TNR18->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>18, 'color' => array('rgb' => '000000')),));

	$TNR20 = new PHPExcel_Style();
	$TNR20->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>20, 'color' => array('rgb' => '000000')),));

	
	$TNR22 = new PHPExcel_Style();
	$TNR22->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>22, 'color' => array('rgb' => '000000')),));

	$A14 = new PHPExcel_Style();
	$A14->applyFromArray(	array('font' => array('name' => 'Arial','size' =>14, 'bold'=> true, 'color' => array('rgb' => '000000')),));

	$A142 = new PHPExcel_Style();
	$A142->applyFromArray(	array('font' => array('name' => 'Arial','size' =>14,  'color' => array('rgb' => '000000')),));

	$A14bord = new PHPExcel_Style();
	$A14bord->applyFromArray(	array('font' => array('name' => 'Arial','size' =>14,'bold'=> true, 'color' => array('rgb' => 'FE0505')), 
								'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));


	$TNR26 = new PHPExcel_Style();
	$TNR26->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>26,'bold'=> true, 'color' => array('rgb' => 'FE0505')), 
								'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));

	$Ari12 = new PHPExcel_Style();
	$Ari12->applyFromArray(	array('font' => array('name' => 'Arial','size' =>12,'color' => array('rgb' => '000000')), 
								'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));

	$TNR18bordes = new PHPExcel_Style();
	$TNR18bordes->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>18,'color' => array('rgb' => '000000')), 
								'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));
	
	$TNR16bordes = new PHPExcel_Style();
	$TNR16bordes->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>16, 'bold'=> true, 'color' => array('rgb' => '000000')), 
								'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));

	$Ari16bordes = new PHPExcel_Style();
	$Ari16bordes->applyFromArray(	array('font' => array('name' => 'Arial','size' =>16,'color' => array('rgb' => '000000')), 
								'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));

	$Ari12bordes = new PHPExcel_Style();
	$Ari12bordes->applyFromArray(	array('font' => array('name' => 'Arial','size' =>12,'color' => array('rgb' => '000000')), 
								'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));

	$Ari14bordes = new PHPExcel_Style();
	$Ari14bordes->applyFromArray(	array('font' => array('name' => 'Arial','size' =>14,'bold'=> true, 'color' => array('rgb' => '000000')), 
								'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101'))),
       							'alignment' =>  array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER)));

	$Ari14bordes2 = new PHPExcel_Style();
	$Ari14bordes2->applyFromArray(	array('font' => array('name' => 'Arial','size' =>14, 'color' => array('rgb' => '000000')), 
								'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ,	'color' => array('rgb' => '010101'))),
       							'alignment' =>  array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER)));

	$Ari14bordes3 = new PHPExcel_Style();
	$Ari14bordes3->applyFromArray(	array('font' => array('name' => 'Arial','size' =>14,'bold'=> true, 'color' => array('rgb' => '000000')), 
		'fill' 	=> array(
			'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
			'color'		=> array('rgb' => 'DBE5F1')
		),	'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101'))),
       							'alignment' =>  array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER)));



	$Ari20 = new PHPExcel_Style();
	$Ari20->applyFromArray(	array('font' => array('name' => 'Arial','size' =>20,'bold'=> true, 'color' => array('rgb' => 'FE0505')),));


	$estiloInformacion = new PHPExcel_Style();
	$estiloInformacion->applyFromArray(
		array(
       		'font' => array(
           	'name'      => 'Times New Roman',   
           	'size' =>26,            
           	'color'     => array(
               	'rgb' => '000000'
           	)
       	),
       	'fill' 	=> array(
			'type'		=> PHPExcel_Style_Fill::FILL_SOLID,
			'color'		=> array('rgb' => 'DBE5F1')
		),
       	'borders' => array(
           	'left'     => array(
               	'style' => PHPExcel_Style_Border::BORDER_THIN ,
                'color' => array(
	            	'rgb' => 'DBE5F1'
               	)
           	)             
       	),
       	'alignment' =>  array(
        			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
        			'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,
        			'wrap'          => TRUE
    		)
        ));

	$bordes = new PHPExcel_Style();
	$bordes->applyFromArray(
		array(       		
	       	'borders' => array(
	           	'allborders'     => array(
	               	'style' => PHPExcel_Style_Border::BORDER_MEDIUM ,
	                'color' => array(
		            	'rgb' => '010101'
	               	)
	           	)      
	       	)
        ));

	$bordesRight = new PHPExcel_Style();
	$bordesRight->applyFromArray(
		array(       		
	       	'borders' => array(
	           	'right'     => array(
	               	'style' => PHPExcel_Style_Border::BORDER_NONE 	                
	           	)      
	       	)
        ));

	// Create new PHPExcel object
	$objPHPExcel = new PHPExcel();

	// Set document properties
	$objPHPExcel->getProperties()->setCreator("Dolibarr")
								 ->setLastModifiedBy("Dolibarr")
								 ->setTitle("COTIZACION")
								 ->setSubject("Cotizacion")
								 ->setDescription("")
								 ->setKeywords("")
								 ->setCategory("Formatos");


	// Add some data
	$objPHPExcel->setActiveSheetIndex(0)
	            ->setCellValue('A1', 'Nombre Empresa')
	            ->setCellValue('A2', 'No Proveedor')
	            ->setCellValue('A4', $conf->global->MAIN_INFO_SOCIETE_NOM)
	            ->setCellValue('A9', "Domicilio ".$conf->global->MAIN_INFO_SOCIETE_ADDRESS." ".$conf->global->MAIN_INFO_SOCIETE_TOWN." C.P.".$conf->global->MAIN_INFO_SOCIETE_ZIP)
	            ->setCellValue('M9', 'Maq.16') ///--------------------------------------------
	            ->setCellValue('O9', 'XXX') //---------------------------------------------------
	            ->setCellValue('A13', 'CLIENTE') ///--------------------------------------------
	            ->setCellValue('A14', 'DIRECCIÓN') ///--------------------------------------------
	            ->setCellValue('A16', 'No proyecto')
	            ->setCellValue('C16', 'XXX_XXXX_XXX_XXXXX_XXX ÑÑÑÑ') //---------------------------------------------------
	            ->setCellValue('A17', 'Solicitante')
	            ->setCellValue('C17', '----------') //---------------------------------------------------
	            ->setCellValue('A18', 'Nombre del Proy.')
	            ->setCellValue('C18', '0') //---------------------------------------------------
	            ->setCellValue('M13', 'Fecha')///--------------------------------------------
	            ->setCellValue('C20','Tiempo de entrega puede variar debido a cambios de diseño modificaciones o falta de información')
	            ->setCellValue('A21', 'PARTE')
	            ->setCellValue('B21', 'DESCRIPCIÓN')
	            ->setCellValue('J21', 'CANTIDAD DE HORAS')
	            ->setCellValue('L21', 'PRECIO UNITARIO')
	            ->setCellValue('N21', 'IMPORTE')
	            ->setCellValue('L15', 'Tiempo de entrega')
	            ->setCellValue('N15', 'Dias') //----------------------------------------------------------;
	            ->setCellValue('L16', 'Vigencia cotización')
	            ->setCellValue('N16', '15 Dias') //----------------------------------------------------------;
	            ->setCellValue('L17', 'Moneda')	            
	            ->setCellValue('N17', 'Pesos Mexicanos') //----------------------------------;
	            ->setCellValue('L18', 'Lugar de Entrega')
	            ->setCellValue('N18', 'Continental, Almacén General') //----------------------------------------------------------;
	            ->setCellValue('L19', 'Condiciones de Pago')
	            ->setCellValue('N19', '15 días A partir de la entrega') 
	            ->setCellValue('B22', 'Perfiladora y Torno CNC') 
	            ->setCellValue('B24', 'Rectificado') 
	            ->setCellValue('B25', 'CNC especial ')
	            ->setCellValue('A26', 'TOTAL HORAS DE MAQUINADO ')
	            ->setCellValue('B28', 'Materiales y tratamientos ')
	            ->setCellValue('B30', 'Servicios Especiales ')
	            ->setCellValue('A32', 'TOTAL ')
	            ->setCellValue('A34', 'TOTAL PIEZAS ')
	            ->setCellValue('A35', 'TOTAL DIBUJOS ')
	            ->setCellValue('A36', 'OBSERVACIONES ') 
	            ->setCellValue('L34', 'Descuento ') 
	            ->setCellValue('N34', '% ') 
	            ->setCellValue('L35', 'SUB TOTAL ') 
	            ->setCellValue('L36', 'IVA 16% ') 
	            ->setCellValue('L37', 'TOTAL ') 
	            ->setCellValue('B37', '1. Requerimientos ') 
	            ->setCellValue('E37', '1.1  Orden de compra ') 
	            ->setCellValue('B38', '2. Características Generales: ')
	            ->setCellValue('E38','2.1 Materiales y tratamientos son Las que se señala en los dibujos de Diseño y Soluciones Competitivas')
	            ->setCellValue('B40', 'Agradecemos de antemano su interés en nuestra cotización, Para proveer cualquier información adicional estamos a sus órdenes.') ;





	 //-----------------------------Combinar celdas----------------------------------
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells('A4:F7');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A9:K9');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('M9:N9');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('O9:P9');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('M13:P13');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A13:H13');   
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A14:H14');   
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A15:H15');   
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A16:B16');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C16:H16');   
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A17:B17');   
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C17:H17');   
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A18:B18');  
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('C18:H18');   
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A19:H19');   
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L15:M15');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N15:P15');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L16:M16');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N16:P16');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L17:M17');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N17:P17');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L18:M18');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N18:P18');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L19:M19');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N19:P19');

    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('B21:I21');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('J21:K21');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L21:M21');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N21:P21');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A26:I26');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A32:M32');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N32:P32');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L34:M34');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L35:M35');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L36:M36');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('L37:M37');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N34:P34');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N35:P35');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N36:P36');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('N37:P37');

    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A34:B34');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A35:B35');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('A35:B35');

    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D34:J34');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D35:J35');
    $objPHPExcel->setActiveSheetIndex(0)->mergeCells('D36:J36');


    $cont=22;
    while ($cont <= 31) {
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('B'.$cont.':I'.$cont);    	
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('J'.$cont.':K'.$cont);    	
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('L'.$cont.':M'.$cont);    	
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('N'.$cont.':P'.$cont);    	
		$cont++;
    }

    
    


    
    
    
///---------------------//Dimensiones de las celdas -----------------------------------
    //$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
	$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(0);
	$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(0);
	$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
	$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(13);
	$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(13);

	$objPHPExcel->getActiveSheet()->getRowDimension('21')->setRowHeight(58);


///---------------------//ESTILOS-----------------------------------
    $objPHPExcel->getActiveSheet()->setSharedStyle($TNR26, "M9:P9");
    $objPHPExcel->getActiveSheet()->setSharedStyle($TNR20, "A1:A2");
    $objPHPExcel->getActiveSheet()->setSharedStyle($TNR18, "A9:K9");
    $objPHPExcel->getActiveSheet()->setSharedStyle($TNR18bordes, "A13:H19");
    $objPHPExcel->getActiveSheet()->setSharedStyle($TNR16bordes, "L15:M19");
    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari12bordes, "N15:P19");
    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari16bordes, "M13:P13");
    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari20, "C20");
    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari14bordes, "A21:P21");
    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari14bordes2, "A22:P25");
    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari14bordes3, "A26:P26");
    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari14bordes3, "A32:P32");
    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari14bordes2, "A27:P31");

    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari14bordes, "A34:B35");
    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari14bordes, "L34:M37");

    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari14bordes2, "D34:J36");
    $objPHPExcel->getActiveSheet()->setSharedStyle($Ari14bordes2, "N34:P37");
    $objPHPExcel->getActiveSheet()->setSharedStyle($A14bord,'A36:A37');
    $objPHPExcel->getActiveSheet()->setSharedStyle($A14,'B37:B40');
    $objPHPExcel->getActiveSheet()->setSharedStyle($A142,'E37:E38');

    //$objPHPExcel->getActiveSheet()->setSharedStyle($bordesRight, "M9:N9");
    $objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "A4:F7");

	
	// Rename worksheet
	$objPHPExcel->getActiveSheet()->setTitle('Cotizacion');
	$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(80);


	// Set active sheet index to the first sheet, so Excel opens this as the first sheet
	$objPHPExcel->setActiveSheetIndex(0);


	// Redirect output to a client’s web browser (Excel5)
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment;filename="Cotizacion.xls"');
	header('Cache-Control: max-age=0');
	// If you're serving to IE 9, then the following may be needed
	header('Cache-Control: max-age=1');

	// If you're serving to IE over SSL, then the following may be needed
	header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
	header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
	header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
	header ('Pragma: public'); // HTTP/1.0

	$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	$objWriter->save('php://output');
	exit;
?>