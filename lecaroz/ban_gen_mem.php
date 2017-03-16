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
				'[/S]' => "</u>",
				'&NBSP;' => "&nbsp;"
				);

function reemplazar_bbcode($texto) {
	$search = array_keys($GLOBALS['bbcode']);
	$texto = str_replace($search, $GLOBALS['bbcode'], $texto);
	return $texto;
}

if (isset($_POST['num_cia1'])) {
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/ban/memorandum.tpl" );
	$tpl->prepare();
	
	$sql = "SELECT num_cia, num_cia_primaria, nombre, direccion FROM catalogo_companias";
	$sql .= !isset($_POST['incluye_cuerpo']) ? ($_POST['num_cia1'] > 0 || $_POST['num_cia2'] > 0 ? ($_POST['num_cia1'] > 0 && $_POST['num_cia2'] > 0 ? " WHERE num_cia BETWEEN $_POST[num_cia1] AND $_POST[num_cia2]" : " WHERE num_cia = $_POST[num_cia1]") : "") : " WHERE num_cia = $_POST[num_cia1]";
	//$sql .= " WHERE num_cia BETWEEN 600 AND 700 OR num_cia = 800";
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
	
	$texto = (isset($_POST['incluye_cuerpo']) ? "Por medio del presente se le comunica que en su hoja del dia <strong>$_POST[fecha_reclamo]</strong> se encontro " : "") . $_POST['texto'];
	
	for ($i = 0; $i < count($result); $i++) {
		$tpl->newBlock("memo");
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", strtoupper($result[$i]['nombre']));
		$tpl->assign("dia", $dia);
		$tpl->assign("mes", mes_escrito($mes, TRUE));
		$tpl->assign("anio", $anio);
		$encargado = $db->query("SELECT nombre_fin FROM encargados WHERE num_cia = {$result[$i]['num_cia']} ORDER BY anio DESC, mes DESC LIMIT 1");
		$tpl->assign("encargado", strtoupper($encargado[0]['nombre_fin']));
		$texto = str_replace("\n", "<br>", $texto);
		$texto = str_replace("ñ", "Ñ", $texto);
		$texto = strtoupper($texto);
		$texto = reemplazar_bbcode($texto);
		$tpl->assign("texto", $texto);
		$tpl->assign("firma", strtoupper($_POST['firma']));
		$tpl->assign("ccp", strtoupper($_POST['ccp']));
		
		if (isset($_POST['incluye_cuerpo'])) {
			$temp = $db->query("SELECT folio FROM memorandums ORDER BY folio DESC LIMIT 1");
			$folio = $temp ? $temp[0]['folio'] + 1 : 1;
			$tpl->assign("folio", $folio);
			
			$sql = "INSERT INTO memorandums (num_cia, folio, firma, ccp, memo, fecha, fecha_reclamo, fecha_aclarado, iduser) VALUES (";
			$sql .= "{$result[$i]['num_cia']}, $folio, '";
			$sql .= strtoupper($_POST['firma']) . "', '";
			$sql .= strtoupper($_POST['ccp']) . "',";
			$sql .= " '$texto', CURRENT_DATE, '$_POST[fecha_reclamo]', NULL, $_SESSION[iduser])";
			$db->query($sql);
		}
	}
	
	$tpl->printToScreen();
	die;
} 

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_gen_mem.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$tpl->assign("fecha", date("d/m/Y", mktime(0, 0, 0, date("n"), date("d") - 1, date("Y"))));
$tpl->assign("disabled", $_SESSION['iduser'] == 28 ? "disabled" : "");

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE" . (in_array($_SESSION['iduser'], $users) ? " num_cia BETWEEN 900 AND 950" : " num_cia <= 300") . " ORDER BY num_cia";
$cia = $db->query($sql);
for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

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