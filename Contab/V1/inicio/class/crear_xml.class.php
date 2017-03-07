<?php
$url[0] = "../";
require_once $url[0]."conex/conexion.php";
class CrearXML extends conexion {   
	var $anio;
	var $mes;
	var $rfc;
	var $xml_version = '1.1';
	var $xmlstr;
	
	var $tipo_envio;
	
	var $tipo_solicitud;
	var $num_orden;
	
	
	var $errors;
	var $mesg;
	var $cta_err;
	
	var $file_path;
	
	function __construct($db)
	{
		parent::__construct(); 
	}
	
	function Crea_Catalogo() {
		$dir = "../periodos/temp/";
		$handle = opendir($dir);
        $ficherosEliminados = 0;
        while ($file = readdir($handle)) {
            if (is_file($dir.$file)) {
                if (unlink($dir.$file) ){
                    $ficherosEliminados++;
                }
            }
        }
		$xmlCatalogo = new SimpleXMLElement($this->xmlstr);
		$xmlCatalogo->addAttribute("Version", $this->xml_version);
		$xmlCatalogo->addAttribute("RFC", $this->rfc);
		$xmlCatalogo->addAttribute("Mes", sprintf("%02d", $this->mes));
		$xmlCatalogo->addAttribute("Anio", $this->anio);
		
		$sql = "Select codagr,descripcion,natur,codsat,nivel From ".PREFIX."contab_cat_ctas ";
    	$sql .= "WHERE 1";
    	//$sql .= " AND entity = ".ENTITY;
    	$sql .= " ORDER BY codagr ";
//    	print $sql."<br>";exit();
    	$resql=$this->db->query($sql);
//     	print "<pre>";
//     	print_r($resql);
//     	print "</pre>";exit();
    	//$ar=array();
    	//$n=0;
		while($rs=$resql->fetch_object()){
			$sqm="SELECT count(a.asiento) as con
					FROM ".PREFIX."contab_polizasdet a, ".PREFIX."contab_polizas b
					WHERE a.cuenta='".$rs->codagr."' AND fk_poliza=b.rowid 
					AND anio=".$this->anio." AND mes=".$this->mes;
			//$sqm.=" AND b.entity=".ENTITY;
			//print $sqm."<br>";
			$ts=$this->db->query($sqm);
			$tsl=$ts->fetch_object();
			if($tsl->con>0){
			$desc= str_replace('"','',  ($rs->descripcion));
			$String=$desc;
			$String = str_replace(array('á','à','â','ã','ª','ä'),"a",$String);
			$String = str_replace(array('Á','À','Â','Ã','Ä'),"A",$String);
			$String = str_replace(array('Í','Ì','Î','Ï'),"I",$String);
			$String = str_replace(array('í','ì','î','ï'),"i",$String);
			$String = str_replace(array('é','è','ê','ë'),"e",$String);
			$String = str_replace(array('É','È','Ê','Ë'),"E",$String);
			$String = str_replace(array('ó','ò','ô','õ','ö','º'),"o",$String);
			$String = str_replace(array('Ó','Ò','Ô','Õ','Ö'),"O",$String);
			$String = str_replace(array('ú','ù','û','ü'),"u",$String);
			$String = str_replace(array('Ú','Ù','Û','Ü'),"U",$String);
			$String = str_replace("ç","c",$String);
			$String = str_replace("Ç","C",$String);
			$String = str_replace("ñ","n",$String);
			$String = str_replace("Ñ","N",$String);
			$String = str_replace("Ý","Y",$String);
			$String = str_replace("ý","y",$String);
			//$String = str_replace("%","",$String);
			$desc=$String;
			$cod = $rs->codagr;
			$xmlcta =$xmlCatalogo->addChild("Ctas");
			$xmlcta->addAttribute("CodAgrup", $cod);
			$xmlcta->addAttribute("NumCta", $cod);
			$xmlcta->addAttribute("Desc", trim($desc));
			//$xmlcta->addAttribute("SubCtaDe", $rs->cta_subctade);
			$xmlcta->addAttribute("Nivel", $rs->nivel);
			$xmlcta->addAttribute("Natur", $rs->natur);
			//print_r($xmlcta);
			//print $rs->codagr." :: ".$desc." :: ".$rs->nivel." :: ".$rs->natur."<--<br><br>";
			//$n++;
			/* if($n>28){
			break;
			} */
			}
		} 
		
		//print_r($xmlCatalogo);
		//exit();
		//print_r($cta_err);
		//dol_syslog("Termino el proceso :: ".$this->errors);
		//$xmlCatalogo->addAttribute("TotalCtas", sizeof($arr));
		
		$xmlCatalogo->saveXML("../periodos/temp/".$this->rfc.$this->anio.sprintf("%02d", $this->mes)."ct".".xml");
		
		if (!$this->errors) {
			$enlace = "../periodos/temp/".$this->rfc.$this->anio.sprintf("%02d", $this->mes)."ct".".xml";
			$enlace2 = "".$this->rfc.$this->anio.sprintf("%02d", $this->mes)."ct".".xml";
			header ("Content-Disposition: attachment; filename=$enlace2");
			header ("Content-Type: application/force-download");
			header ("Content-Length: ".filesize($enlace));
			readfile($enlace);
		}else{
			print "Error al crear archivo";
		}
		 
		return 1;
	}
	
	function Crea_Balanza() {
		$dir = "../periodos/temp/";
		$handle = opendir($dir);
        $ficherosEliminados = 0;
        while ($file = readdir($handle)) {
            if (is_file($dir.$file)) {
                if (unlink($dir.$file) ){
                    $ficherosEliminados++;
                }
            }
        }
		$xmlBal = new SimpleXMLElement($this->xmlstr);
		$xmlBal->addAttribute("Version", $this->xml_version);
		$xmlBal->addAttribute("RFC", $this->rfc);
		//$xmlBal->addAttribute("TotalCtas", $tot_ctas);
		$xmlBal->addAttribute("Mes", sprintf("%02d", $this->mes));
		$xmlBal->addAttribute("Anio", $this->anio);
		$xmlBal->addattribute("TipoEnvio", $this->tipo_envio);
		
		//$elem = $xmlCatalogo->Catalogo;
		
		if ($this->tipo_envio == "C") {
			/* $pol = new Contabpolizas($this->db);
			$pol->get_ult_fecha_modif_contable($this->anio, $this->mes); */
			$sql = "SELECT MAX(t.fecha) as ult_fecha ";
			$sql.= " FROM ".PREFIX."contab_polizas as t";
			$sql.= " WHERE t.anio = $this->anio AND t.mes = $this->mes ";
			$sql.= " AND entity = ".ENTITY;
			//print $sql."<br>";
			$rqs=$this->db->query($sql);
			$rsl=$rqs->fetch_object();
			$xmlBal->addAttribute("FechaModBal", $rsl->ult_fecha);
		}
		//print_r($xmlBal);exit();
		$dia=cal_days_in_month(CAL_GREGORIAN, $this->mes, $this->anio);
		$periodo[0]=$this->anio."/".$this->mes."/1";
		$periodo[1]=$this->anio."/".$this->mes."/".$dia;
		$sql = "SELECT a.codagr as cta,a.descripcion as descta,sum(debe) as debe, sum(haber) as haber,a.natur 
				FROM ".PREFIX."contab_cat_ctas a, ".PREFIX."contab_polizasdet b, ".PREFIX."contab_polizas c
				WHERE a.codagr=b.cuenta AND b.fk_poliza=c.rowid AND c.fecha BETWEEN '".$periodo[0]."' AND '".$periodo[1]."'  
				AND c.entity=".ENTITY." GROUP BY a.codagr";
		//print $sql;exit();
		$rqs=$this->db->query($sql);
		while ($rsl=$rqs->fetch_object()) {
			$cta=$rsl->cta;
			$sql2 = "SELECT a.codagr as cta,a.descripcion as descta,sum(debe) as debe, sum(haber) as haber,a.natur
				FROM ".PREFIX."contab_cat_ctas a, ".PREFIX."contab_polizasdet b, ".PREFIX."contab_polizas c
				WHERE a.codagr=b.cuenta AND b.fk_poliza=c.rowid AND c.fecha <'".$periodo[0]."'
				AND a.codagr='".$cta."' AND c.entity=".ENTITY." ";
            $sql2.=" GROUP BY a.codagr";
			$rqs2=$this->db->query($sql2);
			$ini=$rqs2->fetch_object();
			$idebe=$ini->debe;
			$ihaber=$ini->haber;
			$debe=$rsl->debe;
			$haber=$rsl->haber;
			if($rsl->natur=='D'){
				$sdoini=$idebe-$ihaber;
				$sdofin=$sdoini+$debe-$haber;
			}else{
				$sdoini=$ihaber-$idebe;
				$sdofin=$sdoini+$haber-$debe;
			}
			
			if (abs($sdoini) > 0 || abs($debe) > 0 || abs($haber) > 0 || abs($sdofin) > 0) {
				$xmlcta = $xmlBal->addChild("Ctas");
				$xmlcta->addAttribute("NumCta", $cta);
				$xmlcta->addAttribute("SaldoIni",str_replace(",", "", str_replace(',','',number_format($sdoini,2))));
				$xmlcta->addAttribute("Debe", str_replace(",", "", str_replace(',','',number_format($debe,2))));
				$xmlcta->addAttribute("Haber", str_replace(",", "", str_replace(',','',number_format($haber,2))));
				$xmlcta->addAttribute("SaldoFin",str_replace(",", "",  str_replace(',','',number_format($sdofin,2))));
				//print_r($xmlcta);exit();
			}
		}
		//exit();
		//print_r($xmlBal);
		//print "../periodos/temp/".$this->rfc.$this->anio.sprintf("%02d", $this->mes)."b".strtolower($this->tipo_envio).".xml";
		$xmlBal->saveXML("../periodos/temp/".$this->rfc.$this->anio.sprintf("%02d", $this->mes)."b".strtolower($this->tipo_envio).".xml");
		
		if (!$this->errors) {
			$enlace = "../periodos/temp/".$this->rfc.$this->anio.sprintf("%02d", $this->mes)."b".strtolower($this->tipo_envio).".xml";
			$enlace2 = "".$this->rfc.$this->anio.sprintf("%02d", $this->mes)."b".strtolower($this->tipo_envio).".xml";
			header ("Content-Disposition: attachment; filename=$enlace2");
			header ("Content-Type: application/force-download");
			header ("Content-Length: ".filesize($enlace));
			readfile($enlace);
		}else{
			print "Error al crear archivo";
		}
	
		return 1;
	}
	
	function Crea_xml_Polizas() {
		$dir = "../periodos/temp/";
		$handle = opendir($dir);
        $ficherosEliminados = 0;
        while ($file = readdir($handle)) {
            if (is_file($dir.$file)) {
                if (unlink($dir.$file) ){
                    $ficherosEliminados++;
                }
            }
        }
		global $db,$conf;
		$xmlBal = new SimpleXMLElement($this->xmlstr);
		$xmlBal->addAttribute("Version", $this->xml_version);
		$xmlBal->addAttribute("RFC", $this->rfc);
		//$xmlBal->addAttribute("TotalCtas", $tot_ctas);
		$xmlBal->addAttribute("Mes", sprintf("%02d", $this->mes));
		$xmlBal->addAttribute("Anio", $this->anio);
		$xmlBal->addattribute("TipoSolicitud", $this->tipo_envio);
		$xmlBal->addattribute("NumOrden", "PLZ".$this->anio."0".sprintf("%02d", $this->mes)."/".sprintf("%02d", $this->mes)."");
		
		$anio=$this->anio;
		$mes=$this->mes;
		
		$sql = "SELECT t.rowid, t.tipo_pol, t.cons, t.anio, t.mes, t.fecha, t.concepto, t.comentario, 
				t.anombrede, t.numcheque, t.fk_facture, t.ant_ctes, t.fechahora,societe_type 
				FROM ".PREFIX."contab_polizas as t WHERE 1  AND anio = ".$this->anio." AND mes = ".$this->mes." AND entity = ".ENTITY." 
				ORDER BY t.rowid ASC";
		$rest=$this->db->query($sql);
		while ($fg=$rest->fetch_object()) {
			$sql = "SELECT t.rowid, t.tipo_pol, t.cons, t.anio,";
			$sql.= " t.mes, t.fecha, t.concepto, t.comentario,";
			$sql.= " t.anombrede, t.numcheque, t.fk_facture,";
			$sql.= " t.ant_ctes, t.fechahora,societe_type";
			$sql.= " FROM ".PREFIX."contab_polizas as t";
	        $sql.= " WHERE t.rowid = ".$fg->rowid;
	        $sql.= " AND entity = ".ENTITY;
			$rsl=$this->db->query($sql);
				$pol=$rsl->fetch_object();
				if ($pol->societe_type == 1) {
					//Es un Cliente
					$sql = 'SELECT f.rowid,f.facnumber,f.ref_client,f.ref_ext,f.ref_int,f.type,f.fk_soc,f.amount,f.tva, f.localtax1, f.localtax2, f.total, f.total_ttc, f.revenuestamp';
					$sql.= ' FROM '.PREFIX.'facture as f';
					$sql.= ' WHERE f.entity = '.ENTITY;
					$sql.= " AND f.rowid=".$pol->fk_facture;
					$rqs=$this->db->query($sql);
					$f=$rqs->fetch_object();
					$facnumber = $f->facnumber;
					$sfcid=$f->fk_soc;
					$sql = 'SELECT s.rowid, s.nom as name, s.name_alias, s.entity, s.ref_ext, s.ref_int, s.address, s.datec as date_creation, s.prefix_comm';
					$sql .= ', s.siren as idprof1, s.siret as idprof2, s.ape as idprof3, s.idprof4, s.idprof5, s.idprof6';
					$sql .= ' FROM '.PREFIX.'societe as s';
					$sql .= ' WHERE s.rowid = '.$sfcid;
					$rqq=$this->db->query($sql);
					$noms=$rqq->fetch_object();
					$rcfsoc=$noms->idprof1;
					$nomsoc=$noms->name;
				} else if($pol->societe_type == 2) {
					//Es un Proveedor
					$sql = "SELECT t.rowid, t.ref, t.ref_supplier, t.entity,";
					$sql.= " t.type, t.fk_soc";
					$sql.= ' FROM '.PREFIX.'facture_fourn as t';
					$sql.= " WHERE t.rowid=".$pol->fk_facture;
					//print $sql;
					$rqs=$this->db->query($sql);
					$ff=$rqs->fetch_object();
					$facnumber = $ff->ref;
					$sfcid=$ff->fk_soc;
					$sql = 'SELECT s.rowid, s.nom as name, s.name_alias, s.entity, s.ref_ext, s.ref_int, s.address, s.datec as date_creation, s.prefix_comm';
					$sql .= ', s.siren as idprof1, s.siret as idprof2, s.ape as idprof3, s.idprof4, s.idprof5, s.idprof6';
					$sql .= ' FROM '.PREFIX.'societe as s';
					$sql .= ' WHERE s.rowid = '.$sfcid;
					$rqq=$this->db->query($sql);
					$noms=$rqq->fetch_object();
					$nomsoc=$noms->name;
				}
				$sql4="SELECT sum(debe) as debe, sum(haber) as haber
							FROM ".PREFIX."contab_polizasdet
							WHERE fk_poliza=".$pol->rowid;
				$rest4=$this->db->query($sql4);
				$fg4=$rest4->fetch_object();
				
				if($fg4->debe!= 0 || $fg4->haber!= 0){
					/*Cabecera*/
					$xmlcta = $xmlBal->addChild("Poliza");
					$xmlcta->addAttribute("Fecha", $pol->fecha);
					$String= ($pol->concepto);
					$String = str_replace(array('á','à','â','ã','ª','ä'),"a",$String);
					$String = str_replace(array('Á','À','Â','Ã','Ä'),"A",$String);
					$String = str_replace(array('Í','Ì','Î','Ï'),"I",$String);
					$String = str_replace(array('í','ì','î','ï'),"i",$String);
					$String = str_replace(array('é','è','ê','ë'),"e",$String);
					$String = str_replace(array('É','È','Ê','Ë'),"E",$String);
					$String = str_replace(array('ó','ò','ô','õ','ö','º'),"o",$String);
					$String = str_replace(array('Ó','Ò','Ô','Õ','Ö'),"O",$String);
					$String = str_replace(array('ú','ù','û','ü'),"u",$String);
					$String = str_replace(array('Ú','Ù','Û','Ü'),"U",$String);
					$String = str_replace("ç","c",$String);
					$String = str_replace("Ç","C",$String);
					$String = str_replace("ñ","n",$String);
					$String = str_replace("Ñ","N",$String);
					$String = str_replace("Ý","Y",$String);
					$String = str_replace("ý","y",$String);
					$concep=$String;
					$xmlcta->addAttribute("Concepto", $concep);
					$xmlcta->addAttribute("NumUnIdenPol", $pol->cons);
					//print_r($xmlcta);
					//$xmlcta->addAttribute("NumUnIdenPol", $pol->cons);
					/*TIPO*/
					/* if ($pol->tipo_pol == "D") { $xmlcta->addAttribute("Tipo", 3); }
					else if($pol->tipo_pol == "E") { $xmlcta->addAttribute("Tipo", 2);}
					else if($pol->tipo_pol == "I") { $xmlcta->addAttribute("Tipo", 1); } */
					/*/TIPO*/
					
					$sql2="SELECT rowid
							FROM ".PREFIX."contab_polizasdet
							WHERE fk_poliza=".$pol->rowid;
					$rest2=$this->db->query($sql2);
					while ($fg2=$rest2->fetch_object()){
						$sqm = "SELECT t.rowid, t.asiento, t.cuenta, t.debe,";
						$sqm.= " t.haber, t.descripcion, t.fk_poliza,b.descripcion as nomcuenta";
						$sqm.= " FROM ".PREFIX."contab_polizasdet as t, ".PREFIX."contab_cat_ctas as b";
				        $sqm.= " WHERE t.rowid = ".$fg2->rowid." AND t.cuenta=b.codagr";
				        //print $sqm;exit();
				        $rfs=$this->db->query($sqm);
						$pd=$rfs->fetch_object();
						/*Cabecera*/
						$concepto=$pd->descripcion;
						if($concepto==''){
							$concepto=$pd->nomcuenta;
						}
						if ($pd->debe != 0) {
							$xmlctatras = $xmlcta->addChild("Transaccion");
							$xmlctatras->addAttribute("NumCta",$pd->cuenta);
							$xmlctatras->addAttribute("DesCta",$pd->nomcuenta);
							$xmlctatras->addAttribute("Concepto",$concepto);
							$debe=str_replace(",", "", number_format(($pd->debe), 2));
							$haber=0.00;
							$xmlctatras->addAttribute("Debe",$debe);
							$xmlctatras->addAttribute("Haber",$haber);
							 if ($pol->societe_type == 1) {
							 	$sqmm="SHOW TABLES LIKE '".PREFIX."const'";
							 	$ts=$this->db->query($sqmm);
							 	$tsr=$ts->num_rows;//->fetch_object();
							 	if($tsr>0){
							 		$sqmm="SELECT count(*) as con
										FROM ".PREFIX."const
										WHERE name='MAIN_MODULE_CFDIMX' AND entity=".ENTITY;
							 		$ts=$this->db->query($sqmm);
							 		$tsr=$ts->fetch_object();
							 		//print $tsr->con."<<<";exit();
									if($tsr->con>0){
										$sql3="SELECT uuid
											FROM ".PREFIX."cfdimx
											WHERE fk_facture=".$f->rowid." AND entity_id=".ENTITY;
										$rest3=$this->db->query($sql3);
										$nr3=$rest3->num_rows;
										if($nr3>0){
											$fg3=$rest3->fetch_object();
											$xmlcfdi=$xmlctatras->addChild("CompNal");
											$xmlcfdi->addAttribute("UUID_CFDI",$fg3->uuid);
											$xmlcfdi->addAttribute("RFC",$rcfsoc);
											$xmlcfdi->addAttribute("MontoTotal",str_replace(",", "", number_format($f->total_ttc,2)));
											//print_r($xmlcfdi);
										}
									}
							 	}
							} 
						}else if($pd->haber != 0){
							$xmlctatras = $xmlcta->addChild("Transaccion");
							$xmlctatras->addAttribute("NumCta",$pd->cuenta);
							$xmlctatras->addAttribute("DesCta",$pd->nomcuenta);
							$xmlctatras->addAttribute("Concepto",$concepto);
							$debe=0.00;
							$haber=str_replace(",", "", number_format(($pd->haber), 2));
							$xmlctatras->addAttribute("Debe",$debe);
							$xmlctatras->addAttribute("Haber",$haber);
							if ($pol->societe_type == 1) {
							 	$sqmm="SHOW TABLES LIKE '".PREFIX."const'";
							 	$ts=$this->db->query($sqmm);
							 	$tsr=$ts->num_rows;//->fetch_object();
							 	if($tsr>0){
							 		$sqmm="SELECT count(*) as con
										FROM ".PREFIX."const
										WHERE name='MAIN_MODULE_CFDIMX' AND entity=".ENTITY;
							 		$ts=$this->db->query($sqmm);
							 		$tsr=$ts->fetch_object();
							 		//print $tsr->con."<<<";exit();
									if($tsr->con>0){
										$sql3="SELECT uuid
											FROM ".PREFIX."cfdimx
											WHERE fk_facture=".$f->rowid." AND entity_id=".ENTITY;
										$rest3=$this->db->query($sql3);
										$nr3=$rest3->num_rows;
										if($nr3>0){
											$fg3=$rest3->fetch_object();
											$xmlcfdi=$xmlctatras->addChild("CompNal");
											$xmlcfdi->addAttribute("UUID_CFDI",$fg3->uuid);
											$xmlcfdi->addAttribute("RFC",$rcfsoc);
											$xmlcfdi->addAttribute("MontoTotal",str_replace(",", "", number_format($f->total_ttc,2)));
											//print_r($xmlcfdi);
										}
									}
							 	}
							} 
						}
						unset($pd);
					}
					unset($pd2);
				}
				unset($pol);
				unset($soc);
				unset($ctas);
				
			}
			//exit();
		$xmlBal->saveXML("../periodos/temp/".$this->rfc.$this->anio.sprintf("%02d", $this->mes)."PL".".xml");
		
		if (!$this->errors) {
			$enlace = "../periodos/temp/".$this->rfc.$this->anio.sprintf("%02d", $this->mes)."PL".".xml";
			$enlace2 = "".$this->rfc.$this->anio.sprintf("%02d", $this->mes)."PL".".xml";
			header ("Content-Disposition: attachment; filename=$enlace2");
			header ("Content-Type: application/force-download");
			header ("Content-Length: ".filesize($enlace));
			readfile($enlace);
		}else{
			print "Error al crear archivo";
		}
		
		return 1;
	}
	
	function Verify_Path() {
		if (!is_dir($this->file_path)) {
			$ret = mkdir($this->file_path);
		}
		if ($ret === true || is_dir($this->file_path)) {
		
		} else {
			$this->errors = "Hubo un error al querer almacenar el archivo XML en la carpeta temporal";
			$this->error = 1;
		}
	}
}
?>
