<?php
include 'includes/class.db.inc.php';
include 'includes/class.session2.inc.php';
include 'includes/class.TemplatePower.inc.php';
include 'includes/dbstatus.php';

if(!function_exists('json_encode')) {
	include_once('includes/JSON.php');
	
	$GLOBALS['JSON_OBJECT'] = new Services_JSON();
	
	function json_encode($value) {
		return $GLOBALS['JSON_OBJECT']->encode($value); 
	}
	
	function json_decode($value) {
		return $GLOBALS['JSON_OBJECT']->decode($value); 
	}
}

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

if (!isset($_REQUEST['banco']) && get_val($_REQUEST['banco']) == 0) {
	die('Debe especificar el banco');
}

if (!isset($_REQUEST['importe']) && get_val($_REQUEST['importe']) == 0) {
	die('Debe especificar el monto');
}

$db = new DBclass($dsn, 'autocommit=yes');

$condiciones = array();

if ($_REQUEST['banco'] == 1) {
	$condiciones[] = 'clabe_cuenta IS NOT NULL AND LENGTH(TRIM(clabe_cuenta)) >= 10';
}
else if ($_REQUEST['banco'] == 2) {
	$condiciones[] = 'clabe_cuenta2 IS NOT NULL AND LENGTH(TRIM(clabe_cuenta2)) >= 10';
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

$sql = '
	SELECT
		num_cia,
		nombre,
		' . ($_REQUEST['banco'] == 1 ? 'clabe_cuenta' : 'clabe_cuenta2') . '
			AS cuenta
	FROM
		catalogo_companias
	WHERE
		' . implode(' AND ', $condiciones) . '
	ORDER BY
		num_cia
';

$result = $db->query($sql);

if ($result) {
	$sql = '';
	
	$fecha = isset($_REQUEST['fecha']) && $_REQUEST['fecha'] != '' ? $_REQUEST['fecha'] : date('d/m/Y');
	
	$cod_mov = isset($_REQUEST['cod_mov']) && $_REQUEST['cod_mov'] > 0 ? $_REQUEST['cod_mov'] : 5;
	
	$codgastos = isset($_REQUEST['codgastos']) && $_REQUEST['codgastos'] > 0 ? $_REQUEST['codgastos'] : 999;
	
	$cantidad = isset($_REQUEST['cantidad']) ? $_REQUEST['cantidad'] : 1;
	
	$tipo = isset($_REQUEST['tipo']) ? ($_REQUEST['tipo'] == 'cheque' ? 'FALSE' : 'TRUE') : ($cod_mov == 5 ? 'FALSE' : 'TRUE');
	
	$print = isset($_REQUEST['noprint']) ? 'TRUE' : 'FALSE';
	
	foreach ($result as $rec) {
		for ($i = 0; $i < $cantidad; $i++) {
			$sql .= '
				INSERT INTO
					cheques
						(
							num_cia,
							cuenta,
							fecha,
							folio,
							num_proveedor,
							a_nombre,
							cod_mov,
							codgastos,
							concepto,
							importe,
							archivo,
							poliza,
							imp,
							iduser
						)
					VALUES
						(
							' . $rec['num_cia'] . ',
							' . $_REQUEST['banco'] . ',
							\'' . $fecha . '\',
							(
								SELECT
									COALESCE(MAX(folio), 50) + 1
								FROM
									folios_cheque
								WHERE
									num_cia = ' . $rec['num_cia'] . '
									AND cuenta = ' . $_REQUEST['banco'] . '
									AND fecha >= \'2011/01/01\'
							),
							5001,
							\'' . $rec['nombre'] . '\',
							' . $cod_mov . ',
							' . $codgastos . ',
							\'' . $_REQUEST['concepto'] . '\',
							' . get_val($_REQUEST['importe']) . ',
							TRUE,
							' . $tipo . ',
							' . $print . ',
							1
						)
			' . ";\n";
			
			$sql .= '
				INSERT INTO
					estado_cuenta
						(
							num_cia,
							cuenta,
							fecha,
							folio,
							tipo_mov,
							cod_mov,
							concepto,
							importe,
							iduser
						)
					VALUES
						(
							' . $rec['num_cia'] . ',
							' . $_REQUEST['banco'] . ',
							\'' . $fecha . '\',
							(
								SELECT
									COALESCE(MAX(folio), 50) + 1
								FROM
									folios_cheque
								WHERE
									num_cia = ' . $rec['num_cia'] . '
									AND cuenta = ' . $_REQUEST['banco'] . '
									AND fecha >= \'2011/01/01\'
							),
							TRUE,
							' . $cod_mov . ',
							\'' . $_REQUEST['concepto'] . '\',
							' . get_val($_REQUEST['importe']) . ',
							1
						)
			' . ";\n";
			
			$sql .= '
				INSERT INTO
					movimiento_gastos
						(
							num_cia,
							fecha,
							codgastos,
							concepto,
							importe,
							cuenta,
							folio,
							captura
						)
					VALUES
						(
							' . $rec['num_cia'] . ',
							\'' . $fecha . '\',
							' . $codgastos . ',
							\'' . $_REQUEST['concepto'] . '\',
							' . get_val($_REQUEST['importe']) . ',
							' . $_REQUEST['banco'] . ',
							(
								SELECT
									COALESCE(MAX(folio), 50) + 1
								FROM
									folios_cheque
								WHERE
									num_cia = ' . $rec['num_cia'] . '
									AND cuenta = ' . $_REQUEST['banco'] . '
									AND fecha >= \'2011/01/01\'
							),
							TRUE
						)
			' . ";\n";
			
			$sql .= '
				INSERT INTO
					folios_cheque
						(
							num_cia,
							cuenta,
							folio,
							fecha,
							reservado,
							utilizado
						)
					VALUES
						(
							' . $rec['num_cia'] . ',
							' . $_REQUEST['banco'] . ',
							(
								SELECT
									COALESCE(MAX(folio), 50) + 1
								FROM
									folios_cheque
								WHERE
									num_cia = ' . $rec['num_cia'] . '
									AND cuenta = ' . $_REQUEST['banco'] . '
									AND fecha >= \'2011/01/01\'
							),
							\'' . $fecha . '\',
							FALSE,
							TRUE
						)
			' . ";\n";
		}
	}
	
	$db->query($sql);
}
?>
