<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/phpmailer/class.phpmailer.php');

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'actualizar':
			$turnos = array(
				1,
				2,
				3,
				4
			);
			
			$sql = '';
			
			foreach ($_REQUEST['num_cia'] as $i => $num_cia) {
				foreach ($turnos as $turno) {
					if (get_val($_REQUEST['pesada_' . $turno][$i]) > 0) {
						if ($id = $db->query('SELECT id FROM catalogo_pesadas WHERE num_cia = ' . $num_cia . ' AND cod_turno = ' . $turno)) {
							$sql .= '
								UPDATE
									catalogo_pesadas
								SET
									pesada = ' . get_val($_REQUEST['pesada_' . $turno][$i]) . '
								WHERE
									id = ' . $id[0]['id'] . '
							' . ";\n";
						}
						else {
							$sql .= '
								INSERT INTO
									catalogo_pesadas
										(
											num_cia,
											cod_turno,
											pesada
										)
									VALUES
										(
											' . $num_cia . ',
											' . $turno . ',
											' . get_val($_REQUEST['pesada_' . $turno][$i]) . '
										)
							' . ";\n";
						}
					}
					else {
						$sql .= '
							DELETE FROM
								catalogo_pesadas
							WHERE
									num_cia = ' . $num_cia . '
								AND
									cod_turno = ' . $turno . '
						' . ";\n";
					}
				}
			}
			
			$db->query($sql);
		break;
	}
	
	die;
}

$condiciones = array();

$condiciones[] = 'num_cia <= 300';

if (!in_array($_SESSION['iduser'], array(1, 4))) {
	$condiciones[] = 'iduser = ' . $_SESSION['iduser'];
}

$sql = '
	SELECT
		num_cia,
		nombre_corto
			AS
				nombre_cia
	FROM
			catalogo_companias cc
		LEFT JOIN
			catalogo_operadoras co
				USING
					(idoperadora)
	WHERE
		' . implode(' AND ', $condiciones) . '
	ORDER BY
		num_cia
';
$cias = $db->query($sql);

$sql = '
	SELECT
		num_cia,
		cod_turno,
		pesada
	FROM
			catalogo_pesadas cp
		LEFT JOIN
			catalogo_companias cc
				USING
					(num_cia)
		LEFT JOIN
			catalogo_operadoras co
				USING
					(idoperadora)
	WHERE
		' . implode(' AND ', $condiciones) . '
	ORDER BY
		num_cia,
		cod_turno
';

$result = $db->query($sql);

$pesadas = array();
if ($result) {
	foreach ($result as $rec) {
		$pesadas[$rec['num_cia']][$rec['cod_turno']] = $rec['pesada'];
	}
}

$tpl = new TemplatePower('plantillas/pan/Pesadas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$color = FALSE;
foreach ($cias as $cia) {
	$tpl->newBlock('row');
	
	$tpl->assign('color', $color ? 'on' : 'off');
	$color = !$color;
	
	$tpl->assign('num_cia', $cia['num_cia']);
	$tpl->assign('nombre_cia', utf8_encode($cia['nombre_cia']));
	
	if (isset($pesadas[$cia['num_cia']])) {
		foreach ($pesadas[$cia['num_cia']] as $turno => $pesada) {
			$tpl->assign('pesada_' . $turno, number_format($pesada, 2, '.', ','));
		}
	}
}

$tpl->printToScreen();
?>
