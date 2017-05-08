<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

if (isset($_POST['num_cia'])) {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_POST['mes'], 1, $_POST['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_POST['mes'] + 1, 0, $_POST['anio']));
	
	$sql = "DELETE FROM facturas_diarias WHERE num_cia = $_POST[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2';\n";
	foreach ($_POST['dia'] as $i => $dia)
		if (($importe = get_val($_POST['importe'][$i])) > 0) {
			$fecha = date('d/m/Y', mktime(0, 0, 0, $_POST['mes'], $dia, $_POST['anio']));
			$sql .= "INSERT INTO facturas_diarias (num_cia, fecha, importe) VALUES ($_POST[num_cia], '$fecha', $importe);\n";
		}
	$db->query($sql);
	die(header('location: ./ban_fac_imp_cap.php'));
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_fac_imp_cap.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'], 1, $_GET['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	$dias = intval(date('d', mktime(0, 0, 0, $_GET['mes']+ 1, 0, $_GET['anio'])));
	
	$importes = array();
	for ($i = 1; $i <= $dias; $i++)
		$importes[$i] = 0;
	
	$sql = "SELECT extract(day from fecha) AS dia, importe FROM facturas_diarias WHERE num_cia = $_GET[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2' ORDER BY dia";
	$result = $db->query($sql);
	
	if ($result)
		foreach ($result as $reg)
			$importes[get_val($reg['dia'])] = get_val($reg['importe']);
	
	$tpl->newBlock('captura');
	$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]");
	$tpl->assign('num_cia', $_GET['num_cia']);
	$tpl->assign('nombre', $nombre_cia[0]['nombre_corto']);
	$tpl->assign('mes', $_GET['mes']);
	$tpl->assign('anio', $_GET['anio']);
	$tpl->assign('mes_escrito', mes_escrito($_GET['mes'], TRUE));
	
	for ($i = 0; $i < $dias; $i++) {
		$tpl->newBlock('fila');
		$tpl->assign('i', $i);
		$tpl->assign('next', $i < $dias - 1 ? $i + 1 : 0);
		$tpl->assign('back', $i > 0 ? $i - 1 : $dias - 1);
		$tpl->assign('dia', $i + 1);
		$tpl->assign('importe', $importes[$i + 1] > 0 ? number_format($importes[$i + 1], 2, '.', ',') : '');
	}
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');
$tpl->assign(date('n'), ' selected');
$tpl->assign('anio', date('Y'));

$cias = $db->query('SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia < 900 ORDER BY num_cia');
foreach ($cias as $cia) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $cia['num_cia']);
	$tpl->assign('nombre', $cia['nombre']);
}

$tpl->printToScreen();
?>