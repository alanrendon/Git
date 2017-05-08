<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] == 1) die("Pantalla no disponible...");

$users = array(28, 29, 30, 31);

$descripcion_error[1] = 'NO HAY RESULTADOS';

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_esc_rec.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$clabe_cuenta = $_GET['cuenta'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2';
	$sql = "SELECT num_cia, nombre_corto, $clabe_cuenta FROM catalogo_companias LEFT JOIN (SELECT num_cia, count(id) AS cont FROM estados_cuenta_recibidos";
	$sql .= " WHERE anio = $_GET[anio] AND mes <= $_GET[mes] AND cuenta = $_GET[cuenta] GROUP BY num_cia) AS recibidos USING (num_cia) WHERE $clabe_cuenta IS NOT NULL ";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= ($_SESSION['iduser'] != 1 ? 'AND num_cia BETWEEN ' . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 800') : '') . " AND (" . (!isset($_GET['todos']) ? "cont < $_GET[mes] OR " : "cont <= $_GET[mes] OR ") . "cont IS NULL) ORDER BY num_cia";
	$result = $db->query($sql);
	
	if (!$result)
		die(header('location: ./ban_esc_rec.php?codigo_error=1'));
	
	$tpl->newBlock('listado');
	$tpl->assign('banco', $_GET['cuenta'] == 1 ? 'Banorte' : 'Santander');
	$tpl->assign('anio', $_GET['anio']);
	
	foreach ($result as $reg) {
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre_corto']);
		$tpl->assign('cuenta', $reg[$clabe_cuenta]);
		
		if ($meses = $db->query("SELECT mes FROM estados_cuenta_recibidos WHERE num_cia = $reg[num_cia] AND anio = $_GET[anio] AND mes <= $_GET[mes] AND cuenta = $_GET[cuenta] ORDER BY mes"))
			foreach ($meses as $mes)
				$tpl->assign('color' . $mes['mes'], ' bgcolor="#660099"');
	}
	
	die($tpl->printToScreen());
}

$tpl->newBlock('datos');

$tpl->assign(date('n', mktime(0, 0, 0, date('n'), 0, date('Y'))), ' selected');
$tpl->assign('anio', date('Y', mktime(0, 0, 0, date('n'), 0, date('Y'))));

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias " . ($_SESSION['iduser'] != 1 ? ' WHERE num_cia BETWEEN ' . ($_SESSION['iduser'] >= 28 ? '900 AND 998' : '1 AND 800') : '') . " ORDER BY num_cia";
$result = $db->query($sql);
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre_corto']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>