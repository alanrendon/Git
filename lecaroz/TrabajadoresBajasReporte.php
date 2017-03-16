<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value) {
	return intval($value, 10);
}

$_meses = array(
	1  => 'ENERO',
	2  => 'FEBRERO',
	3  => 'MARZO',
	4  => 'ABRIL',
	5  => 'MAYO',
	6  => 'JUNIO',
	7  => 'JULIO',
	8  => 'AGOSTO',
	9  => 'SEPTIEMBRE',
	10 => 'OCTUBRE',
	11 => 'NOVIEMBRE',
	12 => 'DICIEMBRE'
);

$_dias = array(
	0 => 'D',
	1 => 'L',
	2 => 'M',
	3 => 'M',
	4 => 'J',
	5 => 'V',
	6 => 'S'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('EN PROCESO DE ACTUALIZACION');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		
		case 'reporte':
			$fecha1 = $_REQUEST['fecha1'];
			$fecha2 = $_REQUEST['fecha2'];
			
			$condiciones = array();

			$condiciones[] = "ct.fecha_baja BETWEEN '{$fecha1}' AND '{$fecha2}'";

			if (isset($_REQUEST['tipo']) || isset($_REQUEST['sin_definir']))
			{
				if (isset($_REQUEST['tipo']))
				{
					$condiciones_tipo[] = "ct.id_tipo_baja_trabajador IN (" . implode(', ', $_REQUEST['tipo']) . ")";
				}

				if (isset($_REQUEST['sin_definir']))
				{
					$condiciones_tipo[] = "ct.id_tipo_baja_trabajador IS NULL";
				}

				$condiciones[] = "(" . implode(' OR ', $condiciones_tipo) . ")";
			}

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
				$condiciones[] = 'cc.idadministrador = ' . $_REQUEST['admin'];
			}

			$sql = "
				SELECT
					ct.num_cia,
					cc.nombre
						AS nombre_cia,
					ct.num_emp,
					CONCAT_WS(' ', ct.ap_paterno, ct.ap_materno, ct.nombre)
						AS trabajador,
					ct.fecha_alta,
					ct.fecha_baja,
					COALESCE(ct.id_tipo_baja_trabajador, 999999)
						AS tipo_baja,
					COALESCE(ctbt.descripcion, 'SIN DEFINIR')
						AS motivo
				FROM
					catalogo_trabajadores ct
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_tipos_baja_trabajador ctbt
						USING (id_tipo_baja_trabajador)
				WHERE
					" . implode(' AND ', $condiciones) . "
				ORDER BY
					COALESCE(ct.id_tipo_baja_trabajador, 999999),
					num_cia,
					fecha_baja
			";
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/nom/TrabajadoresBajasReporteImpreso.tpl');
			$tpl->prepare();

			$tpl->assign('fecha1', $fecha1);
			$tpl->assign('fecha2', $fecha2);
			
			if ($result) {
				$tipo_baja = NULL;

				foreach ($result as $rec)
				{
					if ($tipo_baja != $rec['tipo_baja'])
					{
						$tipo_baja = $rec['tipo_baja'];

						$tpl->newBlock('tipo');
						$tpl->assign('tipo', utf8_decode($rec['motivo']));

						$total = 0;
					}

					$tpl->newBlock('row');
					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_decode($rec['nombre_cia']));
					$tpl->assign('num_emp', $rec['num_emp']);
					$tpl->assign('trabajador', utf8_decode($rec['trabajador']));
					$tpl->assign('fecha_alta', $rec['fecha_alta']);
					$tpl->assign('fecha_baja', $rec['fecha_baja']);

					$tpl->assign('tipo.total', number_format(++$total));
				}
			}
			
			$tpl->printToScreen();
			
			break;

	}
	
	die;
}

$tpl = new TemplatePower('plantillas/nom/TrabajadoresBajasReporte.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('01/m/Y'));
$tpl->assign('fecha2', date('d/m/Y'));

$sql = '
	SELECT
		idadministrador
			AS value,
		nombre_administrador
			AS text
	FROM
		catalogo_administradores
	ORDER BY
		text
';

$result = $db->query($sql);

if ($result) {
	foreach ($result as $r) {
		$tpl->newBlock('admin');
		$tpl->assign('value', $r['value']);
		$tpl->assign('text', utf8_encode($r['text']));
	}
}

$sql = '
	SELECT
		id_tipo_baja_trabajador
			AS tipo,
		descripcion
	FROM
		catalogo_tipos_baja_trabajador
	WHERE
		tsbaja IS NULL
	ORDER BY
		tipo
';

$result = $db->query($sql);

if ($result) {
	foreach ($result as $r) {
		$tpl->newBlock('tipo');
		$tpl->assign('tipo', $r['tipo']);
		$tpl->assign('descripcion', utf8_encode($r['descripcion']));
	}
}

$tpl->printToScreen();
?>
