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


	function getPolizas_diot($anio, $mes,$id_prov="") {
        $rows = array();
        global $db,$conf;
        $periodomes = 'poliza.mes';
        if ($mes == 13) {
            $mes = 12;
            $ajuste = 1;
            $periodomes = 13;
        }
        $sql = 'SELECT
				poliza.rowid AS "id",
				poliza.entity,
				(
					CASE poliza.tipo_pol
					WHEN "D" THEN
						"Diario"
					WHEN "E" THEN
						"Egreso"
					WHEN "C" THEN
						"Cheque"
					END
				) AS tipo_pol,
				poliza.tipo_pol AS tipol_l,
				poliza.cons npol,
				CONCAT(
					DATE_FORMAT(poliza.fecha, "%y%c"),
					"-",
					poliza.tipo_pol,
					"-",
					poliza.cons
				) AS cons,
				poliza.cons npol,
				poliza.anio,
				poliza.mes,
				poliza.fecha,
				poliza.concepto,
				poliza.comentario,
				poliza.anombrede,
				poliza.numcheque,
				poliza.societe_type,
				poliza.fk_proveedor,

			IF (p.estado IS NULL, 0, p.estado) AS estado
			FROM
				llx_contab_polizas AS poliza
			LEFT JOIN llx_contab_periodos AS p ON p.anio = poliza.anio
			AND p.mes = ' . $periodomes . '
			WHERE
				poliza.tipo_pol = "E"
			AND poliza.fecha != "0000-00-00"
			AND poliza.entity = ' . $conf->entity . '
			AND poliza.anio = "' . $anio . '"
			AND poliza.mes = "' . $mes . '"

			';
			if (!empty($id_prov)) {
				$sql.='AND poliza.fk_proveedor = '.$id_prov;
			}
			
		$sql.='

			GROUP BY
				poliza.rowid
			ORDER BY
				poliza.fechahora ASC,
				poliza.societe_type ASC,
				poliza.cons ASC,
				poliza.tipo_pol DESC';
	
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
	$polizas_diot= getPolizas_diot($anio, $mes,$fk_proveedor);



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
				asiento.descripcion as de
			FROM
				llx_contab_polizasdet AS asiento
			LEFT JOIN llx_contab_cat_ctas as b on b.cta=asiento.cuenta

			WHERE
				asiento.fk_poliza=".$poli_value->id;
			$res=$db->query($sql);

			if ($db->num_rows($res)>0) {
					$debe=0;
					$haber=0;

					while ($obj=$db->fetch_object()) {
						$debe+=$obj->debe;
						$haber+=$obj->haber;
					}

					$sql="SELECT * FROM llx_contab_societe as a WHERE a.rowid=".$poli_value->fk_proveedor;

					$res=$db->query($sql);
					$id_fiscal="";
					$nombre_extranjero="";
					$rfc="";
					if ($res) {
						$ob=$db->fetch_object($res);
						$id_fiscal=$ob->id_fiscal;
						$nombre_extranjero=$ob->nom;
						$rfc=$ob->rfc;
					}


					$xml_array[$i]['rfc']               =$rfc;
					$xml_array[$i]['key_to']            ="";
					$xml_array[$i]['key_ts']            ="";
					$xml_array[$i]['id_fiscal']         =$id_fiscal;
					$xml_array[$i]['nombre_extranjero'] =$nombre_extranjero;
					if ($haber-$debe!=0) {
						$xml_array[$i]['compIppSubTot']     =round(floatval($haber-$debe),0);
					}else{
						$xml_array[$i]['compIppSubTot']     =round(floatval($haber),0);
					}
					
					$xml_array[$i]['compIppTot']        = 0;
					$i++;
				}
		}

		if( ($xml_rows = get_info_docto_xml((int)$poli_value->id)) && sizeof($xml_rows)>0 && $diot==1 ){
			
			foreach ($xml_rows as $xml_key => $xml_value) {

				$url_xml = $xml_value->url;


				if (file_exists($url_xml) ) {

					$sql="SELECT * FROM llx_contab_societe as a WHERE a.rowid=".$poli_value->fk_proveedor;

					$res=$db->query($sql);
					$id_fiscal="";
					$nombre_extranjero="";
					if ($res) {
						$ob=$db->fetch_object($res);
						$id_fiscal=$ob->id_fiscal;
						$nombre_extranjero=$ob->nom;
					}



					get_data_xml($url_xml);
					$xml_array[$i]['rfc']               =(string)$emisorRfc;
					$xml_array[$i]['key_to']            =(string)$xml_value->key_to;
					$xml_array[$i]['key_ts']            =(string)$xml_value->key_ts;
					$xml_array[$i]['id_fiscal']         =$id_fiscal;
					$xml_array[$i]['nombre_extranjero'] =$nombre_extranjero;
					$xml_array[$i]['compIppSubTot']     =round(floatval($compIppSubTot),0);
					$xml_array[$i]['compIppTot']        = 0;
				
					$i++;
				}

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


