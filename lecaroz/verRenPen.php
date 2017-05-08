<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// Conectarse a la base de datos
$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

if (!in_array($_SESSION['iduser'], array(25))) die;

$sql = "SELECT ca.id, ca.num_local, nombre_local, ca.num_cia, cc.nombre_corto, giro, ca.contacto, ca.telefono FROM catalogo_arrendatarios ca LEFT JOIN catalogo_locales cl ON (cl.id = ca.num_local) LEFT JOIN catalogo_companias cc ON (cc.num_cia = ca.num_cia) WHERE ca.status = 1 AND ca.bloque = 2 ORDER BY ca.num_cia";
$result = $db->query($sql);

if (!$result) die;

$fecha1 = date('Y') == 2008 ? '01/10/2007' : date('01/m/Y', mktime(0, 0, 0, date('d') < 18 ? date('n') - 1 : date('n'), 1, date('Y')));
$fecha2 = date('d/m/Y', mktime(0, 0, 0, date('d') < 18 ? date('n') - 1 : date('n'), 1, date('Y')));
$len = date('n', mktime(0, 0, 0, date('d') < 18 ? date('n') - 1 : date('n'), 1, date('Y'))) + (date('Y') == 2008 ? 3 : 0);

$meses = array();
for ($i = 0; $i < $len; $i++) {
	$meses[$i]['mes'] = date('n', mktime(0, 0, 0, date('n', mktime(0, 0, 0, date('d') < 18 ? date('n') - 1 : date('n'), 1, date('Y'))) - $i, 1, date('Y')));
	$meses[$i]['anio'] = date('Y', mktime(0, 0, 0, date('n', mktime(0, 0, 0, date('d') < 18 ? date('n') - 1 : date('n'), 1, date('Y'))) - $i, 1, date('Y')));
}

function buscar_mes($mes, $anio) {
	global $ren, $est;
	
	if (!$ren && !$est) return FALSE;
	
	if ($ren)
		foreach ($ren as $r)
			if ($r['anio'] == $anio && $r['mes'] == $mes)
				return TRUE;
	
	if ($est)
		foreach ($est as $r)
			if ($r['anio'] == $anio && $r['mes'] == $mes)
				return TRUE;
	
	return FALSE;
}

$pen = array();
$cont = 0;
foreach ($result as $reg) {
	$sql = "SELECT extract(month from fecha_renta) AS mes, extract(year from fecha_renta) AS anio FROM estado_cuenta WHERE local = $reg[id] AND cod_mov = 2 AND fecha_renta BETWEEN '$fecha1' AND '$fecha2' ORDER BY fecha_renta";
	$ren = $db->query($sql);
	
	$sql = "SELECT mes, anio FROM estatus_locales WHERE local = $reg[id] AND anio IN (2007, 2008)";
	$est = $db->query($sql);
	
	foreach ($meses as $fecha)
		if (!buscar_mes($fecha['mes'], $fecha['anio'])) {
			$pen[$cont]['local'] = $reg['num_local'];
			$pen[$cont]['nombre'] = $reg['nombre_local'];
			$pen[$cont]['periodo'] = mes_escrito($fecha['mes'], TRUE) . "/$fecha[anio]";
			$pen[$cont]['contacto'] = $reg['contacto'];
			$pen[$cont]['telefono'] = $reg['telefono'];
			$cont++;
		}
}

if ($cont == 0) die;

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/verRenPen.tpl" );
$tpl->prepare();

foreach ($pen as $pen) {
	$tpl->newBlock('fila');
	$tpl->assign('local', $pen['local']);
	$tpl->assign('nombre', $pen['nombre']);
	$tpl->assign('periodo', $pen['periodo']);
	$tpl->assign('telefono', $pen['telefono']);
}

$tpl->printToScreen();
//$tpl->getOutputContent();
?>