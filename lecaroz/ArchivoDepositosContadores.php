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

			$conditions[] = 'ec.fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'';
			$conditions[] = 'cod_mov IN (1, 16, 44, 99)';

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
					$conditions[] = 'ec.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (!in_array($_SESSION['iduser'], array(1, 4, 67, 2))) {
				if ($_SESSION['tipo_usuario'] == 2) {
					$conditions[] = 'ec.num_cia BETWEEN 900 AND 998';
				}
				else {
					$conditions[] = '(con.iduser = ' . $_SESSION['iduser'] . ' OR aud.iduser = ' . $_SESSION['iduser'] . ')';
				}
			}

			if ($_REQUEST['banco'] > 0) {
				$conditions[] = 'ec.cuenta = ' . $_REQUEST['banco'];
			}

			if ($_REQUEST['contador'] > 0) {
				$conditions[] = 'cc.idcontador = ' . $_REQUEST['contador'];
			}

			if ($_REQUEST['auditor'] > 0) {
				$conditions1[] = 'cc.idauditor = ' . $_REQUEST['auditor'];
			}

			$sql = '
				SELECT
					num_cia
						AS
							"#",
					nombre
						AS
							"COMPAÑIA",
					fecha
						AS
							"FECHA",
					fecha_con
						AS
							"CONCILIADO",
					CASE
						WHEN cuenta = 1 THEN
							\'BANORTE\'
						WHEN cuenta = 2 THEN
							\'SANTANDER\'
						ELSE
							NULL
					END
						AS
							"BANCO",
					concepto
						AS
							"CONCEPTO",
					CASE
						WHEN num_cia BETWEEN 301 AND 599 OR num_cia >= 900 THEN
							CASE
								WHEN fecha < \'2010/01/01\' THEN
									round(importe::numeric / 1.15, 2)
								ELSE
									round(importe::numeric / 1.16, 2)
							END
						ELSE
							importe
					END
						AS
							"VENTA",
					CASE
						WHEN num_cia BETWEEN 301 AND 599 OR num_cia >= 900 THEN
							CASE
								WHEN fecha < \'2010/01/01\' THEN
									importe - round(importe::numeric / 1.15, 2)
								ELSE
									importe - round(importe::numeric / 1.16, 2)
							END
						ELSE
							NULL
					END
						AS
							"I.V.A.",
					importe
						AS
							"IMPORTE"
				FROM
						estado_cuenta
							ec
					LEFT JOIN
						catalogo_companias
							cc
								USING
									(
										num_cia
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
				WHERE
					' . implode(' AND ', $conditions) . '
				ORDER BY
					ec.num_cia,
					ec.fecha
			';

			$result = $db->query($sql);

			if ($result) {
				$data = '';

				$num_cia = NULL;

				foreach ($result as $r) {
					if ($num_cia != $r['#']) {
						if ($num_cia != NULL) {
							$data .= '"","","","","","TOTALES","' . $venta . '","' . $iva . '","' . $total . '"' . "\n\n";
						}

						$num_cia = $r['#'];

						$data .= '"' . implode('","', array_keys($result[0])) . '"' . "\n";

						$venta = 0;
						$iva = 0;
						$total = 0;
					}

					$data .= '"' . implode('","', $r) . '"' . "\n";

					$venta += $r['VENTA'];
					$iva += $r['I.V.A.'];
					$total += $r['IMPORTE'];
				}

				if ($num_cia != NULL) {
					$data .= '"","","","","","TOTALES","' . $venta . '","' . $iva . '","' . $total . '"' . "\n\n";
				}

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=depositos.csv');

				echo $data;
			}
			else {
				header('location: ArchivoDepositosContadores.php');
			}
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ArchivoDepositosContadores.tpl');
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
