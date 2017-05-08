<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);

$bbcode = array(
				'[N]' => "<strong>",
				'[/N]' => "</strong>",
				'[C]' => "<i>",
				'[/C]' => "</i>",
				'[S]' => "<u>",
				'[/S]' => "</u>"
				);

function reemplazar_bbcode($texto) {
	$search = array_keys($GLOBALS['bbcode']);
	$texto = str_replace($search, $GLOBALS['bbcode'], $texto);
	return $texto;
}

if (isset($_POST['num_cia1'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/ban/carta.tpl" );
	$tpl->prepare();
	
	$sql = "SELECT num_cia, nombre, direccion FROM catalogo_companias WHERE";
	$sql .= !in_array($_SESSION['iduser'], $users) ? " num_cia BETWEEN 1 AND 800" : " num_cia BETWEEN 900 AND 950";
	$sql .= $_POST['num_cia1'] > 0 || $_POST['num_cia2'] > 0 ? ($_POST['num_cia1'] > 0 && $_POST['num_cia2'] > 0 ? " AND num_cia BETWEEN $_POST[num_cia1] AND $_POST[num_cia2]" : " AND num_cia = $_POST[num_cia1]") : "";
	$sql .= " ORDER BY num_cia";
	$result = $db->query($sql);
	
	if (!$result) {
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}
	
	$dia = date("d");
	$mes = date("n");
	$anio = date("Y");
	
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("carta");
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", strtoupper($result[$i]['nombre']));
		$tpl->assign("direccion", strtoupper($result[$i]['direccion']));
		$tpl->assign("dia", $dia);
		$tpl->assign("mes", mes_escrito($mes, TRUE));
		$tpl->assign("anio", $anio);
		$tpl->assign("dirigida_a", $_POST['dirigida_a'] != "" ? strtoupper($_POST['dirigida_a']) : "A QUIEN CORRESPONDA");
		$texto = strtoupper($_POST['texto']);
		$texto = str_replace("\n", "<br>", $texto);
		$texto = str_replace("ñ", "Ñ", $texto);
		$texto = reemplazar_bbcode($texto);
		$tpl->assign("texto", $texto);
		if (in_array($_SESSION['iduser'], $users))
			$tpl->assign("firma", $_POST['firma_zap'] != "" ? strtoupper($_POST['firma_zap']) : "RAMON IRIGOYEN LERCHUNDI");
		else {
			if (($result[$i]['num_cia'] > 100 && $result[$i]['num_cia'] < 200) || ($result[$i]['num_cia'] > 701 && $result[$i]['num_cia'] < 750))
				$tpl->assign("firma", $_POST['firma_pan'] != "" ? strtoupper($_POST['firma_ros']) : "ILDEFONSO LARRACHEA ECHENIQUE");
			else
				$tpl->assign("firma", $_POST['firma_pan'] != "" ? strtoupper($_POST['firma_pan']) : "JULIAN EUGENIO LARRACHEA ECHENIQUE");
		}
	}
	
	$tpl->printToScreen();
	die;
} 

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_gen_car.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE" . (in_array($_SESSION['iduser'], $users) ? " num_cia BETWEEN 900 AND 950" : " num_cia < 100") . " ORDER BY num_cia";
$cia = $db->query($sql);
for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

$tpl->newBlock(in_array($_SESSION['iduser'], $users) ? "zap" : "pan");

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
?>