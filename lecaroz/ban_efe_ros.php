<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------

$db = new DBclass($dsn);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_efe_ros.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['porcentaje'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign(date("n"), "selected");
	$tpl->assign("anio", date("Y"));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	
	$tpl->printToScreen();
	$db->desconectar();
	die;
}

$fecha1 = "1/$_GET[mes]/$_GET[anio]";
$fecha2 = date("d/m/Y", mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));

$sql = "SELECT num_cia,nombre_corto,sum(efectivo) AS efectivo FROM total_companias LEFT JOIN catalogo_companias USING (num_cia) WHERE";
$sql .= ($_GET['num_cia'] > 300 && $_GET['num_cia'] < 600) || ($_GET['num_cia'] > 701 && $_GET['num_cia'] < 750) ? " num_cia = $_GET[num_cia]" : " (num_cia BETWEEN 301 AND 599 OR num_cia BETWEEN 702 AND 749)";
$sql .= " AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia,nombre_corto ORDER BY num_cia";
$result = $db->query($sql);

if (!$result) {
	$db->desconectar();
	header("location: ./ban_efe_ros.php?codigo_error=1");
	die;
}

$tpl->newBlock("listado");
$tpl->assign("mes", mes_escrito($_GET['mes']));
$tpl->assign("anio", $_GET['anio']);
$tpl->assign("por", number_format($_GET['porcentaje'],2,".",","));

$efectivo = 0;
$iva = 0;
$porcentaje = 0;
$iva_por = 0;

for ($i=0; $i<count($result); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $result[$i]['num_cia']);
	$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
	$tpl->assign("efectivo", $result[$i]['efectivo'] != 0 ? number_format($result[$i]['efectivo'],2,".",",") : "&nbsp;");
	$tpl->assign("iva", $result[$i]['efectivo'] != 0 ? number_format($result[$i]['efectivo'] * 0.15,2,".",",") : "&nbsp;");
	$tpl->assign("porcentaje", $result[$i]['efectivo'] != 0 ? number_format($result[$i]['efectivo'] * $_GET['porcentaje'] / 100,2,".",",") : "&nbsp;");
	$tpl->assign("iva_por", $result[$i]['efectivo'] != 0 ? number_format(($result[$i]['efectivo'] * $_GET['porcentaje'] / 100) * 0.15,2,".",",") : "&nbsp;");
	
	$efectivo += $result[$i]['efectivo'];
	$iva += $result[$i]['efectivo'] * 0.15;
	$porcentaje += $result[$i]['efectivo'] * $_GET['porcentaje'] / 100;
	$iva_por += ($result[$i]['efectivo'] * $_GET['porcentaje'] / 100) * 0.15;
}
$tpl->assign("listado.efectivo", number_format($efectivo,2,".",","));
$tpl->assign("listado.iva", number_format($iva,2,".",","));
$tpl->assign("listado.porcentaje", number_format($porcentaje,2,".",","));
$tpl->assign("listado.iva_por", number_format($iva_por,2,".",","));

$tpl->printToScreen();
$db->desconectar();
?>