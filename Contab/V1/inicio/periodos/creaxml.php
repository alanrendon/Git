<?php
if($_REQUEST['crearxml']=='create_catalogo_xml'){
	require_once ("../class/crear_xml.class.php");
	require_once "../class/admin.class.php";
	$eDolibarr = new admin();
	$valores = $eDolibarr->get_user();
	$xml = new CrearXML();
	$xml->file_path = "../periodos/";
	$xml->Verify_Path();
	if (!$xml->error) {
		$xml->anio = $_REQUEST['xanio'];
		$xml->mes = $_REQUEST['xmes'];
		$xml->rfc = $valores['MAIN_INFO_SIREN'];
	$xml->xmlstr =
	<<<XML
<catalogocuentas:Catalogo xmlns:catalogocuentas="www.sat.gob.mx/esquemas/ContabilidadE/1_1/CatalogoCuentas" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="www.sat.gob.mx/esquemas/ContabilidadE/1_1/CatalogoCuentas http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/CatalogoCuentas/CatalogoCuentas_1_1.xsd">
</catalogocuentas:Catalogo>
XML;
		$xml->Crea_Catalogo();
		$mesg = $xml->mesg;
		//print_r($xml);
// 		$errors = $xml->errors;
// 		if ($errors) {
// 			$cta_err = $xml->cta_err;
// 		}
	 } else {
	 	$errors = $xml->errors;
	 }  
}

if($_REQUEST['crearxml']=='create_balanza_xml'){
	require_once ("../class/crear_xml.class.php");
	require_once "../class/admin.class.php";
	$eDolibarr = new admin();
	$valores = $eDolibarr->get_user();
	$xml = new CrearXML(); 
	$xml->file_path = "../periodos/";
	$xml->Verify_Path();
	if(!$xml->error) {
		$xml->anio = $_REQUEST['xanio'];
		$xml->mes = $_REQUEST['xmes'];
		$xml->rfc = $valores['MAIN_INFO_SIREN'];
		$xml->tipo_envio = $_REQUEST['tipoenv'];
		$xml->xmlstr =
		<<<XML
<BCE:Balanza xmlns:BCE="http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/BalanzaComprobacion" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/BalanzaComprobacion http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/BalanzaComprobacion/BalanzaComprobacion_1_1.xsd">
</BCE:Balanza>
XML;
		$xml->Crea_Balanza();
		$mesg = $xml->mesg;
		$errors = $xml->errors;
		if ($errors) {
			print $xml->cta_err;
		}
	 } else {
		$errors = $xml->errors;
	} 
}

if($_REQUEST['crearxml']=='create_xml_polizas'){
	require_once ("../class/crear_xml.class.php");
	require_once "../class/admin.class.php";
	$eDolibarr = new admin();
	$valores = $eDolibarr->get_user();
	$xml = new CrearXML();
	$xml->file_path = "../periodos/";
	$xml->Verify_Path();
	
	if (!$xml->error) {
		$xml->anio = $_REQUEST['xanio'];
		$xml->mes = $_REQUEST['xmes'];
		$xml->rfc = $valores['MAIN_INFO_SIREN'];
		$xml->tipo_envio = "AF";///REVISAR ESTO XML
		$xml->xmlstr =
		<<<XML
<PLZ:Polizas xmlns:PLZ="http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/PolizasPeriodo" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/PolizasPeriodo http://www.sat.gob.mx/esquemas/ContabilidadE/1_1/PolizasPeriodo/PolizasPeriodo_1_1.xsd">
</PLZ:Polizas>
XML;
		$xml->Crea_xml_Polizas();
		$mesg = $xml->mesg;
		$errors = $xml->errors;
		if ($errors) {
			$cta_err = $xml->cta_err;
		}
	} else {
		$errors = $xml->errors;
	}
}