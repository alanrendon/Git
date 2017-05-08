<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/cheques.inc.php';

$db = new DBclass($dsn, "autocommit=yes");

$conditions[] = 'codgastos IN (134, 161)';
$conditions[] = 'fecha_cancelacion IS NULL';
$conditions[] = 'importe > 0';
if (isset($_REQUEST['id'])) {
	$conditions[] = 'c.id IN (' . implode(', ', $_REQUEST['id']) . ')';
}
else if (isset($_REQUEST['idstatus'])) {
	$conditions[] = 'idstatus = ' . $_REQUEST['idstatus'];
	//$conditions[] = 'c.cuenta = 2';
	
	if (isset($_REQUEST['num_cia'])) {
		$conditions[] = 'num_cia >= ' . $_REQUEST['num_cia'];
	}
}
else if (isset($_REQUEST['num_cia']) && isset($_REQUEST['cuenta']) && isset($_REQUEST['folio'])) {
	$conditions[] = 'num_cia = ' . $_REQUEST['num_cia'];
	$conditions[] = 'c.cuenta = ' . $_REQUEST['cuenta'];
	$conditions[] = 'folio = ' . $_REQUEST['folio'];
}
else if (isset($_REQUEST['fecha1']) && isset($_REQUEST['fecha2'])) {
	$conditions[] = 'c.fecha BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
}
else {
	die('NO HAY ID DE IMPRESION');
}

$sql = '
	SELECT
		c.num_cia,
		cc.nombre
			AS nombre_cia,
		CASE
			WHEN c.cuenta = 1 THEN
				clabe_cuenta
			WHEN c.cuenta = 2 THEN
				clabe_cuenta2
		END
			AS cuenta,
		a_nombre
			AS nombre,
		importe,
		c.cuenta
			AS banco,
		folio,
		fecha + interval \'5 days\'
			AS fecha
	FROM
		cheques c
		LEFT JOIN catalogo_proveedores cp
			USING (num_proveedor)
		LEFT JOIN catalogo_companias cc
			USING (num_cia)
	WHERE
		' . implode(' AND ', $conditions) . '
	ORDER BY
		num_cia,
		c.cuenta,
		folio
';
$result = $db->query($sql);

if (!$result) {
	die('NO HAY RESULTADOS');
}

$banorte = array();
$santander = array();

foreach ($result as $rec) {
	if ($rec['banco'] == 1) {
		$banorte[] = $rec;
	}
	else if ($rec['banco'] == 2) {
		$santander[] = $rec;
	}
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower('plantillas/ban/CartaSolicitudRemesaV2.tpl');
$tpl->prepare();

if (count($banorte) > 0) {
	$filas_x_hoja = 12;
	$filas = $filas_x_hoja;
	
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : $banorte[0]['fecha'], $fecha);
	
	foreach ($banorte as $r) {
		if ($filas >= $filas_x_hoja) {
			$filas = 0;
			
			$tpl->newBlock('cartaBanorte');
			$tpl->assign('num_cia', $r['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($r['nombre_cia']));
			$tpl->assign('dia', $fecha[1]);
			$tpl->assign('mes', mes_escrito($fecha[2]));
			$tpl->assign('anio', $fecha[3]);
			$tpl->assign('firma', utf8_encode($_REQUEST['firma']));
			$tpl->assign('salto', '<br style="page-break-after:always;" /><br />');
			
			$total = 0;
		}
		
		$tpl->newBlock('cuentaBanorte');
		$tpl->assign('cuenta', $r['cuenta']);
		$tpl->assign('nombre_cia', utf8_encode($r['nombre_cia']));
		$tpl->assign('folio', $r['folio']);
		$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
		
		$total += $r['importe'];
		
		$tpl->assign('cartaBanorte.total', number_format($total, 2, '.', ','));
		
		$filas++;
	}
}

if (count($santander) > 0) {
	$filas_x_hoja = 12;
	$filas = $filas_x_hoja;
	
	ereg('([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})', isset($_REQUEST['fecha']) ? $_REQUEST['fecha'] : $santander[0]['fecha'], $fecha);
	
	foreach ($santander as $r) {
		if ($filas >= $filas_x_hoja) {
			$filas = 0;
			
			$tpl->newBlock('cartaSantander');
			$tpl->assign('num_cia', $r['num_cia']);
			$tpl->assign('nombre_cia', utf8_encode($r['nombre_cia']));
			$tpl->assign('dia', $fecha[1]);
			$tpl->assign('mes', mes_escrito($fecha[2]));
			$tpl->assign('anio', $fecha[3]);
			$tpl->assign('firma', utf8_encode($_REQUEST['firma']));
			$tpl->assign('salto', '<br style="page-break-after:always;" /><br />');
			
			$total = 0;
		}
		
		$tpl->newBlock('cuentaSantander');
		$tpl->assign('cuenta', $r['cuenta']);
		$tpl->assign('nombre_cia', utf8_encode($r['nombre_cia']));
		$tpl->assign('folio', $r['folio']);
		$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
		
		$total += $r['importe'];
		
		$tpl->assign('cartaSantander.total', number_format($total, 2, '.', ','));
		
		$filas++;
	}
}

$tpl->printToScreen();
$db->desconectar();
?>