<?php
include './includes/class.db.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');

$sql = '
	SELECT
		fp.num_cia,
		fecha_mov,
		num_fact,
		CASE
			WHEN fp.cuenta = 1 THEN
				\'BANORTE\'
			ELSE
				\'SANTANDER\'
		END
			AS
				banco,
		CASE
			WHEN ec.cod_mov = 5 THEN
				\'CHEQUE\'
			ELSE
				\'TRANSFERENCIA\'
		END
			AS
				movimiento,
		folio_cheque,
		fecha_con,
		total,
		facturas
	FROM
			facturas_pagadas
				fp
		LEFT JOIN
			cheques
				c
					ON
						(
								c.num_cia = fp.num_cia
							AND
								c.folio = fp.folio_cheque
							AND
								c.cuenta = fp.cuenta
						)
		LEFT JOIN
			estado_cuenta
				ec
					ON
						(
								ec.num_cia = c.num_cia
							AND
								ec.folio = c.folio
							AND
								ec.cuenta = c.cuenta
						)
	WHERE
			fecha_mov >= \'2008/01/01\'
		AND
			fp.num_proveedor = 7
		AND
			fecha_con IS NOT NULL
	ORDER BY
		fp.num_cia,
		num_fact
';
$result = $db->query($sql);

$tpl = new TemplatePower('plantillas/fac/carta_nutriseleccion.tpl');
$tpl->prepare();

$num_cia = NULL;
foreach ($result as $r) {
	if ($num_cia != $r['num_cia']) {
		if ($num_cia != NULL) {
			$tpl->assign('carta.salto', '<br style="page-break-after:always;">');
		}
		
		$num_cia = $r['num_cia'];
		
		$nombre_cia = $db->query('SELECT nombre FROM catalogo_companias WHERE num_cia = ' . $r['num_cia']);
		
		$tpl->newBlock('carta');
		$tpl->assign('dia', date('d'));
		$tpl->assign('mes', mes_escrito(date('n')));
		$tpl->assign('anio', date('Y'));
		$tpl->assign('nombre_cia', $nombre_cia[0]['nombre']);
	}
	$tpl->newBlock('fila');
	$tpl->assign('fecha_mov', $r['fecha_mov']);
	$tpl->assign('num_fact', $r['num_fact']);
	$tpl->assign('banco', $r['banco']);
	$tpl->assign('movimiento', $r['movimiento']);
	$tpl->assign('folio', $r['folio_cheque']);
	$tpl->assign('fecha_con', $r['fecha_con']);
	$tpl->assign('importe', number_format($r['total'], 2, '.', ','));
	$tpl->assign('facturas', $r['facturas']);
}

$tpl->printToScreen();
?>