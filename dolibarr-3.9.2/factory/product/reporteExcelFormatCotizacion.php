<?php
	
	$res=@include("../../main.inc.php");
	require_once '../lib/Classes/PHPExcel.php';

	if (! $res) $res=include("../../../main.inc.php");  

	require_once(DOL_DOCUMENT_ROOT."/core/class/html.formfile.class.php");

	$id=$_GET['id'];

	$sql = "SELECT";
	$sql .= " t.fk_product_father,";
	$sql .= " t.fk_product_children,";
	$sql .= " t.pmp,";
	$sql .= " t.price,";
	$sql .= " t.qty,";
	$sql .= " t.globalqty,";
	$sql .= " t.description,";
	$sql .= " t.treatment,";
	$sql .= " t2.hmcnc,";
	$sql .= " t2.hecnc,";
	$sql .= " t2.hr,";
	$sql .= " t2.esp,";
	$sql .= " t2.anc,";
	$sql .= " t2.lar,";
	$sql .= " t2.tc,";
	$sql .= " t2.fac,";
	$sql .= " t2.porcent,";
	$sql .= " t3.price_ttc,";
	$sql .= " t3.price as price_mat,";
	$sql .= " t2.fk_object,";
	$sql .= " t3.label,";
	$sql .= " t3.fk_product_type,";
	$sql .= " t3.ref";
	

	$sql.= " FROM ".MAIN_DB_PREFIX."product_factory as t
		LEFT JOIN ".MAIN_DB_PREFIX."product_extrafields as t2 on  t.fk_product_children=t2.fk_object
		INNER JOIN ".MAIN_DB_PREFIX."product as t3 on t.fk_product_children=t3.rowid";	
	$sql.= " WHERE t.fk_product_father=".$id;	

	$rql=$db->query($sql);


	$sql4 = "SELECT";
	$sql4 .= " t3.label,";
	$sql4 .= " t3.ref";	
	$sql4.= " FROM ".MAIN_DB_PREFIX."product as t3";	
	$sql4.= " WHERE t3.rowid=".$id;
	$rql4=$db->query($sql4);
	$father=$db->fetch_object($rql4);




	$sql2 = "SELECT t.price FROM ".MAIN_DB_PREFIX."product as t WHERE t.ref='cxhdm' AND t.entity=".$conf->entity;
	$r2=$db->query($sql2);
	$cxhdm=$db->fetch_object($r2);

	$sql3 = "SELECT t.price FROM ".MAIN_DB_PREFIX."product as t WHERE t.ref='cxhcnc' AND t.entity=".$conf->entity;
	$r3=$db->query($sql3);
	$cxhcnc=$db->fetch_object($r3);

	$sql4 = "SELECT t.price FROM ".MAIN_DB_PREFIX."product as t WHERE t.ref='cxhr' AND t.entity=".$conf->entity;
	$r4=$db->query($sql4);
	$cxhr=$db->fetch_object($r4);
	

	if($rql->num_rows > 0 ){

		$Ari24bordes2 = new PHPExcel_Style();
		$Ari24bordes2->applyFromArray(	array('font' => array('name' => 'Arial','size' =>24,'bold'=> true, 'color' => array('rgb' => '000000')), 
									'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN,	'color' => array('rgb' => '010101')))));

		$Ari24bordes = new PHPExcel_Style();
		$Ari24bordes->applyFromArray(	array('font' => array('name' => 'Arial','size' =>20, 'color' => array('rgb' => '000000')), 
									'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_THIN ,	'color' => array('rgb' => '010101')))));

		$Ari22 = new PHPExcel_Style();
		$Ari22->applyFromArray(	array('font' => array('name' => 'Arial','size' =>22,'bold'=> true, 'color' => array('rgb' => 'FE0505')),));

		$top = new PHPExcel_Style();
		$top->applyFromArray(array( 'borders' => array(	'top' => array('style' => PHPExcel_Style_Border::BORDER_THIN ,	'color' => array('rgb' => '000000')))));


		$TNR28 = new PHPExcel_Style();
		$TNR28->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>28, 'bold'=> true, 'color' => array('rgb' => '000000')),));


		$TNR26 = new PHPExcel_Style();
		$TNR26->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>26, 'color' => array('rgb' => '000000')), ));

		$TNR28bordes = new PHPExcel_Style();
		$TNR28bordes->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>28,'bold'=> true, 'color' => array('rgb' => 'FE0505')), 
									'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));

		$TNR28bordes2 = new PHPExcel_Style();
		$TNR28bordes2->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>28,'bold'=> true, 'color' => array('rgb' => '000000')), 
									'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));

		$TNR28bordes3 = new PHPExcel_Style();
		$TNR28bordes3->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>28, 'color' => array('rgb' => '000000')), 
									'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));

		$TNR26bordes = new PHPExcel_Style();
		$TNR26bordes->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>26 ,'bold'=> true, 'color' => array('rgb' => '000000')), 
									'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));

		$TNR26bordes2 = new PHPExcel_Style();
		$TNR26bordes2->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>26, 'color' => array('rgb' => '000000')), 
									'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));


		$estiloInformacion = new PHPExcel_Style();
		$estiloInformacion->applyFromArray(	array('font' => array('name'  => 'Times New Roman',  'size' =>26,'color' => array('rgb' => '000000'	)),
	       					'fill' 	=> array('type'		=> PHPExcel_Style_Fill::FILL_SOLID,'color'=> array('rgb' => 'DBE5F1')),
	      				 	'borders' => array(	'left' => array('style' => PHPExcel_Style_Border::BORDER_THIN ,'color' => array('rgb' => 'DBE5F1'))),
	     				  	'alignment' =>  array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE) ));

		$bordes = new PHPExcel_Style();
		$bordes->applyFromArray(
			array( 	'borders' => array(	'allborders'  => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,'color' => array('rgb' => '010101')))));

		
		$Ari22bordes = new PHPExcel_Style();
		$Ari22bordes->applyFromArray(	array('font' => array('name'  => 'Arial',  'size' =>22, 'bold'=> true, 'color' => array('rgb' => '000000'	)),
	      				 	'borders' => array(	'allborders'  => array('style' => PHPExcel_Style_Border::BORDER_THIN ,'color' => array('rgb' => '010101'))),
	     				  	'alignment' =>  array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE) ));

		$Ari22bordes2 = new PHPExcel_Style();
		$Ari22bordes2->applyFromArray(	array('font' => array('name'  => 'Arial',  'size' =>22,  'color' => array('rgb' => '000000'	)),
	      				 	'borders' => array(	'right'  => array('style' => PHPExcel_Style_Border::BORDER_THIN ,'color' => array('rgb' => '010101')), 'left'  => array('style' => PHPExcel_Style_Border::BORDER_THIN ,'color' => array('rgb' => '010101'))),
	     				  	'alignment' =>  array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,'vertical'   => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE) ));

		$TNR18 = new PHPExcel_Style();
		$TNR18->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>18, 'color' => array('rgb' => '000000')),));

		$TNR20 = new PHPExcel_Style();
		$TNR20->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>20, 'color' => array('rgb' => '000000')),));


		$A14 = new PHPExcel_Style();
		$A14->applyFromArray(	array('font' => array('name' => 'Arial','size' =>14, 'bold'=> true, 'color' => array('rgb' => '000000')),));

		$A142 = new PHPExcel_Style();
		$A142->applyFromArray(	array('font' => array('name' => 'Arial','size' =>14,  'color' => array('rgb' => '000000')),));

		$A14bord = new PHPExcel_Style();
		$A14bord->applyFromArray(	array('font' => array('name' => 'Arial','size' =>14,'bold'=> true, 'color' => array('rgb' => 'FE0505')), 
									'borders' => array(	'allborders' => array('style' => PHPExcel_Style_Border::BORDER_MEDIUM ,	'color' => array('rgb' => '010101')))));


		$C2TNR26 = new PHPExcel_Style();
		$C2TNR26->applyFromArray(	array('font' => array('name' => 'Times New Roman','size' =>26,'bold'=> true, 'color' => array('rgb' => 'FE0505')), 
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


		$C2estiloInformacion = new PHPExcel_Style();
		$C2estiloInformacion->applyFromArray(array('font' => array(	'name'  => 'Times New Roman', 'size' =>26, 	'color' => array('rgb' => '000000')),
	       	'fill' 	=> array('type'	=> PHPExcel_Style_Fill::FILL_SOLID,'color'=> array('rgb' => 'DBE5F1')),
	       	'borders' => array('left'  => array('style' => PHPExcel_Style_Border::BORDER_THIN ,'color' => array('rgb' => 'DBE5F1')) ),'alignment' =>  array('horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, 'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,'wrap' => TRUE) ));

		
		
		//*****************************************************************************************


		// Create new PHPExcel object
		$objPHPExcel = new PHPExcel();

		// Set document properties
		$objPHPExcel->getProperties()->setCreator("Dolibarr")
									 ->setLastModifiedBy("Dolibarr")
									 ->setTitle("FORMATO COTIZACION")
									 ->setSubject("formato cotizacion")
									 ->setDescription("")
									 ->setKeywords("")
									 ->setCategory("Formatos");
		//*****************************************************************************************

		$totalGlobal=0;								

		 //-----------------------------Combinar celdas----------------------------------
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C6:I9');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C11:L11');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C12:L12');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('F5:I5');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('R10:S10');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('R12:T12');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C14:J14');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C15:J17');
		//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C16:J16');
		//$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C17:J17');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C18:F18');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('G18:J18');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C19:F19');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('G19:J19');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C20:F20');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('G20:J20');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q15:R15');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('S15:T15');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q16:R16');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('S16:T16');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q17:R17');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('S17:T17');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q18:R18');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('S18:T18');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('Q19:R19');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('S19:T19');
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('D23:E23');									 

		// Add some data
		$objPHPExcel->setActiveSheetIndex(0)
		            ->setCellValue('C3', 'Datos Empresa')
		            ->setCellValue('C5', 'No Proveedor')
		            ->setCellValue('C6', $conf->global->MAIN_INFO_SOCIETE_NOM)
		            ->setCellValue('C11', "Domicilio ".$conf->global->MAIN_INFO_SOCIETE_ADDRESS." ".$conf->global->MAIN_INFO_SOCIETE_TOWN." C.P.".$conf->global->MAIN_INFO_SOCIETE_ZIP)
		            ->setCellValue('C12', 'Tel. Oficina  Fax. , Nextel ,')///--------------------------------------------
		            ->setCellValue('R10', 'N. de Propuesta') 
		            ->setCellValue('T10', '') //---------------------------------------------------
		            ->setCellValue('C14', 'CLIENTE') ///--------------------------------------------
		            ->setCellValue('C15', 'DIRECCIÓN') ///--------------------------------------------
		            ->setCellValue('C18', 'No proyecto')
		            ->setCellValue('G18', '') //---------------------------------------------------
		            ->setCellValue('C19', 'Solicitante')
		            ->setCellValue('G19', '') //---------------------------------------------------
		            ->setCellValue('C20', 'Nombre del Proy.')
		            ->setCellValue('G20', '') //---------------------------------------------------*/
		            ->setCellValue('R12', 'Fecha')///--------------------------------------------	          
		            ->setCellValue('Q15', 'Tiempo de entrega')
		            ->setCellValue('S15', 'Dias') //----------------------------------------------------------;
		            ->setCellValue('Q16', 'Vigencia cotización')
		            ->setCellValue('S16', 'Dias') //----------------------------------------------------------;
		            ->setCellValue('Q17', 'Moneda')	            
		            ->setCellValue('S17', 'Pesos Mexicanos') //----------------------------------;
		            ->setCellValue('Q18', 'Lugar de Entrega')
		            ->setCellValue('S18', '') //----------------------------------------------------------;
		            ->setCellValue('Q19', 'Condiciones de Pago')
		            ->setCellValue('S19', '') //----------------------------------------------------------;
		            ->setCellValue('C23', 'Nº de Planos')	            
		            ->setCellValue('D23', 'Nº Pieza')	            
		            ->setCellValue('F23', 'Cant.')	            
		            ->setCellValue('G23', 'Descripción')	            
		            ->setCellValue('H23', 'Horas Maquinado y CNC')	            
		            ->setCellValue('I23', 'Horas Especial CNC')
		            ->setCellValue('J23', 'Horas Rectificado.')	            
		            ->setCellValue('K23', 'Material')	            
		            ->setCellValue('L23', 'Tratamiento')
		            ->setCellValue('M23', 'Costo x hora de Maquinado   $160.00')	            
		            ->setCellValue('O23', 'Costo por  hora CNC')	            	            
		            ->setCellValue('P23', 'Costo por hora Rectificado')	            
		            ->setCellValue('Q23', 'Costo Unitario de Material')	            
		            ->setCellValue('R23', 'Costo Unitario Tratamiento.')
		            ->setCellValue('S23', 'Precio Unitario')	            
		            ->setCellValue('T23', 'Total');	       

		//Se agregan los datos
		$i = 24;
		$x=1;
		while ($rs=$db->fetch_object($rql)) {

	/*///////////////////////////Calculo del costo unitario de material////////////
				costo unitario de material =costo materia prima * utilidad de materia prima									
				utilidad de materia prima = costo de materia prima  *  % %  ejemplo  =Q24*R24%		
				peso teorico= espesor * ancho * largo * factor / 1000000
				conversion MN = precio kg * tc	
				costo materia prima = conversion MN * peso teorico		*/
				
			$pt=($rs->esp*$rs->anc*$rs->lar*$rs->fac/1000000); ////////////////Peso teorico///////////////////////		
						
			$conversionMN=$rs->price_mat*$rs->tc;/////////////////////////conversion MN/////////////////////
			
			$costMatPrima=($conversionMN*$pt); 	////////////Costo materia prima /////////////////////////
				
			$utilidadMP=(($costMatPrima*$rs->porcent)/100); ////////////////Utilidad de materia prima////////////////////

			$costUnitMat=$costMatPrima+$utilidadMP; ////////costo unitario de materia////////////////7
			
	/////////FIN del costo unitario de material////////////		
						
			$type= substr($rs->ref, 0, 4);
			$ban=0;
	
			$tre=0;
			$costoUnitarioTrat=0;

			if($rs->treatment==0 || $rs->treatment==null){
				$tre=0;
			}else{
				$tre=$rs->treatment;

				/*//////////////calculo de Costo Unitario Tratamiento
					Costo Unitario de trat.=costo tratamiento + UTILIDAD TRATAMIENTO	
					costo tratamiento=FACTOR * AREA CUADRADA
					AREA CUADRADA =ANCHO PULGADAS * LARGO PULGADAS
					ANCHO PULGADAS = ancho milimetros / 25.4
					LARGO PULGADAS = largo milimetros / 25.4
					UTILIDAD TRATAMIENTO = costo tratamiento *  %utilidad %  ejemplo  =Y24*Z24%*/						

				$string = "SELECT t.anc, t.lar, t.fac";	
				$string.= " FROM ".MAIN_DB_PREFIX."product_extrafields as t";	
				$string.= " WHERE t.fk_object=".$rs->fk_product_children;
				$dataTrat=$db->query($string);
				$resTrat=$db->fetch_object($dataTrat);

				$largo=($resTrat->lar/25.4);				
				$ancho=($resTrat->anc/25.4);				
				$area=$largo*$ancho;				

				$string = "SELECT t.porcent, t.fac ";	
				$string.= " FROM ".MAIN_DB_PREFIX."product_extrafields as t";	
				$string.= " WHERE t.fk_object=".$tre;
				$dataTrat=$db->query($string);
				$resTrat=$db->fetch_object($dataTrat);			

				$costoTrat=($resTrat->fac*$area);			

				$utilidad=(($costoTrat*$resTrat->porcent)/100);
				$costoUnitarioTrat=$costoTrat+$utilidad;			
				/////////////Fin calculo costo unitario tratamiento*/		
			}

				$sql4 = "SELECT";
				$sql4 .= " t3.label,";
				$sql4 .= " t3.ref";	
				$sql4.= " FROM ".MAIN_DB_PREFIX."product as t3";	
				$sql4.= " WHERE t3.rowid=".$tre;
				$rql4=$db->query($sql4);
				$rs4=$db->fetch_object($rql4);

				$objPHPExcel->setActiveSheetIndex(0)
        		    ->setCellValue('C'.$i, $x)
		            ->setCellValue('D'.$i, $father->ref)        		    
            		->setCellValue('F'.$i, $rs->qty)
            		->setCellValue('G'.$i, $father->label)
            		->setCellValue('H'.$i, $rs->hmcnc)
            		->setCellValue('I'.$i, $rs->hecnc)
            		->setCellValue('J'.$i, $rs->hr)
            		->setCellValue('K'.$i, $rs->label)
            		->setCellValue('L'.$i, $rs4->label)
            		->setCellValue('M'.$i, $cxhdm->price)
            		->setCellValue('O'.$i, $cxhcnc->price)
            		->setCellValue('P'.$i, $cxhr->price)
            		->setCellValue('Q'.$i, '=round('.$costUnitMat.',2)')
            		->setCellValue('R'.$i, '=round('.$costoUnitarioTrat.',2)')            		
            		->setCellValue('S'.$i,'=T'.$i.'/F'.$i.'')               		
            		->setCellValue('T'.$i,'=SUM(H'.$i.'*M'.$i.')+(I'.$i.'*O'.$i.')+(J'.$i.'*P'.$i.')+((Q'.$i.'+R'.$i.')*F'.$i.')'); 
            	
			$i++;
			$x++;	
			$totalGlobal+=(($costUnitMat+$costoUnitarioTrat)*$rs->qty);
					
		}

		$tot=
		$objPHPExcel->setActiveSheetIndex(0)
        		    ->setCellValue('C'.$i, 'Observaciones')
        		    ->setCellValue('S'.$i, 'Servicios Especiales')
        		    ->setCellValue('S'.($i+1), 'Descuento')
        		    ->setCellValue('S'.($i+2), 'Sub. total')
        		    ->setCellValue('T'.($i+2), '=SUM(T24:T'.($i-1).')')
        		    ->setCellValue('S'.($i+3), 'I.V.A ')
        		    ->setCellValue('T'.($i+3), '=+(T'.($i+2).'*0.16)')        		    
        		    ->setCellValue('S'.($i+4), 'Total')
        		    ->setCellValue('T'.($i+4), '=+(T'.($i+2).'+'.'T'.($i+3).')')
        		    ->setCellValue('F'.($i+1), 'Total de dibujos')
        		    ->setCellValue('H'.($i+1),  ($i-24))
        		    ->setCellValue('F'.($i+2), 'Total de piezas')
        		    ->setCellValue('H'.($i+2),  '=SUM(F24:F'.($i-1).')')
        		    ->setCellValue('F'.($i+3), 'Horas Maquinado')
        		    ->setCellValue('H'.($i+3),  '=SUM(H24:H'.($i-1).')')
        		    ->setCellValue('F'.($i+4), 'Horas de CNC')
        		    ->setCellValue('H'.($i+4),  '=SUM(I24:I'.($i-1).')')
        		    ->setCellValue('F'.($i+5), 'Horas de Rectificado')  
        		    ->setCellValue('H'.($i+5),  '=SUM(J24:J'.($i-1).')')
        		    ->setCellValue('I'.($i+1), '(No de Planos)')
        		    ->setCellValue('K'.($i+1), ' 1. Requerimientos')
        		    ->setCellValue('K'.($i+2), '1.1  Orden de compra')
        		    ->setCellValue('K'.($i+4), '2. Características Generales:')
        		    ->setCellValue('K'.($i+5), 'Las que se señala en los dibujos de Diseño y Soluciones Competitivas	')
        		    ->setCellValue('D'.($i+9), 'Agradecemos de antemano su interés en nuestra cotización. ')
        		    ->setCellValue('D'.($i+10), 'Para proveer cualquier información adicional estamos a sus órdenes.');

        		          		    
        		    


		
		$objPHPExcel->setActiveSheetIndex(0)->mergeCells('C'.$i.':F'.$i.'');

		$cont=24;
		while ($cont <=($i-1)){
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('D'.$cont.':E'.($cont));	
			$cont++;
		}

		$cont=0;
		while ($cont <=5){
			$objPHPExcel->setActiveSheetIndex(0)->mergeCells('F'.($i+$cont).':G'.($i+$cont));	
			$cont++;
		}

	///---------------------//ESTILOS-----------------------------------
		//$objPHPExcel->getActiveSheet()->setSharedStyle($Ari24bordes, "S".($i+1).":T".($i+1)."");
		$objPHPExcel->getActiveSheet()->setSharedStyle($Ari24bordes, "S".$i.":T".($i+1));
		$objPHPExcel->getActiveSheet()->setSharedStyle($Ari24bordes, "S".($i+3).":T".($i+4));
		$objPHPExcel->getActiveSheet()->setSharedStyle($Ari24bordes2, "S".($i+2).":T".($i+2));
		$objPHPExcel->getActiveSheet()->setSharedStyle($Ari22bordes2, "C24:T".($i-1));
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR28, "C3");
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR26, "C5");
		$objPHPExcel->getActiveSheet()->setSharedStyle($estiloInformacion, "C6:I9");
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR26, "C11:L11");
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR26, "C12:L12");
		$objPHPExcel->getActiveSheet()->setSharedStyle($bordes, "F5:I5");
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR28bordes, "R10:T10");
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR28bordes2, "R12:T12");
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR28bordes3, "C14:J17");
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR26bordes, "C16:F20");
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR26bordes2, "G18:J20");
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR26bordes, "Q15:R19");
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR26bordes2, "S15:T19");
		$objPHPExcel->getActiveSheet()->setSharedStyle($Ari22bordes, "C23:T23");
		$objPHPExcel->getActiveSheet()->setSharedStyle($top, "C".$i.":R".$i."");
		$objPHPExcel->getActiveSheet()->setSharedStyle($Ari22, "C".$i."");
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR26,  "D".($i+1).":G".($i+10));
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR28,  "H".($i+1).":H".($i+5));
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR28,  "K".($i+1));
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR26,  "K".($i+2));
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR28,  "K".($i+4));
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR26,  "K".($i+5));
		$objPHPExcel->getActiveSheet()->setSharedStyle($TNR26,  "I".($i+1));



	///---------------------//Dimensiones de las celdas -----------------------------------
	    //$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(1);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(1);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(42);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(44);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(24);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(0);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(33);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(31);
		$objPHPExcel->getActiveSheet()->getColumnDimension('Q')->setWidth(23);
		$objPHPExcel->getActiveSheet()->getColumnDimension('R')->setWidth(23);
		$objPHPExcel->getActiveSheet()->getColumnDimension('S')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('T')->setWidth(30);
		$objPHPExcel->getActiveSheet()->getColumnDimension('U')->setWidth(13);

		$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(1);
		$objPHPExcel->getActiveSheet()->getRowDimension('2')->setRowHeight(1);
		$objPHPExcel->getActiveSheet()->getRowDimension('23')->setRowHeight(117);

		
		// Rename worksheet
		$objPHPExcel->getActiveSheet()->setTitle('FORMATOCOTIZACION');

		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(40);
		$objPHPExcel->getActiveSheet()->setShowGridlines(false);



		////////////////*******************************************************************
		////////////////*******************************************************************
		////////////////***************************Hoja 2****************************************
		////////////////*******************************************************************
		////////////////*******************************************************************

		$objPHPExcel->createSheet(1);
		$objPHPExcel->setActiveSheetIndex(1); //Seleccionar la pestaña deseada		
		$objPHPExcel->getActiveSheet()->setTitle('COTICACION C');
		$objPHPExcel->getActiveSheet()->setShowGridlines(false);

		$objPHPExcel->setActiveSheetIndex(1)
            ->setCellValue('A1', 'Nombre Empresa')
            ->setCellValue('A2', 'No Proveedor')
            ->setCellValue('A4', $conf->global->MAIN_INFO_SOCIETE_NOM)
            ->setCellValue('A9', "Domicilio ".$conf->global->MAIN_INFO_SOCIETE_ADDRESS." ".$conf->global->MAIN_INFO_SOCIETE_TOWN." C.P.".$conf->global->MAIN_INFO_SOCIETE_ZIP)
            ->setCellValue('M9', 'Maq') ///--------------------------------------------
            ->setCellValue('O9', '') //---------------------------------------------------
            ->setCellValue('A13', 'CLIENTE') ///--------------------------------------------
            ->setCellValue('A14', 'DIRECCIÓN') ///--------------------------------------------
            ->setCellValue('A16', 'No proyecto')
            ->setCellValue('C16', '') //---------------------------------------------------
            ->setCellValue('A17', 'Solicitante')
            ->setCellValue('C17', '') //---------------------------------------------------
            ->setCellValue('A18', 'Nombre del Proy.')
            ->setCellValue('C18', '') //---------------------------------------------------
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
            ->setCellValue('N18', 'Continental, Almacén General') //
            ->setCellValue('L19', 'Condiciones de Pago')
            ->setCellValue('N19', '15 días A partir de la entrega') 
            ->setCellValue('B22', 'Perfiladora y Torno CNC') 
            ->setCellValue('J22', "='FORMATOCOTIZACION'!H".($i+3)."")
            ->setCellValue('L22', "='FORMATOCOTIZACION'!M24")
          	->setCellValue('N22', "=L22*J22")
            ->setCellValue('B24', 'Rectificado') 
            ->setCellValue('J24', "='FORMATOCOTIZACION'!H".($i+5)."")
            ->setCellValue('L24', "='FORMATOCOTIZACION'!P24")
            ->setCellValue('N24', "=L24*J24")
            ->setCellValue('B25', 'CNC especial ')
            ->setCellValue('J25', "='FORMATOCOTIZACION'!H".($i+4)."")
            ->setCellValue('L25', "='FORMATOCOTIZACION'!O24")
            ->setCellValue('N25', "=L25*J25")
            ->setCellValue('A26', 'TOTAL HORAS DE MAQUINADO ')
            ->setCellValue('J26', "=SUM(J22:K25)")
           	->setCellValue('N26', "=N22+N23+N24+N25")
            ->setCellValue('B28', 'Materiales y tratamientos ')
            //->setCellValue('L28', "=(SUM('FORMATOCOTIZACION'!Q24:Q".($i-1).")+SUM('FORMATOCOTIZACION'!R24:R".($i-1)."))")
            ->setCellValue('L28', '=round('.$totalGlobal.',2)')
            ->setCellValue('N28', "=L28")
            ->setCellValue('B30', 'Servicios Especiales ')
            ->setCellValue('A32', 'TOTAL ')
            ->setCellValue('N32', "=SUM(N28:P31)")
            ->setCellValue('A34', 'TOTAL PIEZAS ')
            ->setCellValue('D34', "='FORMATOCOTIZACION'!H".($i+2)."")
            ->setCellValue('A35', 'TOTAL DIBUJOS ')
            ->setCellValue('N35', "=N32+N26")
            ->setCellValue('D35', "='FORMATOCOTIZACION'!H".($i+1)."")
            ->setCellValue('A36', 'OBSERVACIONES ') 
            ->setCellValue('N36', "=+N35*0.16")            
            ->setCellValue('L34', 'Descuento ') 
            ->setCellValue('N34', '% ') 
            ->setCellValue('L35', 'SUB TOTAL ') 
            ->setCellValue('L36', 'IVA 16% ') 
            ->setCellValue('L37', 'TOTAL ') 
            ->setCellValue('N37', "=N35+N36")
            ->setCellValue('B37', '1. Requerimientos ') 
            ->setCellValue('E37', '1.1  Orden de compra ') 
            ->setCellValue('B38', '2. Características Generales: ')
            ->setCellValue('E38','2.1 Materiales y tratamientos son Las que se señala en los dibujos de Diseño y Soluciones Competitivas')
            ->setCellValue('B40', 'Agradecemos de antemano su interés en nuestra cotización, Para proveer cualquier información adicional estamos a sus órdenes.') ;


	        //-----------------------------Combinar celdas----------------------------------
		$objPHPExcel->setActiveSheetIndex(1)->mergeCells('A4:F7');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A9:K9');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('M9:N9');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('O9:P9');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('M13:P13');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A13:H13');   
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A14:H15');   
	    //$objPHPExcel->setActiveSheetIndex(1)->mergeCells('A15:H15');   
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A16:B16');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('C16:H16');   
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A17:B17');   
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('C17:H17');   
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A18:B18');  
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('C18:H18');   
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A19:H19');   
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('L15:M15');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('N15:P15');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('L16:M16');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('N16:P16');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('L17:M17');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('N17:P17');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('L18:M18');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('N18:P18');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('L19:M19');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('N19:P19');

	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('B21:I21');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('J21:K21');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('L21:M21');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('N21:P21');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A26:I26');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A32:M32');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('N32:P32');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('L34:M34');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('L35:M35');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('L36:M36');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('L37:M37');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('N34:P34');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('N35:P35');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('N36:P36');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('N37:P37');

	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A34:B34');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A35:B35');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('A35:B35');

	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('D34:J34');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('D35:J35');
	    $objPHPExcel->setActiveSheetIndex(1)->mergeCells('D36:J36');


	    $cont=22;
	    while ($cont <= 31) {
			$objPHPExcel->setActiveSheetIndex(1)->mergeCells('B'.$cont.':I'.$cont);    	
			$objPHPExcel->setActiveSheetIndex(1)->mergeCells('J'.$cont.':K'.$cont);    	
			$objPHPExcel->setActiveSheetIndex(1)->mergeCells('L'.$cont.':M'.$cont);    	
			$objPHPExcel->setActiveSheetIndex(1)->mergeCells('N'.$cont.':P'.$cont);    	
			$cont++;
	    }


	    
	    
	    
	///---------------------//Dimensiones de las celdas -----------------------------------
	    //$objPHPExcel->getActiveSheet()->getColumnDimension("A")->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(0);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(0);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(17);
		$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getColumnDimension('N')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('O')->setWidth(13);
		$objPHPExcel->getActiveSheet()->getColumnDimension('P')->setWidth(13);

		$objPHPExcel->getActiveSheet()->getRowDimension('21')->setRowHeight(58);

		$c=22;
		while ( $c<= 40) {
			$objPHPExcel->getActiveSheet()->getRowDimension($c)->setRowHeight(30);
			$c++;
		}

		///---------------------//ESTILOS-----------------------------------
	    $objPHPExcel->getActiveSheet()->setSharedStyle($C2TNR26, "M9:P9");
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
	    $objPHPExcel->getActiveSheet()->setSharedStyle($A14bord,'A36:B36');
	    $objPHPExcel->getActiveSheet()->setSharedStyle($A14,'B37:B40');
	    $objPHPExcel->getActiveSheet()->setSharedStyle($A142,'E37:E38');
	    $objPHPExcel->getActiveSheet()->setSharedStyle($C2estiloInformacion, "A4:F7");


		$objPHPExcel->getActiveSheet()->getSheetView()->setZoomScale(80);



		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);


		// Redirect output to a client’s web browser (Excel5)
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="FormatoCotizacion.xls"');
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
	}else{
		print_r('No hay resultados para mostrar');
	}
?>