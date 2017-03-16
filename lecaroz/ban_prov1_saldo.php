<?php
// CONSULTA DE PRODUCCION
// Tabla 'produccion'
// Menu 'Panaderías->Producción'
//define ('IDSCREEN',1241); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron registros";
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl");
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_prov1_saldo.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");
// -------------------------------- Buscar datos de compañía -------------------------------------------------

	// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	$tpl->printToScreen();
	die();
}
if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
	$tpl->printToScreen();
	die();
}


// -------------------------------- GENERAR ARREGLO ---------------------------------------------------------
if($_GET['id']=="") {
	if ($_GET['cia'] < 900)
		$sql="SELECT pasivo_proveedores.*, pasivo_proveedores.fecha AS fecha_mov, NULL AS clave, CASE WHEN fecha_solicitud IS NOT NULL AND fecha_aclaracion IS NULL THEN TRUE ELSE FALSE END AS pendiente, CASE WHEN xml_file != '' THEN TRUE ELSE FALSE END AS cfdi FROM pasivo_proveedores LEFT JOIN facturas_pendientes USING (num_proveedor, num_fact) LEFT JOIN facturas USING (num_proveedor, num_fact) WHERE pasivo_proveedores.num_cia=".$_GET['cia']."/* AND pasivo_proveedores.num_proveedor NOT IN (15, 792) AND pasivo_proveedores.total > 0 AND (num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM pasivo_proveedores WHERE num_proveedor = 283 AND fecha < '01/01/2014')*/ ORDER BY pasivo_proveedores.num_cia,pasivo_proveedores.fecha";
	else
		$sql="SELECT num_cia, num_proveedor, num_fact, fecha AS fecha_mov, total, copia_fac, clave, CASE WHEN clave > 0 THEN 1 ELSE 0 END AS tipo, FALSE AS pendiente, CASE WHEN xml_file != '' THEN TRUE ELSE FALSE END AS cfdi FROM facturas_zap WHERE num_cia = $_GET[cia] AND total > 0 AND folio IS NULL AND sucursal <> 'TRUE' ORDER BY tipo, num_cia, fecha_mov";
}
else {
	if ($_GET['cia'] < 900)
		$sql="SELECT pasivo_proveedores.*, pasivo_proveedores.fecha AS fecha_mov, NULL AS clave, CASE WHEN fecha_solicitud IS NOT NULL AND fecha_aclaracion IS NULL THEN TRUE ELSE FALSE END AS pendiente, CASE WHEN xml_file != '' THEN TRUE ELSE FALSE END AS cfdi FROM pasivo_proveedores LEFT JOIN facturas_pendientes USING (num_proveedor, num_fact) LEFT JOIN facturas USING (num_proveedor, num_fact) WHERE /*pasivo_proveedores.total > 0 AND pasivo_proveedores.num_proveedor NOT IN (15, 792) AND (num_proveedor, num_fact) NOT IN (SELECT num_proveedor, num_fact FROM pasivo_proveedores WHERE num_proveedor = 283 AND fecha < '01/01/2014') AND*/ pasivo_proveedores.id=".$_GET['id'];
	else
		$sql="SELECT num_cia, num_proveedor, num_fact, fecha AS fecha_mov, total, copia_fac, clave, CASE WHEN clave > 0 THEN 1 ELSE 0 END AS tipo, FALSE AS pendiente, CASE WHEN xml_file != '' THEN TRUE ELSE FALSE END AS cfdi FROM facturas_zap WHERE id = $_GET[id] AND sucursal <> 'TRUE' ORDER BY tipo, num_cia, fecha_mov";
}
$pasivo=ejecutar_script($sql,$dsn);

//print_r($pasivo);
//----------------------------------------------------------------------------------------------------------
if(!$pasivo){
	header("location: ./ban_prov1_saldo.php?codigo_error=1");
	die();
}


$aux_cia= -1;
$aux_prov= -1;
$clave = 0;

$total_proveedor=0;
$total_prov=0;
$total_cia=0;

$tope=count($pasivo);
$tope -=1;
for($i=0;$i<count($pasivo);$i++){
	if($aux_cia != $pasivo[$i]['num_cia'] || ($pasivo[$i]['clave'] > 0 && $clave == 0)){
		$total_cia=0;
		$tpl->newBlock("compania");
		$tpl->assign("num_cia",$pasivo[$i]['num_cia']);
		$cia=obtener_registro("catalogo_companias",array("num_cia"),array($pasivo[$i]['num_cia']),"","",$dsn);
		$tpl->assign("nom_compania",$cia[0]['nombre_corto']);
		$aux_cia=$pasivo[$i]['num_cia'];
		$clave = $pasivo[$i]['clave'] > 0 ? 1 : 0;
		if($_GET['id']!="")
			$tpl->newBlock("centrado");

	}
	$tpl->newBlock("rows1");
	$tpl->assign("num_proveedor",$pasivo[$i]['num_proveedor'] . ($pasivo[$i]['clave'] > 0 ? "-{$pasivo[$i]['clave']}" : ''));
	$prov=obtener_registro("catalogo_proveedores",array("num_proveedor"),array($pasivo[$i]['num_proveedor']),"","",$dsn);
	$tpl->assign("nom_proveedor",$prov[0]['nombre']);
	$tpl->assign("num_fact",$pasivo[$i]['num_fact']);
	$tpl->assign("fecha_mov",$pasivo[$i]['fecha_mov']);
	$tpl->assign("importe",number_format($pasivo[$i]['total'],2,'.',','));
	$tpl->assign('v', $pasivo[$i]['copia_fac'] == 't' ? '<span style="' . ($pasivo[$i]['pendiente'] == 't' ? 'color:#C00;' : '') . '">X</span>' : '&nbsp;');
	$tpl->assign('cfdi', $pasivo[$i]['cfdi'] == 't' ? 'X' : '&nbsp;');
	$total_cia +=$pasivo[$i]['total'];

//	if($_GET['id']=="")	{
		$tpl->gotoBlock("compania");
		$tpl->assign("total_cia",number_format($total_cia,2,'.',','));
//	}
}

$tpl->printToScreen();
?>
