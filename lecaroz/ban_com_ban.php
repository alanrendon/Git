<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$descripcion_error[1] = 'NO HAY RESULTADOS';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] == 1) die("Pantalla no disponible...");

$users = array(28, 29, 30, 31);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_com_ban.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['num_cia'])) {
	$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'], 1, $_GET['anio']));
	$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	$cat = $_GET['cuenta'] == 1 ? 'bancos' : 'santander';
	
	/*$sql = "SELECT num_cia, fecha, concepto, cod_mov, descripcion, ec.tipo_mov, importe FROM estado_cuenta AS ec LEFT JOIN catalogo_mov_$cat USING (cod_mov)";
	$sql .= " WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND entra_bal = 'TRUE' AND cuenta = $_GET[cuenta]";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= " GROUP BY num_cia, fecha, concepto, cod_mov, descripcion, ec.tipo_mov, importe ORDER BY num_cia, fecha";*/
	$sql = "SELECT num_cia, fecha, concepto, cod_mov, (SELECT descripcion FROM catalogo_mov_$cat WHERE cod_mov = ec.cod_mov LIMIT 1) AS desc, ec.tipo_mov, importe FROM estado_cuenta AS";
	$sql .= " ec WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND cuenta = $_GET[cuenta] AND cod_mov IN (SELECT cod_mov FROM catalogo_mov_$cat WHERE entra_bal = 'TRUE'";
	$sql .= $_GET['tipo'] > 0 ? " AND tipo_mov = '" . ($_GET['tipo'] == 1 ? 'FALSE' : 'TRUE') . "'" : '';
	$sql .= " GROUP BY cod_mov)";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : '';
	$sql .= " ORDER BY num_cia, fecha";
	$result = $db->query($sql);
	
	if (!$result) {
		header('location: ./ban_com_ban.php?codigo_error=1');
		die;
	}
	
	$tpl->newBlock('listado');
	$tpl->assign('mes', mes_escrito($_GET['mes']));
	$tpl->assign('anio', $_GET['anio']);
	
	$num_cia = NULL;
	$abonos = 0;
	$cargos = 0;
	$total = 0;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$tpl->newBlock('cia');
			$nombre = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $reg[num_cia]");
			$tpl->assign('num_cia', $reg['num_cia']);
			$tpl->assign('nombre', $nombre[0]['nombre_corto']);
			$abono = 0;
			$cargo = 0;
			$dif = 0;
		}
		$tpl->newBlock('mov');
		$tpl->assign('fecha', $reg['fecha']);
		$tpl->assign('concepto', trim($reg['concepto']) != '' ? trim($reg['concepto']) : '&nbsp;');
		$tpl->assign('cod_mov', $reg['cod_mov']);
		$tpl->assign('desc', $reg['desc']);
		$tpl->assign('abono', $reg['tipo_mov'] == 'f' ? number_format($reg['importe'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('cargo', $reg['tipo_mov'] == 't' ? number_format($reg['importe'], 2, '.', ',') : '&nbsp;');
		$abono += $reg['tipo_mov'] == 'f' ? $reg['importe'] : 0;
		$cargo += $reg['tipo_mov'] == 't' ? $reg['importe'] : 0;
		$abonos += $reg['tipo_mov'] == 'f' ? $reg['importe'] : 0;
		$cargos += $reg['tipo_mov'] == 't' ? $reg['importe'] : 0;
		$tpl->assign('cia.abono', number_format($abono, 2, '.', ','));
		$tpl->assign('cia.cargo', number_format($cargo, 2, '.', ','));
		$dif += $reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe'];
		$total += $reg['tipo_mov'] == 'f' ? $reg['importe'] : -$reg['importe'];
		$tpl->assign('cia.total', number_format(abs($dif), 2, '.', ','));
		$tpl->assign('cia.color_total', $dif > 0 ? '0000CC' : 'CC0000');
	}
	$tpl->assign('listado.abonos', number_format($abonos, 2, '.', ','));
	$tpl->assign('listado.cargos', number_format($cargos, 2, '.', ','));
	$tpl->assign('listado.total', number_format(abs($total), 2, '.', ','));
	$tpl->assign('listado.color', $total > 0 ? '0000CC' : 'CC0000');
	$tpl->printToScreen();
	die;
}

$tpl->newBlock('datos');
$tpl->assign(date('n'), ' selected');
$tpl->assign('anio', date('Y'));

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia";
$cias = $db->query($sql);
foreach ($cias as $cia) {
	$tpl->newBlock('c');
	$tpl->assign('num_cia', $cia['num_cia']);
	$tpl->assign('nombre', $cia['nombre_corto']);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
}

$tpl->printToScreen();
?>