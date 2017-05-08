<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
}

$_meses = array(
	1  => 'Enero',
	2  => 'Febrero',
	3  => 'Marzo',
	4  => 'Abril',
	5  => 'Mayo',
	6  => 'Junio',
	7  => 'Julio',
	8  => 'Agosto',
	9  => 'Septiembre',
	10 => 'Octubre',
	11 => 'Noviembre',
	12 => 'Diciembre'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {

		case 'obtener_cia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';

			$result = $db->query($sql);

			if ($result) {
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'reimprimir':
			$ids = array();
			$not_found = array();

			// Recorrer la lista enviada
			foreach ($_REQUEST['num_cia'] as $i => $num_cia) {
				if ($num_cia > 0 && $_REQUEST['folio'][$i] > 0 && $_REQUEST['fecha'][$i] != '') {
					if ($id = $db->query('
						SELECT
							id
						FROM
							cheques
						WHERE
							num_cia = ' . $num_cia . '
							AND cuenta = ' . $_REQUEST['banco'][$i] . '
							AND folio = ' . $_REQUEST['folio'][$i] . '
							AND fecha = \'' . $_REQUEST['fecha'][$i] . '\'
							AND fecha_cancelacion IS NULL
							AND importe > 0
							/* [07-Nov-2014] Solo tomar en cuenta del 2014 a la fecha para evitar encontrar duplicados por folios reiniciados */
							/* [20-Nov-2017] Removida condición ya que ahora se envia la fecha de emisión de pago */
							/*AND fecha >= \'01/01/2014\'*/
					')) {
						$ids[] = $id[0]['id'];
					} else {
						$not_found[] = array(
							'num_cia' => $num_cia,
							'banco'   => $_REQUEST['banco'][$i],
							'folio'   => $_REQUEST['folio'][$i],
							'fecha'   => $_REQUEST['fecha'][$i]
						);
					}
				}
			}

			if ($ids || $not_found) {
				$tpl = new TemplatePower('plantillas/ban/ReimpresionPolizasStatus.tpl');
				$tpl->prepare();

				if ($ids) {
					$sql = '
						SELECT
							num_cia,
							nombre_corto
								AS nombre_cia,
							CASE
								WHEN cuenta = 1 THEN
									\'<img src="/lecaroz/imagenes/Banorte16x16.png" width="16" height="16" /> BANORTE\'
								WHEN cuenta = 2 THEN
									\'<img src="/lecaroz/imagenes/Santander16x16.png" width="16" height="16" /> SANTANDER\'
							END
								AS banco,
							folio,
							fecha,
							a_nombre
								AS beneficiario,
							importe
						FROM
							cheques c
							LEFT JOIN catalogo_companias cc
								USING (num_cia)
						WHERE
							id IN (' . implode(', ', $ids) . ')
						ORDER BY
							num_cia,
							cuenta,
							folio
					';

					$result = $db->query($sql);

					$tpl->newBlock('reimpresos');

					$tpl->assign('polizas', count($ids));

					foreach ($result as $rec) {
						$tpl->newBlock('reimpreso');
						$tpl->assign('num_cia', $rec['num_cia']);
						$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
						$tpl->assign('banco', utf8_encode($rec['banco']));
						$tpl->assign('folio', $rec['folio']);
						$tpl->assign('fecha', $rec['fecha']);
						$tpl->assign('beneficiario', utf8_encode($rec['beneficiario']));
						$tpl->assign('importe', number_format($rec['importe'], 2));
					}

					$sql = '
						UPDATE
							cheques
						SET
							imp = FALSE
						WHERE
							id IN (' . implode(', ', $ids) . ')
					';

					$db->query($sql);
				}

				if ($not_found) {
					$tpl->newBlock('not_found');

					$tpl->assign('polizas', count($not_found));

					foreach ($not_found as $nf) {
						$tpl->newBlock('nf');
						$tpl->assign('num_cia', $nf['num_cia']);

						$nombre = $db->query('
							SELECT
								nombre_corto
							FROM
								catalogo_companias
							WHERE
								num_cia = ' . $nf['num_cia'] . '
						');

						$tpl->assign('nombre_cia', utf8_encode($nombre[0]['nombre_corto']));

						$tpl->assign('banco', $nf['banco'] == 1 ? '<img src="/lecaroz/imagenes/Banorte16x16.png" width="16" height="16" /> BANORTE' : '<img src="/lecaroz/imagenes/Santander16x16.png" width="16" height="16" /> BANORTE');
						$tpl->assign('folio', $nf['folio']);
						$tpl->assign('fecha', $nf['fecha']);
					}
				}

				echo $tpl->getOutputContent();
			}

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/ban/ReimpresionPolizas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
