<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

$users = array(28, 29, 30, 31);

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_mpc_con_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Tipo de listado -------------------------------------------------------
if (!isset($_GET['fecha'])) {
	$tpl->newBlock("datos");
	
	$tpl->assign("fecha",date("d/m/Y", mktime(0, 0, 0, date("m"), date("d") - 1, date("Y"))));
	
	if ($_SESSION['tipo_usuario'] == 2) {
		$tpl->assign("cod1", 1);
		$tpl->assign("cod2", 44);
	}
	else {
		$tpl->assign("cod1", 1);
		$tpl->assign("cod2", 2);
		$tpl->assign("cod3", 16);
		$tpl->assign("cod4", 99);
		$tpl->assign("cod5", 29);
		$tpl->assign("cod6", 13);
	}
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	$tpl->printToScreen();
	die();
}

$cuenta = $_GET['cuenta'];
$clabe_cuenta = $cuenta == 1 ? "clabe_cuenta" : "clabe_cuenta2";

$cod_tmp = array();
foreach ($_GET['cod_mov'] as $cod)
	if ($cod > 0)
		$cod_tmp[] = $cod;

$cod_car_tmp = array();
foreach ($_GET['cod_mov_car'] as $cod)
	if ($cod > 0)
		$cod_car_tmp[] = $cod;

$cod_all = array();
foreach ($_GET['cod_mov_all'] as $cod)
	if ($cod > 0)
		$cod_all_tmp[] = $cod;

$condiciones[] = 'num_cia BETWEEN ' . ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899');
$condiciones[] = 'cuenta = ' . $cuenta;
$condiciones[] = 'fecha_con IS NULL';

$cias_omt = array();
foreach ($_GET['num_cia_omt'] as $c) {
	if ($c > 0) {
		$cias_omt[] = $c;
	}
}

if ($_GET['num_cia1'] > 0) {
	if ($_GET['num_cia2'] > 0) {
		$condiciones[] = 'num_cia BETWEEN ' . $_REQUEST['num_cia1'] . ' AND ' . $_GET['num_cia2'];
	}
	else {
		$condiciones[] = 'num_cia = ' . $_GET['num_cia1'];
	}
}

if (count($cias_omt) > 0) {
	$condiciones[] = 'num_cia NOT IN (' . implode(', ', $cias_omt) . ')';
}

if ($_GET['num_pro'] > 0) {
	$condiciones[] = 'cheques.num_proveedor = ' . $_GET['num_pro'];
}

if ($_GET['fecha'] != '') {
	$condiciones[] = 'estado_cuenta.fecha <= \'' . $_GET['fecha'] . '\'';
}

if ($_GET['opc'] > 0) {
	$condiciones[] = 'tipo_mov = \'' . ($_GET['opc'] == 1 ? 'FALSE' : 'TRUE') . '\'';
}

if ($_GET['opc'] == 0 && count($cod_all_tmp) > 0) {
	$condiciones[] = 'cod_mov IN (' . implode(', ', $cod_all_tmp) . ')';
}

if ($_GET['opc'] == 1 && count($cod_tmp) > 0) {
	$condiciones[] = 'cod_mov IN (' . implode(', ', $cod_tmp) . ')';
}

if ($_GET['opc'] == 2 && count($cod_car_tmp) > 0) {
	$condiciones[] = 'cod_mov IN (' . implode(', ', $cod_car_tmp) . ')';
}

$sql = '
	SELECT
		num_cia,
		' . $clabe_cuenta . '
			AS
				cuenta,
		nombre
			AS
				nombre_cia,
		estado_cuenta.fecha,
		tipo_mov,
		estado_cuenta.importe,
		estado_cuenta.cod_mov,
		folio,
		estado_cuenta.concepto,
		cod_mov
	FROM
			estado_cuenta
		LEFT JOIN
			catalogo_companias
				USING
					(num_cia)';
$sql .= $_GET['num_pro'] > 0 ? ' LEFT JOIN cheques USING (num_cia, folio, cuenta)' : '';

$sql .= '
	WHERE
		' . implode(' AND ', $condiciones);

$sql .= '
	ORDER BY
		num_cia,
		fecha,
		tipo_mov ASC,
		importe
';
$result = $db->query($sql);

if (!$result) {
	header("location: ./ban_mpc_con_v2.php?codigo_error=1");
	die;
}

// Crear bloque para el listado
if ($_GET['tipo'] == 1) {
	$tpl->newBlock("listado");
	$tpl->assign('banco', $cuenta == 1 ? 'BANORTE' : 'SANTANDER');
	$tpl->assign('fecha', date('d/m/Y H:i'));
}

$cat_mov = $cuenta == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";
$codigos = $db->query("SELECT cod_mov, descripcion FROM $cat_mov GROUP BY cod_mov, descripcion ORDER BY cod_mov");

function buscar($cod_mov) {
	global $codigos;
	
	foreach ($codigos as $cod)
		if ($cod_mov == $cod['cod_mov'])
			return $cod['descripcion'];
	
	return FALSE;
}

// Iniciar ciclo de recorrido de movimientos
$num_cia = NULL;
$data = "Cia,Nombre,Cuenta,Banco,Fecha,Importe,Folio,Beneficiario,Concepto\n";
for ($i = 0; $i < count($result); $i++) {
	if ($num_cia != $result[$i]['num_cia'] && $_GET['tipo'] == 1) {
		$num_cia = $result[$i]['num_cia'];
				
		$tpl->newBlock("cia");
		$tpl->assign("num_cia", $result[$i]['num_cia']);
		$tpl->assign("nombre_cia", $result[$i]['nombre_cia']);
		$tpl->assign("cuenta", $result[$i]['cuenta']);
		
		$total_depositos = 0;
		$total_retiros = 0;
	}
	if ($_GET['tipo'] == 1) {
		// Crear fila de movimiento
		$tpl->newBlock("fila");
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("deposito", $result[$i]['tipo_mov'] == "f" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("retiro", $result[$i]['tipo_mov'] == "t" ? number_format($result[$i]['importe'], 2, ".", ",") : "&nbsp;");
		$tpl->assign("folio", $result[$i]['folio'] > 0 ? ($result[$i]['cod_mov'] == 41 ? "<span style=\"color: #009933;\">" : "<span>") . $result[$i]['folio'] . "</span>" : "&nbsp;");
		if ($result[$i]['folio'] > 0) {
			$beneficiario = $db->query("SELECT a_nombre FROM cheques WHERE cuenta = $cuenta AND num_cia = $num_cia AND folio = {$result[$i]['folio']}");
			$tpl->assign("beneficiario", $beneficiario ? $beneficiario[0]['a_nombre'] : "&nbsp;");
		}
		else
			$tpl->assign("beneficiario", "&nbsp;");
		$tpl->assign("concepto",$result[$i]['concepto']);
		
		$total_depositos += $result[$i]['tipo_mov'] == "f" ? $result[$i]['importe'] : 0;
		$total_retiros += $result[$i]['tipo_mov'] == "t" ? $result[$i]['importe'] : 0;
		
		$tpl->assign("cia.total_depositos", number_format($total_depositos, 2, ".", ","));
		$tpl->assign("cia.total_retiros", number_format($total_retiros, 2, ".", ","));
	}
	else {
		if ($result[$i]['folio'] > 0) {
			$tmp = $db->query("SELECT a_nombre FROM cheques WHERE cuenta = $cuenta AND num_cia = {$result[$i]['num_cia']} AND folio = {$result[$i]['folio']}");
			$beneficiario = $tmp ? $tmp[0]['a_nombre'] : '';
		}
		else {
			$beneficiario = '';
		}
		$data .= "\"{$result[$i]['num_cia']}\",\"{$result[$i]['nombre_cia']}\",\"{$result[$i]['cuenta']}\",\"" . ($cuenta == 1 ? 'BANORTE' : 'SANTANDER') . "\",\"{$result[$i]['fecha']}\",\"" . ($result[$i]['tipo_mov'] == 't' ? -$result[$i]['importe'] : $result[$i]['importe']) . "\",\"{$result[$i]['folio']}\",\"$beneficiario\",\"{$result[$i]['concepto']}\"\n";
	}
}

if ($_GET['tipo'] == 1)
	$tpl->printToScreen();
else {
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=pendientes.csv");
	
	echo $data;
}
?>