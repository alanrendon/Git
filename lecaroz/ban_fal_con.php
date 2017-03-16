<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "";

$db = new DBclass($dsn, "autocommit=yes");

$users = array(28, 29, 30, 31);

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_fal_con.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Insertar datos
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
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
	die;
}

$tpl->newBlock("listado");

$sql = "SELECT id, num_cia, nombre_corto, fecha, deposito, importe, tipo, descripcion FROM faltantes_cometra LEFT JOIN catalogo_companias USING (num_cia) WHERE" . ($_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia] AND" : "") . (in_array($_SESSION['iduser'], $users) ? " num_cia BETWEEN 900 AND 950 AND " : " num_cia BETWEEN 1 AND 800 AND ") . " fecha_con IS NULL ORDER BY num_cia, fecha, tipo DESC";
$result = $db->query($sql);

if ($result) {
	$num_cia = NULL;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			if ($num_cia != NULL) {
				$tpl->assign("cia.deposito", number_format($depositos, 2, ".", ","));
				$tpl->assign("cia.faltante", number_format($faltantes, 2, ".", ","));
				$tpl->assign("cia.sobrante", number_format($sobrantes, 2, ".", ","));
				$tpl->assign("cia.diferencia", number_format($faltantes - $sobrantes, 2, ".", ","));
			}
			
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
			
			$depositos = 0;
			$faltantes = 0;
			$sobrantes = 0;
		}
		$tpl->newBlock("fila");
		$tpl->assign("id", $result[$i]['id']);
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("deposito", $result[$i]['deposito'] != 0 ? number_format($result[$i]['deposito'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("faltante", $result[$i]['tipo'] == "f" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("sobrante", $result[$i]['tipo'] == "t" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("descripcion", $result[$i]['descripcion'] != "" ? $result[$i]['descripcion'] : "&nbsp;");
		
		$depositos += $result[$i]['deposito'];
		$faltantes += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : 0;
		$sobrantes += $result[$i]['tipo'] == "t" ? $result[$i]['importe'] : 0;
	}
	
	if ($num_cia != NULL) {
		$tpl->assign("cia.deposito", number_format($depositos, 2, ".", ","));
		$tpl->assign("cia.faltante", number_format($faltantes, 2, ".", ","));
		$tpl->assign("cia.sobrante", number_format($sobrantes, 2, ".", ","));
		$tpl->assign("cia.diferencia", number_format($faltantes - $sobrantes, 2, ".", ","));
	}
}
else
	$tpl->newBlock("no_result");

$tpl->printToScreen();
$db->desconectar();
?>