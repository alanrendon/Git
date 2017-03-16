<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['num_cia1'])) {
	$cuenta = $_GET['cuenta'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2';
	$banco = $_GET['cuenta'] == 1 ? 'BANORTE' : 'SANTANDER';
	$dia = date('d');
	$mes = mes_escrito(date('n'));
	$anio = date('Y');
	$nombres[1] = array('JULIAN EUGENIO LARRACHEA ECHENIQUE', 'BAUTISTA GOÑI ARAMBURU', 'ILDEFONSO LARRACHEA ECHENIQUE', 'JUAN MANUEL GOÑI LARRACHEA');
	$nombres[2] = array('JULIAN EUGENIO LARRACHEA ECHENIQUE', 'JUAN MANUEL GOÑI LARRACHEA', 'RAMON IRIGOYEN LERCHUNDI');
	$sql = "SELECT num_cia, nombre, $cuenta FROM catalogo_companias WHERE num_cia BETWEEN ";
	$sql .= ($_SESSION['iduser'] != 1 ? ($_SESSION['iduser'] >= 28 ? '900 AND 950' : '1 AND 800') : '1 AND 999');
	$sql .= " AND ($cuenta IS NOT NULL OR trim($cuenta) != '')";
	$sql .= $_GET['num_cia1'] > 0 ? ' AND num_cia ' . ($_GET['num_cia2'] > 0 ? "BETWEEN $_GET[num_cia1] AND $_GET[num_cia2]" : "= $_GET[num_cia1]") : '';
	$sql .= " ORDER BY num_cia";
	$result = $db->query($sql);
	
	if (!$result) {
		$tpl->newBlock('cerrar');
		$tpl->printToScreen();
		die;
	}
	
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/ban/carta_autorizacion.tpl" );
	$tpl->prepare();
	
	foreach ($result as $reg) {
		$tpl->newBlock('carta');
		$tpl->assign('dia', $dia);
		$tpl->assign('mes', $mes);
		$tpl->assign('anio', $anio);
		$tpl->assign('banco', $banco);
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre_cia', $reg['nombre']);
		$tpl->assign('cuenta', $reg[$cuenta]);
		
		$i = $reg['num_cia'] >= 900 ? 2 : 1;
		foreach ($nombres[$i] as $val) {
			$tpl->newBlock('nombre');
			$tpl->assign('nombre', $val);
		}
	}
	$tpl->printToScreen();
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_aut_fir.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message",$descripcion_error[$_GET['codigo_error']]);	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}
$tpl->printToScreen();
?>