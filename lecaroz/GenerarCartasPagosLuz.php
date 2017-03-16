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
		case 'generar':
			$condiciones = array();
			
			if (!in_array($_SESSION['iduser'], array(1))) {
				$condiciones[] = $_SESSION['iduser'] >= 28 ? 'num_cia BETWEEN 900 AND 998' : 'num_cia BETWEEN 1 AND 899';
			}
			
			$condiciones[] = 'codgastos = 12';
			
			$condiciones[] = '(no_cta_cia_luz IS NOT NULL AND TRIM(no_cta_cia_luz) <> \'\')';
			
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
					$condiciones[] = 'num_cia IN (' . implode(', ', $cias) . ')';
				}
			}
			
			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0) {
				$condiciones[] = 'idadministrador = ' . $_REQUEST['admin'];
			}
			
			if ((isset($_REQUEST['fecha1']) && trim($_REQUEST['fecha1']) != '') || (isset($_REQUEST['fecha2']) && trim($_REQUEST['fecha2']) != '')) {
				if ((isset($_REQUEST['fecha1']) && trim($_REQUEST['fecha1']) != '') && (isset($_REQUEST['fecha2']) && trim($_REQUEST['fecha2']) != '')) {
					$condiciones[] = 'fecha_con BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
				}
				else if (isset($_REQUEST['fecha1']) && trim($_REQUEST['fecha1']) != '') {
					$condiciones[] = 'fecha_con = \'' . $_REQUEST['fecha1'] . '\'';
				}
				else if (isset($_REQUEST['fecha2']) && trim($_REQUEST['fecha2']) != '') {
					$condiciones[] = 'fecha_con >= \'' . $_REQUEST['fecha2'] . '\'';
				}
			}
			
			$sql = '
				SELECT
					num_cia,
					nombre
						AS
							nombre_cia,
					no_cta_cia_luz
						AS
							num_servicio,
					fecha_con
						AS
							fecha,
					EXTRACT(year FROM fecha_con)
						AS
							anio_rec,
					EXTRACT(month FROM fecha_con)
						AS
							mes_rec,
					c.importe,
					CASE
						WHEN cuenta = 1 THEN
							\'Banorte\'
						WHEN cuenta = 2 THEN
							\'Santander\'
					END
						AS
							banco,
					folio
				FROM
						cheques c
					LEFT JOIN
						estado_cuenta ec
							USING
								(num_cia, cuenta, folio)
					LEFT JOIN
						catalogo_companias cc
							USING
								(num_cia)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					num_cia
			';
			$result = $db->query($sql);
			
			if ($result) {
				$tpl = new TemplatePower('plantillas/ban/CartaPagoLuz.tpl');
				$tpl->prepare();
				
				$dia = date('j');
				$mes = date('n');
				$anio = date('Y');
				
				
				foreach ($result as $r) {
					$tpl->newBlock('carta');
					$tpl->assign('num_cia', $r['num_cia']);
					$tpl->assign('nombre_cia', $r['nombre_cia']);
					$tpl->assign('dia', $dia);
					$tpl->assign('mes', mes_escrito($mes));
					$tpl->assign('anio', $anio);
					$tpl->assign('num_servicio', $r['num_servicio']);
					$tpl->assign('mes_rec', mes_escrito($r['mes_rec']));
					$tpl->assign('anio_rec', $r['anio_rec']);
					$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
					$tpl->assign('banco', $r['banco']);
					$tpl->assign('folio', $r['folio']);
					$tpl->assign('fecha', $r['fecha']);
					$tpl->assign('salto', '<br style="page-break-after:always;" /><!--<br />-->');
				}
				
				$tpl->printToScreen();
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/GenerarCartasPagosLuz.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

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

foreach ($admins as $a) {
	$tpl->newBlock('admin');
	$tpl->assign('id', $a['id']);
	$tpl->assign('nombre', $a['nombre']);
}

$tpl->printToScreen();
?>