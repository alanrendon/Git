<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
include './includes/class.auxinv.inc.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'generar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$conditions[] = 'c.fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			$conditions[] = 'c.importe >= 0';

			/*
			@ Intervalo de compañías
			*/
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '') {
				$cias = array();

				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$cias[] = $piece;
					}
				}

				if (count($cias) > 0) {
					$conditions[] = 'c.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (!in_array($_SESSION['iduser'], array(1, 4, 67, 2))) {
				if ($_SESSION['tipo_usuario'] == 2) {
					$conditions[] = 'c.num_cia BETWEEN 900 AND 998';
				}
				else {
					$conditions[] = '(con.iduser = ' . $_SESSION['iduser'] . ' OR aud.iduser = ' . $_SESSION['iduser'] . ')';
				}
			}

			if (!isset($_REQUEST['pagados'])) {
				$conditions[] = 'c.fecha_cancelacion IS NOT NULL';
			}

			if (!isset($_REQUEST['cancelados'])) {
				$conditions[] = 'c.fecha_cancelacion IS NULL';
			}

			if ($_REQUEST['banco'] > 0) {
				$conditions[] = 'c.cuenta = ' . $_REQUEST['banco'];
			}

			if ($_REQUEST['contador'] > 0) {
				$conditions[] = 'cc.idcontador = ' . $_REQUEST['contador'];
			}

			if ($_REQUEST['auditor'] > 0) {
				$conditions1[] = 'cc.idauditor = ' . $_REQUEST['auditor'];
			}

			$sql = '
				SELECT
					c.num_cia
						AS
							"#CIA",
					cc.nombre
						AS
							"COMPAÑIA",
					c.num_proveedor
						AS
							"#PRO",
					a_nombre
						AS
							"PROVEEDOR",
					CASE
						WHEN ec.cod_mov IS NOT NULL AND ec.cod_mov = 41 THEN
							\'TRANSFERENCIA\'
						WHEN ec.cod_mov IS NOT NULL AND ec.cod_mov <> 41 THEN
							\'CHEQUE\'
						ELSE
							NULL
					END
						AS
							"TIPO",
					CASE
						WHEN c.cuenta = 1 THEN
							\'BANORTE\'
						WHEN c.cuenta = 2 THEN
							\'SANTADER\'
						ELSE
							NULL
					END
						AS
							"BANCO",
					c.folio
						AS
							"FOLIO",
					c.fecha
						AS
							"FECHA",
					CASE
						WHEN fecha_cancelacion IS NOT NULL THEN
							0
						ELSE
							c.importe
					END
						AS
							"IMPORTE",
					fecha_con
						AS
							"COBRADO",
					fecha_cancelacion
						AS
							"CANCELADO",
					facturas
						AS
							"FACTURAS PAGADAS",
					CASE
						WHEN fecha_con IS NOT NULL THEN
							\'CONCILIADO\'
						WHEN fecha_cancelacion IS NOT NULL THEN
							\'CANCELADO\'
						ELSE
							\'PENDIENTE\'
					END
						AS
							"ESTADO"
				FROM
						cheques
							c
					LEFT JOIN
						estado_cuenta
							ec
								USING
									(
										num_cia,
										folio,
										cuenta,
										fecha
									)
					LEFT JOIN
						catalogo_companias
							cc
								ON
									(
										c.num_cia = cc.num_cia
									)
					LEFT JOIN
						catalogo_contadores
							con
								USING
									(
										idcontador
									)
					LEFT JOIN
						catalogo_auditores
							aud
								USING
									(
										idauditor
									)
					LEFT JOIN
						catalogo_proveedores
							cp
								ON
									(
										c.num_proveedor = cp.num_proveedor
									)
				WHERE
					' . implode(' AND ', $conditions) . '
				ORDER BY
					c.num_cia,
					c.cuenta,
					c.folio
			';

			$result = $db->query($sql);

			if ($result) {
				$data = '';

				$num_cia = NULL;

				foreach ($result as $r) {
					if ($num_cia != $r['#CIA']) {
						if ($num_cia != NULL) {
							$data .= '"","","","","","","","TOTAL","' . $total . '"' . "\n\n";
						}

						$num_cia = $r['#CIA'];

						$data .= '"' . implode('","', array_keys($result[0])) . '"' . "\n";

						$total = 0;
					}

					$data .= '"' . implode('","', $r) . '"' . "\n";

					$total += $r['IMPORTE'];
				}

				if ($num_cia != NULL) {
					$data .= '"","","","","","","","TOTAl","' . $total . '"' . "\n";
				}

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=cheques.csv');

				echo $data;
			}
			else {
				header('location: ArchivoChequesContadores.php');
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ArchivoChequesContadores.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));
$tpl->assign(date('n'), ' selected');

$sql = '
	SELECT
		idcontador
			AS
				id,
		nombre_contador
			AS
				nombre
	FROM
		catalogo_contadores
	ORDER BY
		nombre
';
$contadores = $db->query($sql);

foreach ($contadores as $c) {
	$tpl->newBlock('contador');
	$tpl->assign('id', $c['id']);
	$tpl->assign('nombre', $c['nombre']);
}

$sql = '
	SELECT
		idauditor
			AS
				id,
		nombre_auditor
			AS
				nombre
	FROM
		catalogo_auditores
	ORDER BY
		nombre
';
$auditores = $db->query($sql);

foreach ($auditores as $a) {
	$tpl->newBlock('auditor');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$tpl->printToScreen();
?>
