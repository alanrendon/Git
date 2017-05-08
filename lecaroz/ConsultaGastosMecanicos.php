<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'getCia':
			$sql = '
				SELECT
					nombre_corto
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_REQUEST['num_cia'] . '
			';
			$result = $db->query($sql);
			
			echo $result[0]['nombre_corto'];
		break;
		
		case 'getQuery':
			$conditions1 = array();
			$conditions2 = array();
			
			$conditions1[] = 'c.fecha ' . ($_REQUEST['fecha2'] != '' ? 'BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'' : '= \'' . $_REQUEST['fecha1'] . '\'');
			$conditions2[] = 'fecha ' . ($_REQUEST['fecha2'] != '' ? 'BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'' : '= \'' . $_REQUEST['fecha1'] . '\'');
			
			if ($_REQUEST['num_cia'] > 0) {
				$conditions1[] = 'num_cia = ' . $_REQUEST['num_cia'];
				$conditions2[] = 'num_cia = ' . $_REQUEST['num_cia'];
			}
			if ($_REQUEST['admin'] > 0) {
				$conditions1[] = 'idadministrador = ' . $_REQUEST['admin'];
				$conditions2[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			$sql = '
					SELECT
						nombre_administrador
							AS
								admin,
						num_cia,
						nombre_corto
							AS
								nombre_cia,
						c.num_proveedor
							AS
								num_pro,
						a_nombre
							AS
								nombre_pro,
						c.fecha,
						fecha_con
							AS
								conciliado,
						CASE
							WHEN cuenta = 1 THEN
								\'BANORTE\'
							ELSE
								\'SANTADER\'
						END
							AS
								banco,
						folio,
						c.concepto,
						facturas,
						c.importe,
						0
							AS
								tipo
					FROM
							cheques
								c
						LEFT JOIN
							estado_cuenta
								ec
									USING
										(
											num_cia,
											cuenta,
											folio
										)
						LEFT JOIN
							catalogo_companias
								cc
									USING
										(
											num_cia
										)
						LEFT JOIN
							catalogo_administradores
								ca
									USING
										(
											idadministrador
										)
					WHERE
							codgastos
								IN
									(
										45,
										46,
										34,
										124,
										83,
										150
									)
						AND
							' . implode(' AND ', $conditions1) . '
				UNION
					SELECT
						nombre_administrador
							AS
								admin,
						num_cia,
						nombre_corto
							AS
								nombre_cia,
						0
							AS
								num_pro,
						\'\'
							AS
								nombre_pro,
						fecha,
						NULL
							AS
								conciliado,
						\'CAJA\'
							AS
								banco,
						0
							AS
								folio,
						\'REFACCIONES TALLERES\'
							AS
								concepto,
						\'\'
							AS
								facturas,
						importe,
						1
							AS
								tipo
					FROM
							gastos_caja
								gc
						LEFT JOIN
							catalogo_companias
								cc
									USING
										(
											num_cia
										)
						LEFT JOIN
							catalogo_administradores
								ca
									USING
										(
											idadministrador
										)
					WHERE
							cod_gastos = 84
						AND
							' . implode(' AND ', $conditions2) . '
				ORDER BY
					' . ($_REQUEST['admin'] != 0 ? 'admin,' : '') . '
					num_cia,
					tipo,
					fecha,
					banco,
					folio
			';
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/fac/GastosMecanicos.tpl');
			$tpl->prepare();
			
			if ($result) {
				$tpl->newBlock('result');
				$tpl->assign('fecha1', $_REQUEST['fecha1']);
				$tpl->assign('fecha2', $_REQUEST['fecha2'] != '' ? $_REQUEST['fecha2'] : $_REQUEST['fecha1']);
				
				$num_cia = NULL;
				$admin = NULL;
				$total_general = 0;
				foreach ($result as $r) {
					if ($_REQUEST['admin'] != 0) {
						if ($admin != NULL && $admin != $r['admin']) {
							$tpl->newBlock('total');
							$tpl->assign('total', number_format($total_general, 2, '.', ','));
							
							$tpl->assign('salto', '<br style="page-break-after:always;" />');
							
							$tpl->newBlock('result');
							$tpl->assign('fecha1', $_REQUEST['fecha1']);
							$tpl->assign('fecha2', $_REQUEST['fecha2'] != '' ? $_REQUEST['fecha2'] : $_REQUEST['fecha1']);
							$tpl->assign('admin', $r['admin'] . '<br />');
							
							$total_general = 0;
						}
						else if ($admin == NULL) {
							$tpl->assign('admin', $r['admin'] . '<br />');
						}
					}
					
					if ($num_cia != $r['num_cia']) {
						$num_cia = $r['num_cia'];
						$admin = $r['admin'];
						
						$tpl->newBlock('cia');
						$tpl->assign('num_cia', $r['num_cia']);
						$tpl->assign('nombre', $r['nombre_cia']);
						
						$total = 0;
					}
					$tpl->newBlock('row');
					$tpl->assign('fecha', $r['fecha']);
					$tpl->assign('conciliado', $r['conciliado']);
					$tpl->assign('banco', $r['banco']);
					$tpl->assign('folio', $r['folio'] > 0 ? $r['folio'] : '');
					$tpl->assign('num_pro', $r['num_pro'] > 0 ? $r['num_pro'] : '');
					$tpl->assign('nombre_pro', $r['nombre_pro']);
					$tpl->assign('concepto', $r['concepto']);
					$tpl->assign('facturas', $r['facturas']);
					$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
					
					$total += $r['importe'];
					$tpl->assign('cia.total', number_format($total, 2, '.', ','));
					
					$total_general += $r['importe'];
				}
				$tpl->newBlock('total');
				$tpl->assign('total', number_format($total_general, 2, '.', ','));
			}
			else {
				$tpl->newBlock('no_result');
			}
			
			$tpl->printToScreen();
			
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/ConsultaGastosMecanicos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), 1, date('Y'))));
$tpl->assign('fecha2', date('d/m/Y', mktime(0, 0, 0, date('n'), date('n'), date('Y'))));

$sql = '
	SELECT
		idadministrador
			AS
				id,
		nombre_administrador
			AS
				nombre
	FROM
		catalogo_administradores
	ORDER BY
		nombre
		
';
$admins = $db->query($sql);

if ($admins)
	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $a['id']);
		$tpl->assign('nombre', $a['nombre']);
	}

$tpl->printToScreen();
?>