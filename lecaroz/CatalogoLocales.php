<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

$_meses = array(
	1  => 'ENE',
	2  => 'FEB',
	3  => 'MAR',
	4  => 'ABR',
	5  => 'MAY',
	6  => 'JUN',
	7  => 'JUL',
	8  => 'AGO',
	9  => 'SEP',
	10 => 'OCT',
	11 => 'NOV',
	12 => 'DIC'
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
		case 'inicio':
			$tpl = new TemplatePower('plantillas/ren/CatalogoLocalesInicio.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
		break;

		case 'consultar':
			$condiciones = array();

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25))) {
				$condiciones[] = 'inm.oficina = ' . $_SESSION['tipo_usuario'];
			}

			$condiciones[] = 'arrendador BETWEEN ' . ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25)) ? ($_SESSION['tipo_usuario'] == 2 ? '900 AND 998' : '1 AND 899') : '1 AND 999');

			$condiciones[] = 'loc.tsbaja IS NULL';

			if (isset($_REQUEST['arrendadores']) && trim($_REQUEST['arrendadores']) != '') {
				$arrendadores = array();

				$pieces = explode(',', $_REQUEST['arrendadores']);
				foreach ($pieces as $piece) {
					if (count($exp = explode('-', $piece)) > 1) {
						$arrendadores[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else {
						$arrendadores[] = $piece;
					}
				}

				if (count($arrendadores) > 0) {
					$condiciones[] = 'arrendador IN (' . implode(', ', $arrendadores) . ')';
				}
			}

			if (isset($_REQUEST['categoria']) && $_REQUEST['categoria'] > 0) {
				$condiciones[] = 'categoria = ' . $_REQUEST['categoria'];
			}

			$sql = '
				SELECT
					idlocal
						AS id,
					arrendador,
					nombre_arrendador,
					local,
					categoria,
					alias_local,
					domicilio,
					cuenta_predial,
					tipo_local,
					superficie,
					COALESCE((
						SELECT
							TRUE
						FROM
							rentas_arrendatarios
						WHERE
							idlocal = loc.idlocal
							AND tsbaja IS NULL
					), FALSE)
						AS status
				FROM
					rentas_locales loc
					LEFT JOIN rentas_arrendadores inm
						USING (idarrendador)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					arrendador,
					local
			';

			$result = $db->query($sql);

			$tpl = new TemplatePower('plantillas/ren/CatalogoLocalesConsulta.tpl');
			$tpl->prepare();

			if ($result) {
				$tpl->newBlock('result');

				$arrendador = NULL;
				foreach ($result as $rec) {
					if ($arrendador != $rec['arrendador']) {
						$arrendador = $rec['arrendador'];

						$tpl->newBlock('arrendador');
						$tpl->assign('arrendador', $rec['arrendador']);
						$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));

						$color = FALSE;
					}

					$tpl->newBlock('arrendatario');

					$tpl->assign('color', $color ? 'on' : 'off');

					$color = !$color;

					$tpl->assign('id', $rec['id']);
					$tpl->assign('gray', $rec['status'] == 't' ? '_gray' : '');
					$tpl->assign('local', $rec['local']);
					$tpl->assign('categoria', $rec['categoria']);
					$tpl->assign('alias_local', utf8_encode($rec['alias_local']));
					$tpl->assign('domicilio', utf8_encode($rec['domicilio']));
					$tpl->assign('cuenta_predial', $rec['cuenta_predial'] != '' ? $rec['cuenta_predial'] : '&nbsp;');
					$tpl->assign('tipo_local', $rec['tipo_local'] == 1 ? 'COMERCIAL' : 'VIVIENDA');
					$tpl->assign('superficie', $rec['superficie']);
				}
			}

			echo $tpl->getOutputContent();
		break;

		case 'alta':
			$tpl = new TemplatePower('plantillas/ren/CatalogoLocalesAlta.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
		break;

		case 'obtenerArrendador':
			$condiciones = array();

			if ( ! in_array($_SESSION['iduser'], array(1, 4, 7, 10, 25))) {
				$condiciones[] = 'oficina = ' . $_SESSION['tipo_usuario'];
			}

			$condiciones[] = 'arrendador = ' . $_REQUEST['arrendador'];

			$sql = '
				SELECT
					idarrendador,
					arrendador,
					nombre_arrendador
				FROM
					rentas_arrendadores
				WHERE
					' . implode(' AND ', $condiciones) . '
			';

			$arrendador = $db->query($sql);

			$arrendador[0]['nombre_arrendador'] = utf8_encode($arrendador[0]['nombre_arrendador']);

			if ($arrendador) {
				echo json_encode($arrendador[0]);
			}
		break;

		case 'doAlta':
			$sql = '
				SELECT
					local
				FROM
					rentas_locales
				WHERE
					idarrendador = ' . $_REQUEST['idarrendador'] . '
					AND tsbaja IS NULL
				ORDER BY
					local
			';

			$result = $db->query($sql);

			$local = 1;

			if ($result) {
				foreach ($result as $rec) {
					if ($local == $rec['local']) {
						$local++;
					}
					else {
						break;
					}
				}
			}

			$sql = '
				INSERT INTO
					rentas_locales
						(
							idarrendador,
							local,
							alias_local,
							categoria,
							domicilio,
							tipo_local,
							superficie,
							cuenta_predial,
							iduser_ins,
							tsins,
							iduser_mod,
							tsmod
						)
					VALUES
						(
							' . $_REQUEST['idarrendador'] . ',
							' . $local . ',
							\'' . utf8_decode($_REQUEST['alias_local']) . '\',
							' . utf8_decode($_REQUEST['categoria']) . ',
							\'' . utf8_decode($_REQUEST['domicilio']) . '\',
							' . $_REQUEST['tipo_local'] . ',
							' . get_val($_REQUEST['superficie']) . ',
							\'' . $_REQUEST['cuenta_predial'] . '\',
							' . $_SESSION['iduser'] . ',
							now(),
							' . $_SESSION['iduser'] . ',
							now()
						)
			';

			$db->query($sql);
		break;

		case 'modificar':
			$sql = '
				SELECT
					idlocal,
					idarrendador,
					arrendador,
					nombre_arrendador,
					alias_local,
					categoria,
					tipo_local,
					domicilio,
					superficie,
					cuenta_predial
				FROM
					rentas_locales loc
					LEFT JOIN rentas_arrendadores inm
						USING (idarrendador)
				WHERE
					idlocal = ' . $_REQUEST['id'] . '
			';
			$result = $db->query($sql);

			$rec = $result[0];

			$tpl = new TemplatePower('plantillas/ren/CatalogoLocalesModificar.tpl');
			$tpl->prepare();

			$tpl->assign('idlocal', $rec['idlocal']);
			$tpl->assign('idarrendador', $rec['idarrendador']);
			$tpl->assign('arrendador', $rec['arrendador']);
			$tpl->assign('nombre_arrendador', utf8_encode($rec['nombre_arrendador']));
			$tpl->assign('alias_local', utf8_encode($rec['alias_local']));
			$tpl->assign('categoria_' . $rec['categoria'], ' selected="selected"');
			$tpl->assign('tipo_local_' . $rec['tipo_local'], ' selected');
			$tpl->assign('domicilio', utf8_encode($rec['domicilio']));
			$tpl->assign('superficie', number_format($rec['superficie'], 2, '.', ','));
			$tpl->assign('cuenta_predial', $rec['cuenta_predial']);

			echo $tpl->getOutputContent();
		break;

		case 'doModificar':
			$sql = '
				UPDATE
					rentas_locales
				SET
					alias_local = \'' . utf8_decode($_REQUEST['alias_local']) . '\',
					categoria = ' . utf8_decode($_REQUEST['categoria']) . ',
					tipo_local = ' . $_REQUEST['tipo_local'] . ',
					domicilio = \'' . $_REQUEST['domicilio'] . '\',
					superficie = ' . get_val($_REQUEST['superficie']) . ',
					cuenta_predial = \'' . $_REQUEST['cuenta_predial'] . '\',
					tsmod = now(),
					iduser_mod = ' . $_SESSION['iduser'] . '
				WHERE
					idlocal = ' . $_REQUEST['idlocal'] . '
			';

			$db->query($sql);
		break;

		case 'doBaja':
			 $sql = '
			 	UPDATE
					rentas_locales
				SET
					tsbaja = now(),
					iduser_baja = ' . $_SESSION['iduser'] . '
				WHERE
					idlocal = ' . $_REQUEST['id'] . '
			 ';

			 $db->query($sql);
		break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ren/CatalogoLocales.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>
