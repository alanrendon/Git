<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, 'autocommit=yes');

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/ros/ros_com_con_v2.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha1 = "01/$_GET[mes]/$_GET[anio]";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));

	$sql = "SELECT num_cia, nombre_corto AS nombre, sum(efectivo) AS efectivo FROM total_companias LEFT JOIN catalogo_companias USING (num_cia) WHERE";
	$sql .= " fecha BETWEEN '$fecha1' AND '$fecha2'";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
	$sql .= " GROUP BY num_cia, nombre_corto ORDER BY num_cia";
	$result = $db->query($sql);

	if (!$result)
		die(header('location: ./ros_com_con_v2.php?codigo_error=1'));

	$tpl->newBlock('listado');
	$tpl->assign('mes', mes_escrito($_GET['mes']));
	$tpl->assign('anio', $_GET['anio']);

	$total_pollos = 0;
	$total_piernas = 0;
	$total_pescuezos = 0;
	$total_alas = 0;
	$total_comision = 0;
	foreach ($result as $reg) {
		$tpl->newBlock('fila');
		$tpl->assign('num_cia', $reg['num_cia']);
		$tpl->assign('nombre', $reg['nombre']);
		$tpl->assign('comision', $reg['efectivo'] != 0 ? number_format($reg['efectivo'] * 0.006, 2, '.', ',') : '&nbsp;');
		$total_comision += $reg['efectivo'] * 0.006;

		$sql = "SELECT codmp, sum(unidades) AS unidades FROM hoja_diaria_rost WHERE num_cia = $reg[num_cia] AND fecha BETWEEN '$fecha1' AND '$fecha2'";
		$sql .= " AND codmp IN (160, 700, 600, 352, 297, 363, 573, 434) GROUP BY codmp ORDER BY codmp";
		$unidades = $db->query($sql);

		$pollos = 0;
		$piernas = 0;
		$pescuezos = 0;
		$alas = 0;
		if ($unidades)
			foreach ($unidades as $u) {
				$pollos += in_array($u['codmp'], array(160, 700, 600, 573)) ? $u['unidades'] : 0;
				$piernas += $u['codmp'] == 352 ? $u['unidades'] : 0;
				$pescuezos += $u['codmp'] == 297 ? $u['unidades'] : 0;
				$alas += in_array($u['codmp'], array(363, 434)) ? $u['unidades'] : 0;
			}
		$tpl->assign('pollos', $pollos != 0 ? number_format($pollos) : '&nbsp;');
		$tpl->assign('piernas', $piernas != 0 ? number_format($piernas) : '&nbsp;');
		$tpl->assign('pescuezos', $pescuezos !=0 ? number_format($pescuezos) : '&nbsp;');
		$tpl->assign('alas', $alas != 0 ? number_format($alas) : '&nbsp;');

		$total_pollos += $pollos;
		$total_piernas += $piernas;
		$total_pescuezos += $pescuezos;
		$total_alas += $alas;
	}
	$tpl->assign('listado.pollos', $total_pollos != 0 ? number_format($total_pollos) : '&nbsp;');
	$tpl->assign('listado.piernas', $total_pollos != 0 ? number_format($total_piernas) : '&nbsp;');
	$tpl->assign('listado.pescuezos', $total_pollos != 0 ? number_format($total_pescuezos) : '&nbsp;');
	$tpl->assign('listado.alas', $total_pollos != 0 ? number_format($total_alas) : '&nbsp;');
	$tpl->assign('listado.comision', $total_comision != 0 ? number_format($total_comision, 2, '.', ',') : '&nbsp;');

	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');
$tpl->assign(date('n'), 'selected');
$tpl->assign('anio', date('Y'));

$result = $db->query('SELECT num_cia, nombre_corto AS nombre FROM catalogo_companias WHERE num_cia BETWEEN 301 AND 599 ORDER BY num_cia');
foreach ($result as $reg) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $reg['num_cia']);
	$tpl->assign('nombre', $reg['nombre']);
}

$result = $db->query('SELECT idadministrador AS id, nombre_administrador AS nombre FROM catalogo_administradores ORDER BY nombre');
foreach ($result as $reg) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $reg['id']);
	$tpl->assign('nombre', $reg['nombre']);
}

if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>
