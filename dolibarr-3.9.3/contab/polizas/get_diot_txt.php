<?php 
	

	$url[0] = '../';
	$url[4] = '../polizas/';

	//require_once '../class/poliza.class.php';
	//require_once '../class/periodo.class.php';
	//require_once '../class/fact_prov_pendientes.class.php';
	require 'get_data_xml.php';



	$res=0;
	if (! $res && file_exists("../main.inc.php")) $res=@include '../main.inc.php';					// to work if your module directory is into dolibarr root htdocs directory
	if (! $res && file_exists("../../main.inc.php")) $res=@include '../../main.inc.php';			// to work if your module directory is into a subdir of root htdocs directory
	if (! $res && file_exists("../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../dolibarr/htdocs/main.inc.php';     // Used on dev env only
	if (! $res && file_exists("../../../../dolibarr/htdocs/main.inc.php")) $res=@include '../../../../dolibarr/htdocs/main.inc.php';   // Used on dev env only
	if (! $res) die("Include of main fails");



	function get_ultimo_periodo_abierto()
	{
		global $db;
		$sql ="
		SELECT
			max(anio) as anio,
	        max(mes) as mes,
	        (
			CASE max(mes)
				WHEN '1' THEN
					'Enero'
				WHEN '2' THEN
					'Febrero'
				WHEN '3' THEN
					'Marzo'
				WHEN '4' THEN
					'Abril'
				WHEN '5' THEN
					'Mayo'
				WHEN '6' THEN
					'Junio'
				WHEN '7' THEN
					'Julio'
				WHEN '8' THEN
					'Agosto'
				WHEN '9' THEN
					'Septiembre'
				WHEN '10' THEN
					'Octubre'
				WHEN '11' THEN
					'Noviembre'
				WHEN '12' THEN
					'Dicembre'
	            WHEN '13' THEN
					'Ajuste'
				END
			) AS mes_name
		FROM
		llx_contab_periodos
	    WHERE
			estado IN(1,2)
	    ";
		$query= $db->query($sql);
		if ($query) {
			return $db->fetch_object($query);
		}
		return false;
	}


	function getPolizas_diot($anio, $mes,$id_prov="",$format,$diot) {
        $rows = array();
        global $db,$conf;
        $periodomes = 'poliza.mes';
        if ($mes == 13) {
            $mes = 12;
            $ajuste = 1;
            $periodomes = 13;
        }
        $sql = '
        	SELECT
				poliza.rowid AS "id",
				a.nom,
				CONCAT(
					DATE_FORMAT(poliza.fecha, "%y%c"),
					"-",
					poliza.tipo_pol,
					"-",
					poliza.cons
				) AS cons,
				poliza.tipo_pol AS tipol_l,
				poliza.cons npol,
				b.fk_proveedor
			FROM
				llx_contab_polizasdet AS b 
			RIGHT JOIN llx_contab_polizas AS poliza ON b.fk_poliza = poliza.rowid
			RIGHT JOIN llx_contab_periodos AS p ON p.anio = poliza.anio
			LEFT JOIN llx_contab_societe as a ON a.rowid=b.fk_proveedor';

			if ($diot==3 || $diot==1) {
				$sql.='
				LEFT JOIN llx_contab_doc as c ON c.folio=
				CONCAT(
					DATE_FORMAT(poliza.fecha, "%y"),
					IF(DATE_FORMAT(poliza.fecha, "%c")<10,CONCAT(0,DATE_FORMAT(poliza.fecha, "%c")),DATE_FORMAT(poliza.fecha, "%c") ),
					"-",
					poliza.tipo_pol,
					"-",
					poliza.cons
				)';
			}
			

			$sql.='
			WHERE
				(poliza.tipo_pol = "E" OR poliza.contabilizar_pol=1)
			AND p.mes = ' . $periodomes . '
			AND poliza.entity = ' . $conf->entity . '
			AND p.mes = ' . $periodomes . '
			AND poliza.anio = ' . $anio . '
			AND poliza.mes = ' . $mes;
			if ($diot==3) {
				$sql.='
					AND (c.rowid>0 OR (b.fk_proveedor>-1 AND b.fk_proveedor IS NOT NULL) )
				';
			}elseif ($diot==2) {
				
				$sql.='
					AND b.fk_proveedor>-1 AND b.fk_proveedor IS NOT NULL
				';
			}elseif ($diot==1) {
				$sql.='
					AND c.rowid>0 
				';
			}
			
		if (!empty($id_prov) && $id_prov>0) {
			$sql.='AND b.fk_proveedor = '.$id_prov;
		}
			
		$sql.='
			GROUP BY
				poliza.rowid,
				b.fk_proveedor
			';

		if ($format==1) {
			$sql.='
				ORDER BY
				poliza.fechahora ASC,
				poliza.societe_type ASC,
				poliza.cons ASC,
				poliza.tipo_pol DESC';
		}else{
			$sql.='
				ORDER BY
					a.nom ASC';
		}


        $query = $db->query($sql);
        if ($query) {

            $rows = array();
            while ($row = $db->fetch_object($query)) {

	            $rows[] = $row;

            }
        }

        return $rows;
    }

    function get_info_docto_xml($idpol) {
        
        global $db;

        $rows = array();
        $sql="SELECT * FROM llx_contab_polizas as a WHERE a.rowid=".$idpol;


        $query=$db->query($sql);
        $obj=$db->fetch_object($query);



        $an=substr($obj->anio, -2, 2); 			
		$m = ((int)$obj->mes<10) ? "0".$obj->mes : $obj->mes ;
		$folio = $an.$m."-".$obj->tipo_pol."-".$obj->cons; 

        $string= "SELECT rowid, url FROM ".MAIN_DB_PREFIX."contab_doc WHERE folio='".$folio."'";


		$que=$db->query($string);
        
        if ($que) {
            while ($row = $db->fetch_object($que)) {
                $rows[] = $row;
            }
        }


        return $rows;
    }




	//$polizas       =new Poliza();
	//$periodo       =new Periodo();
	//$facture_fourn =new FacPolizaPendiente();
	$fecha         =get_ultimo_periodo_abierto();



	$anio          =0;
	$mes           =0;
	$j             =0;
	$i             =0;
	$xml_array     =array(); 
	$fk_proveedor=GETPOST("proveedor");
	$diot=GETPOST("diot","int");
	$format=GETPOST("format");
	$iva=0.16;
	$iva_cal=0.16;
	if ($format==2) {
		require_once  "../class/PHPExcel.php";
	}


	if (isset($_GET['anio']) && isset($_GET['mes'])) {
		$anio = (int)$_GET['anio'];
		$mes  = (int)$_GET['mes'];
	}
	if($anio <=0 || $mes <= 0 ){
		$anio  = $fecha->anio;
    	$mes   = $fecha->mes;
	}

	$polizas_diot= getPolizas_diot($anio, $mes,$fk_proveedor,$format,$diot);


	if ($format==1) {
		header('Content-type: text/plain');
	    header('Content-Disposition: attachment; filename=DIOT_POLIZAS_'.$anio.'-'.$mes.'.txt');
	}else{
		$objPHPExcel = new PHPExcel();
	    $objPHPExcel->getProperties()->setCreator("Dolibarr")
					->setTitle('DIOT_POLIZAS_'.$anio.'-'.$mes)
					->setSubject("Office 2007 XLSX")
					->setDescription("Pólizas XLSX, generado usando clases PHP.")
					->setKeywords("office 2007 openxml php")
					->setCategory("Contabilidad");
	}

    //header('Content-type: application/ms-excel');
	//header('Content-Disposition: attachment; filename=DIOT_POLIZAS_'.$anio.'-'.$mes.'.xls');


	foreach ($polizas_diot as $poli_key => $poli_value) {

		if ($diot==2) {

			$sql="
			SELECT
				asiento.asiento,
				asiento.cuenta,
				b.descta as des,
				asiento.debe,
				asiento.haber,
				asiento.rowid,
				asiento.iva,
				asiento.descripcion as de,
				c.porcentaje
			FROM
				llx_contab_polizasdet AS asiento
			LEFT JOIN llx_contab_cat_ctas as b on b.cta=asiento.cuenta
			LEFT JOIN llx_contab_cat_iva as c on c.id_cuenta=b.rowid
			WHERE
				asiento.fk_poliza=".$poli_value->id.' AND asiento.fk_proveedor = '.$poli_value->fk_proveedor;
			

			$res2=$db->query($sql);

			$res=$db->query($sql);

			if ($db->num_rows($res)>0) {
				$debe=0;
				$haber=0;
				$iva_debe=0;
				$iva_haber=0;
				while ($obj=$db->fetch_object($res)) {
					if ($obj->porcentaje>0) {
							$debe+=round($obj->debe/ ($obj->porcentaje/100) ,4);
							$haber+=round($obj->haber/ ($obj->porcentaje/100) ,4);
							$iva_debe+=round(($obj->debe/ ($obj->porcentaje/100)  )  *($obj->porcentaje/100 ) ,4);
							$iva_haber+=round(($obj->haber/ ($obj->porcentaje/100) ) *($obj->porcentaje/100) ,4);
					}else{
							$debe+=round($obj->debe/$iva,4);
							$haber+=round($obj->haber/$iva,4);

							$iva_debe+=round(($obj->debe/$iva)*$iva,4);
							$iva_haber+=round(($obj->haber/$iva)*$iva,4);
					}
				}

	
				//echo "<br><br>".round($iva_debe);
				$sql="SELECT * FROM llx_contab_societe as a WHERE a.rowid=".$poli_value->fk_proveedor;

				$res=$db->query($sql);
				$id_fiscal="";
				$nombre_extranjero="";
				$rfc="";


				$code_tip_prov="";
				$code_tip_op="";
				if ($res) {
					$ob=$db->fetch_object($res);
					$id_fiscal=$ob->id_fiscal;
					$nombre_extranjero=$ob->nom;
					$rfc=$ob->rfc;
					$tip_prov=$ob->tip_prov;
					
					if ($tip_prov==1) {
						$code_tip_prov="04";
					}
					if ($tip_prov==2) {
						$code_tip_prov="05";
					}
					if ($tip_prov==3) {
						$code_tip_prov="15";
					}


					$tip_op=$ob->tip_op;
					
					if ($tip_op==1) {
						$code_tip_op="03";
					}
					if ($tip_op==2) {
						$code_tip_op="06";
					}
					if ($tip_op==3) {
						$code_tip_op="85";
					}
				}


				$xml_array[$i]['rfc']               =$rfc;
				$xml_array[$i]['key_to']            =$code_tip_prov;
				$xml_array[$i]['key_ts']            =$code_tip_op;
				$xml_array[$i]['id_fiscal']         ="";
				$xml_array[$i]['nombre_extranjero'] ="";
				$xml_array[$i]['nombre_extranjero2'] =$nombre_extranjero;
				$xml_array[$i]['prov']     			 =$poli_value->fk_proveedor;

				if ($haber-$debe==0) {
					$xml_array[$i]['compIppSubTot']     =round(floatval($haber),0);

				}else{
					if ($haber==0) {

						$xml_array[$i]['compIppSubTot']     =round(floatval(($debe) ),0);
					}else{
						
						$xml_array[$i]['compIppSubTot']     =round(floatval(($haber-$debe) ),0);
					}
				}

				if ($iva_haber-$iva_debe==0) {
					$xml_array[$i]['iva']     =round(floatval($iva_haber),0);

				}else{
					if ($iva_haber==0) {
						$xml_array[$i]['iva']     =round(floatval(($iva_debe) ),0);
					}else{
						
						$xml_array[$i]['iva']     =round(floatval(($iva_haber-$iva_debe) ),0);
					}
				}

				$xml_array[$i]['compIppTot']        = 0;
				$i++;
				
			}
		}elseif ($diot==1) {
			if( ($xml_rows = get_info_docto_xml((int)$poli_value->id)) && sizeof($xml_rows)>0 ){
			
				foreach ($xml_rows as $xml_key => $xml_value) {
					$url_xml = $xml_value->url;
					if (file_exists($url_xml) ) {

						get_data_xml($url_xml);
						$sql="SELECT * FROM llx_contab_societe as a WHERE a.rfc='".$emisorRfc."' ";

						$res=$db->query($sql);
						$id_fiscal="";
						$nombre_extranjero="";
						if ($res) {
							$ob=$db->fetch_object($res);
							$id_fiscal=$ob->id_fiscal;
							$nombre_extranjero=$ob->nom;
							$rfc=$ob->rfc;
							$tip_prov=$ob->tip_prov;

							if ($tip_prov==1) {
								$code_tip_prov="04";
							}
							if ($tip_prov==2) {
								$code_tip_prov="05";
							}
							if ($tip_prov==3) {
								$code_tip_prov="15";
							}


							$tip_op=$ob->tip_op;
							
							if ($tip_op==1) {
								$code_tip_op="03";
							}
							if ($tip_op==2) {
								$code_tip_op="06";
							}
							if ($tip_op==3) {
								$code_tip_op="85";
							}
						}


						
						
						$xml_array[$i]['rfc']               =$emisorRfc;
						$xml_array[$i]['key_to']            =$code_tip_prov;
						$xml_array[$i]['key_ts']            =$code_tip_op;
						$xml_array[$i]['id_fiscal']         =$id_fiscal;
						$xml_array[$i]['nombre_extranjero2'] =$nombre_extranjero;
						$xml_array[$i]['compIppSubTot']     =round(floatval($compIppSubTot),0);
						$xml_array[$i]['compIppTot']        = 0;
						$xml_array[$i]['iva']     			=round(floatval($arrTras[$xml_key]["importe"]),0);
						$xml_array[$i]['prov']     			=$poli_value->fk_proveedor;

						$i++;
					}

				}
			
			}
		}elseif ($diot==3) {
			$sql="
			SELECT
				asiento.asiento,
				asiento.cuenta,
				b.descta as des,
				asiento.debe,
				asiento.haber,
				asiento.rowid,
				asiento.iva,
				asiento.descripcion as de,
				c.porcentaje
			FROM
				llx_contab_polizasdet AS asiento
			LEFT JOIN llx_contab_cat_ctas as b on b.cta=asiento.cuenta
			LEFT JOIN llx_contab_cat_iva as c on c.id_cuenta=b.rowid
			WHERE
				asiento.fk_poliza=".$poli_value->id;
			if ($poli_value->fk_proveedor>0) {
				$sql.=' AND asiento.fk_proveedor = '.$poli_value->fk_proveedor;

				$res=$db->query($sql);

				if ($db->num_rows($res)>0) {
					$debe=0;
					$haber=0;
					$iva_debe=0;
					$iva_haber=0;
					while ($obj=$db->fetch_object($res)) {
						if ($obj->porcentaje>0) {
								$debe+=round($obj->debe/ ($obj->porcentaje/100) ,4);
								$haber+=round($obj->haber/ ($obj->porcentaje/100) ,4);
								$iva_debe+=round(($obj->debe/ ($obj->porcentaje/100)  )  *($obj->porcentaje/100 ) ,4);
								$iva_haber+=round(($obj->haber/ ($obj->porcentaje/100) ) *($obj->porcentaje/100) ,4);
						}else{
								$debe+=round($obj->debe/$iva,4);
								$haber+=round($obj->haber/$iva,4);

								$iva_debe+=round(($obj->debe/$iva)*$iva,4);
								$iva_haber+=round(($obj->haber/$iva)*$iva,4);
						}
					}

		
					//echo "<br><br>".round($iva_debe);
					$sql="SELECT * FROM llx_contab_societe as a WHERE a.rowid=".$poli_value->fk_proveedor;

					$res=$db->query($sql);
					$id_fiscal="";
					$nombre_extranjero="";
					$rfc="";


					$code_tip_prov="";
					$code_tip_op="";
					if ($res) {
						$ob=$db->fetch_object($res);
						$id_fiscal=$ob->id_fiscal;
						$nombre_extranjero=$ob->nom;
						$rfc=$ob->rfc;
						$tip_prov=$ob->tip_prov;
						
						if ($tip_prov==1) {
							$code_tip_prov="04";
						}
						if ($tip_prov==2) {
							$code_tip_prov="05";
						}
						if ($tip_prov==3) {
							$code_tip_prov="15";
						}


						$tip_op=$ob->tip_op;
						
						if ($tip_op==1) {
							$code_tip_op="03";
						}
						if ($tip_op==2) {
							$code_tip_op="06";
						}
						if ($tip_op==3) {
							$code_tip_op="85";
						}
					}


					$xml_array[$i]['rfc']               =$rfc;
					$xml_array[$i]['key_to']            =$code_tip_prov;
					$xml_array[$i]['key_ts']            =$code_tip_op;
					$xml_array[$i]['id_fiscal']         ="";
					$xml_array[$i]['nombre_extranjero'] ="";
					$xml_array[$i]['nombre_extranjero2'] =$nombre_extranjero;
					$xml_array[$i]['prov']     			=$poli_value->fk_proveedor;

					if ($haber-$debe==0) {
						$xml_array[$i]['compIppSubTot']     =round(floatval($haber),0);

					}else{
						if ($haber==0) {

							$xml_array[$i]['compIppSubTot']     =round(floatval(($debe) ),0);
						}else{
							
							$xml_array[$i]['compIppSubTot']     =round(floatval(($haber-$debe) ),0);
						}
					}

					if ($iva_haber-$iva_debe==0) {
						$xml_array[$i]['iva']     =round(floatval($iva_haber),0);

					}else{
						if ($iva_haber==0) {
							$xml_array[$i]['iva']     =round(floatval(($iva_debe) ),0);
						}else{
							
							$xml_array[$i]['iva']     =round(floatval(($iva_haber-$iva_debe) ),0);
						}
					}

					$xml_array[$i]['compIppTot']        = 0;
					$i++;
					
				}
			}else{
				if( ($xml_rows = get_info_docto_xml((int)$poli_value->id)) && sizeof($xml_rows)>0 ){
			
					foreach ($xml_rows as $xml_key => $xml_value) {
						$url_xml = $xml_value->url;
						if (file_exists($url_xml) ) {

							get_data_xml($url_xml);
							$sql="SELECT * FROM llx_contab_societe as a WHERE a.rfc='".$emisorRfc."' ";

							$res=$db->query($sql);
							$id_fiscal="";
							$nombre_extranjero="";
							if ($res) {
								$ob=$db->fetch_object($res);
								$id_fiscal=$ob->id_fiscal;
								$nombre_extranjero=$ob->nom;
								$rfc=$ob->rfc;
								$tip_prov=$ob->tip_prov;

								if ($tip_prov==1) {
									$code_tip_prov="04";
								}
								if ($tip_prov==2) {
									$code_tip_prov="05";
								}
								if ($tip_prov==3) {
									$code_tip_prov="15";
								}


								$tip_op=$ob->tip_op;
								
								if ($tip_op==1) {
									$code_tip_op="03";
								}
								if ($tip_op==2) {
									$code_tip_op="06";
								}
								if ($tip_op==3) {
									$code_tip_op="85";
								}
							}

							
							$xml_array[$i]['rfc']               =(string)$emisorRfc[0];
							$xml_array[$i]['key_to']            =$code_tip_prov;
							$xml_array[$i]['key_ts']            =$code_tip_op;
							$xml_array[$i]['id_fiscal']         =$id_fiscal;
							$xml_array[$i]['nombre_extranjero2'] =$nombre_extranjero;
							$xml_array[$i]['compIppSubTot']     =round(floatval($compIppSubTot),0);
							$xml_array[$i]['compIppTot']        = $arrTras[$xml_key]['tasa'];
							$xml_array[$i]['iva']     			=round(floatval($arrTras[$xml_key]["importe"]),0);
							$xml_array[$i]['prov']     			=$poli_value->fk_proveedor;


							$i++;
						}

					}
				
				}
			}

			
		}

		
	}


	
	$aux=array();
  	if ($format==1) {
  		$xml_array=burbuja($xml_array,sizeof($xml_array));
  	}
	
	
	if ($xml_array && $format==2) {
		$meses = array('ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO',
               'AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE');
		$objPHPExcel->getActiveSheet()->mergeCells('A1:D1');
		$objPHPExcel->getActiveSheet()->mergeCells('A2:D2');
		$objPHPExcel->setActiveSheetIndex(0)
	                ->setCellValue('A1', $conf->global->MAIN_INFO_SOCIETE_NOM);
	    $objPHPExcel->setActiveSheetIndex(0)
	                ->setCellValue('A2', "REPORTE DIOT DEL MES DE ".$meses[$mes-1]." ".$anio );
		
	    $objPHPExcel->getActiveSheet()
	    ->getStyle('C3:F3')
	    ->getAlignment()
	    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);


		$objPHPExcel->setActiveSheetIndex(0)
	                ->setCellValue('A3', "")
	                ->setCellValue('B3', "")
	                ->setCellValue('C3', "RFC")
	                ->setCellValue('D3', "PROVEEDOR")
	                ->setCellValue('E3', "IMPORTE" )
	                ->setCellValue('F3', "IVA");  
	}


	for ($i=0;$i<count($xml_array);$i++)
	{
	    for ($j=$i+1; $j<count($xml_array);$j++)
	    {
		    if ($xml_array[$i]['rfc'] == $xml_array[$j]['rfc'] && empty($xml_array[$i]['prov']))
		    {
		    	
		        $xml_array[$j]['compIppSubTot']= $xml_array[$i]['compIppSubTot']+$xml_array[$j]['compIppSubTot'];
		        $xml_array[$j]['iva']= $xml_array[$i]['iva']+$xml_array[$j]['iva'];
		        unset($xml_array[$i]);
		    }
		}
	}
	


	for ($i=0; $i <= sizeof($xml_array) ; $i++) {
		
		if (!empty($xml_array[$i])) {
			if($i==0)
			{
				$aux[$j] = $xml_array[$i];
			}else if( $aux[$j]['rfc'] ==  $xml_array[$i]['rfc']  && ( $aux[$j]['key_to'] == $xml_array[$i]['key_to'] && $aux[$j]['key_ts'] == $xml_array[$i]['key_ts'])  ){

				$aux[$j]['compIppSubTot'] += $xml_array[$i]['compIppSubTot'];
				$aux[$j]['compIppTot']    += $xml_array[$i]['compIppTot'];
				$aux[$j]['iva']    += $xml_array[$i]['iva'];
			}else{
				$j++;
				$aux[$j] = $xml_array[$i];
			}
		}
	}




	

	$xml_array =$aux;
    //$file = fopen('DIOT_POLIZAS_'.$anio.'-'.$mes.'.txt', "a");
    $i=4;
    $tot_imp=0;
    $tot_iva=0;
	foreach ($xml_array as $xml_key => $xml_value) {
		/*echo<<<EOT
		{$xml_value['key_to']}|{$xml_value['key_ts']}|{$xml_value['rfc']}|||||{$xml_value['compIppSubTot']}|||||||||||||||
		\n\r
		EOT;*/
		$xml_value['compIppSubTot'] = $xml_value['compIppSubTot'] == 0 ?  '':$xml_value['compIppSubTot'];
		$xml_value['compIppTot']    = $xml_value['compIppTot'] == 0 ?	  '':$xml_value['compIppTot'];

		

		if ($format==2) {
			$ban=1;
			$objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$i, $xml_value['key_to'])
                    ->setCellValue('B'.$i, $xml_value['key_ts'])
                    ->setCellValue('C'.$i, $xml_value['rfc'])
                    ->setCellValue('D'.$i, $xml_value['nombre_extranjero2'])
                    ->setCellValue('E'.$i,  round(floatval($xml_value['compIppSubTot']),0) )
                    ->setCellValue('F'.$i,  round(floatval($xml_value['iva']),2)   );
            //echo $xml_value['compIppSubTot']."<br>";
            $tot_imp+=$xml_value['compIppSubTot'];
            $tot_iva+=$xml_value['iva'];




		}else{
			echo $xml_value['key_to'].'|'.$xml_value['key_ts'].'|'.$xml_value['rfc'].'|'.$xml_value['id_fiscal'].'|'.$xml_value['nombre_extranjero'].'|||'.round(floatval($xml_value['compIppSubTot']),0).'||0||||||||||'.$xml_value['compIppTot'].'|||';
			echo PHP_EOL;
		}
		$i++;
		
	}


	if ($format==2) {
		if ($ban==1) {
			$objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('C'.$i, "TOTALES")
                    ->setCellValue('D'.$i, "")
                    ->setCellValue('E'.$i,  round(floatval($tot_imp),0) )
                    ->setCellValue('F'.$i,  round(floatval($tot_iva),2) );  
		}
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
	    $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
		$objPHPExcel->setActiveSheetIndex(0);

	    header('Content-Type: application/vnd.ms-excel');
	    header('Content-Disposition: attachment;filename="'.'DIOT_POLIZAS_'.$anio.'-'.$mes. ".xls".'"');
	    header('Cache-Control: max-age=0');

	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
	    $objWriter->save('php://output');
	    exit;
	}
	//fclose($file);

	function burbuja($A,$n)
    {
        for($i=1;$i<$n;$i++)
        {
                for($j=0;$j<$n-$i;$j++)
                {
                    if($A[$j]>$A[$j+1])
                        {$k=$A[$j+1]; $A[$j+1]=$A[$j]; $A[$j]=$k;}
                }
        }

      return $A;
    }
 ?>