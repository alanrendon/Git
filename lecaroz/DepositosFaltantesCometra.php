<?php

include_once('includes/class.db.inc.php');
include_once('includes/class.session2.inc.php');
include_once('includes/class.TemplatePower.inc.php');
include_once('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

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

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'validarComprobante':
			$sql = '
				SELECT
					comprobante
				FROM
					cometra
				WHERE
						tsend IS NULL
					AND
						comprobante = ' . $_REQUEST['comprobante'] . '
				LIMIT 1
			';
			$result = $db->query($sql);
			
			if ($result) {
				echo -1;
			}
		break;
		
		case 'registrar':
			$sql = '
				INSERT INTO
					cometra
						(
							comprobante,
							tipo_comprobante,
							num_cia,
							fecha,
							cod_mov,
							concepto,
							importe,
							iduser_ins,
							tsins,
							iduser_mod,
							tsmod
						)
					SELECT
						' . $_REQUEST['comprobante'] . ',
						4,
						num_cia,
						fecha,
						7,
						\'DEPOSITO FALTANTE\',
						importe,
						' . $_SESSION['iduser'] . ',
						now(),
						' . $_SESSION['iduser'] . ',
						now()
					FROM
						cometra_faltantes
					WHERE
						id
							IN
								(
									' . implode(', ', $_REQUEST['id']) . '
								)
			' . ";\n";
			
			$sql .= '
				UPDATE
					cometra_faltantes
				SET
					iduser_reg = ' . $_SESSION['iduser'] . ',
					tsreg = now()
				WHERE
					id
						IN
							(
								' . implode(', ', $_REQUEST['id']) . '
							)
			' . ";\n";
			
			$sql .= '
				UPDATE
					cometra
				SET
					banco = actual.banco
				FROM
					(
						SELECT
							banco
						FROM
							cometra
						WHERE
							banco IS NOT NULL
						LIMIT
							1
					)
						actual
				WHERE
					cometra.banco IS NULL
			' . ";\n";
			
			if ($sql != '') {
				$db->query($sql);
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/cometra/DepositosFaltantesCometra.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	SELECT
		CASE
			WHEN banco = 1 THEN
				\'BANORTE\'
			WHEN banco = 2 THEN
				\'SANTANDER\'
			ELSE
				\'SIN DEFINIR\'
		END
			AS
				nombre_banco
	FROM
		cometra
	WHERE
			tsend IS NULL
		AND
			banco IS NOT NULL
	LIMIT
		1
';
$banco = $db->query($sql);

if ($banco)  {
	$tpl->assign('nombre_banco', $banco[0]['nombre_banco']);
}
else {
	$tpl->assign('nombre_banco', 'SIN DEFINIR');
}

$sql = '
	SELECT
		id,
		num_cia,
		nombre,
		fecha,
		importe
	FROM
			cometra_faltantes cf
		LEFT JOIN
			catalogo_companias cc
				USING
					(
						num_cia
					)
	WHERE
		tsreg IS NULL
	ORDER BY
		num_cia,
		fecha
';
$result = $db->query($sql);

if ($result) {
	$total = 0;
	foreach ($result as $r) {
		$tpl->newBlock('row');
		$tpl->assign('id', $r['id']);
		$tpl->assign('num_cia', $r['num_cia']);
		$tpl->assign('nombre_cia', $r['nombre']);
		$tpl->assign('fecha', $r['fecha']);
		$tpl->assign('importe', number_format($r['importe'], 2, '.', ','));
		$total += $r['importe'];
	}
	$tpl->assign('_ROOT.total', number_format($total, 2, '.', ','));
}
else {
	$tpl->assign('_ROOT.total', '0.00');
}

$tpl->printToScreen();
?>
