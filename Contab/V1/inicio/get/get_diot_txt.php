<?php 
	

	$url[0] = '../';
	$url[4] = '../polizas/';

	require_once '../class/poliza.class.php';
	require_once '../class/periodo.class.php';
	require_once '../class/fact_prov_pendientes.class.php';
	require '../get/get_data_xml.php';

	$polizas       =new Poliza();
	$periodo       =new Periodo();
	$facture_fourn =new FacPolizaPendiente();
	$fecha         =$periodo->get_ultimo_periodo_abierto();
	$anio          =0;
	$mes           =0;
	$j             =0;
	$i             =0;
	$xml_array     =array(); 

	if (isset($_GET['anio']) && isset($_GET['mes'])) {
		$anio = (int)$_GET['anio'];
		$mes  = (int)$_GET['mes'];
	}
	if($anio <=0 || $mes <= 0 ){
		$anio  = $fecha->anio;
    	$mes   = $fecha->mes;
	}
	header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename=DIOT_POLIZAS_'.$anio.'-'.$mes.'.txt');
	$polizas_diot= $polizas->getPolizas_diot($anio, $mes);

	foreach ($polizas_diot as $poli_key => $poli_value) {
		if( ($xml_rows = $polizas->get_info_docto_xml((int)$poli_value['id'])) && sizeof($xml_rows)>0 ){
			foreach ($xml_rows as $xml_key => $xml_value) {
					
					$url_xml = $url[4].'archivos/'.ENTITY.'/POL'.$poli_value['id'].'-'.$poli_value['tipol_l'].$poli_value['npol'].'/'.$xml_value->archivo;

					if(empty($xml_value->archivo) && ($fac_rows = $polizas->get_info_factures_xml_without_operation_societe_fourn((int)$poli_value['id'])) && sizeof($fac_rows)>0 ){
					
						foreach ($fac_rows as $fac_key => $fac_value) {
							$xml_array[$i]['rfc']               =(string)$xml_value->rfc;
							$xml_array[$i]['key_to']            =(string)$xml_value->key_to;
							$xml_array[$i]['key_ts']            =(string)$xml_value->key_ts;
							
							$xml_array[$i]['id_fiscal']         =(string)$xml_value->id_fiscal;
							$xml_array[$i]['nombre_extranjero'] =(string)$xml_value->nombre_extranjero;
							$xml_array[$i]['compIppSubTot']     =0;
							$xml_array[$i]['compIppTot']        =round(floatval($fac_value->total),0);
							$i++;
						
						}
					}
					else if (file_exists($url_xml) ) {
			
						get_data_xml($url_xml);
						$xml_array[$i]['rfc']               =(string)$emisorRfc;
						$xml_array[$i]['key_to']            =(string)$xml_value->key_to;
						$xml_array[$i]['key_ts']            =(string)$xml_value->key_ts;
						$xml_array[$i]['id_fiscal']         ='';
						$xml_array[$i]['nombre_extranjero'] ='';
						$xml_array[$i]['compIppSubTot']     =round(floatval($compIppSubTot),0);
						$xml_array[$i]['compIppTot']        = 0;
						
						$i++;
					}

			}
		
		}else if( ($fac_rows = $polizas->get_info_factures_xml((int)$poli_value['id'])) && sizeof($fac_rows)>0 ){
			foreach ($fac_rows as $fac_key => $fac_value) {
				$xml_array[$i]['rfc']               =(string)$fac_value->rfc;
				$xml_array[$i]['key_to']            =(string)$fac_value->key_to;
				$xml_array[$i]['key_ts']            =(string)$fac_value->key_ts;
				$xml_array[$i]['id_fiscal']         ='';
				$xml_array[$i]['nombre_extranjero'] ='';
				$xml_array[$i]['compIppSubTot']     =round(floatval($fac_value->subtotal),0);
				$xml_array[$i]['compIppTot']        = 0;

				$i++;
			}
		}
	}

	$aux=array();
  
	$xml_array=burbuja($xml_array,sizeof($xml_array));
	
	for ($i=0; $i < sizeof($xml_array) ; $i++) { 
		if($i==0)
		{
			$aux[$j] = $xml_array[$i];
		}else if( $aux[$j]['rfc'] ==  $xml_array[$i]['rfc']  && ( $aux[$j]['key_to'] == $xml_array[$i]['key_to'] && $aux[$j]['key_ts'] == $xml_array[$i]['key_ts'])  ){
			$aux[$j]['compIppSubTot'] += $xml_array[$i]['compIppSubTot'];
			$aux[$j]['compIppTot']    += $xml_array[$i]['compIppTot'];
		}else{
			$j++;
			$aux[$j] = $xml_array[$i];
		}
	}
	$xml_array =$aux;


	foreach ($xml_array as $xml_key => $xml_value) {
		/*echo<<<EOT
		{$xml_value['key_to']}|{$xml_value['key_ts']}|{$xml_value['rfc']}|||||{$xml_value['compIppSubTot']}|||||||||||||||
		\n\r
		EOT;*/
		$xml_value['compIppSubTot'] = $xml_value['compIppSubTot'] == 0 ?  '':$xml_value['compIppSubTot'];
		$xml_value['compIppTot']    = $xml_value['compIppTot'] == 0 ?	  '':$xml_value['compIppTot'];
		echo $xml_value['key_to'].'|'.$xml_value['key_ts'].'|'.$xml_value['rfc'].'|'.$xml_value['id_fiscal'].'|'.$xml_value['nombre_extranjero'].'|||'.$xml_value['compIppSubTot'].'||||||||||||'.$xml_value['compIppTot'].'|||';
			if ($xml_key<(sizeof($xml_array)-1)) 
				echo PHP_EOL;
	}

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