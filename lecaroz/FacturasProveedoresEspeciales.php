<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('Modificando');

$text = array('Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ', 'ñ');
$html = array('&Aacute;', '&Eacute;', '&Iacute;', '&Oacute;', '&Uacute;', '&Ntilde;', '&Ntilde;');

if (isset($_POST['action'])) {
	if ($_POST['action'] == 'retrieve') {
		if ($_POST['type'] == 'c')
			$sql = '
				SELECT
					nombre_corto
						AS
							concepto
				FROM
					catalogo_companias
				WHERE
					num_cia = ' . $_POST['clave'] . '
			';
		else if ($_POST['type'] == 'p')
			$sql = '
				SELECT
					nombre
						AS
							concepto,
					observaciones
				FROM
					catalogo_proveedores
				WHERE
					num_proveedor = ' . $_POST['clave'] . '
			';
		else if ($_POST['type'] == 'g')
			$sql = '
				SELECT
					descripcion
						AS
							concepto
				FROM
					catalogo_gastos
				WHERE
					codgastos = ' . $_POST['clave'] . '
			';
		$result = $db->query($sql);
		
		if ($result) {
			if ($_POST['type'] == 'p' && trim($result[0]['observaciones']) != '') {
				echo $result[0]['concepto'] . '|' . $result[0]['observaciones'];
			}
			else {
				echo $result[0]['concepto'];
			}
		}
	}
	else if ($_POST['action'] == 'validFac') {
		$sql = '
			SELECT
					num_cia
				||
					\'|\'
				||
					nombre_corto
				||
					\'|\'
				||
					fecha
						AS
							data
						
			FROM
					facturas
						f
				LEFT JOIN
					catalogo_companias
						cc
						USING
							(
								num_cia
							)
			WHERE
					f.num_proveedor = ' . $_POST['p'] . '
				AND
					f.num_fact = \'' . strtoupper($_POST['f']) . '\'
		';
		$result = $db->query($sql);
		
		if ($result)
			echo $result[0]['data'];
	}
	else if ($_POST['action'] == 'validDate') {
		$sql = '
			SELECT
				\'' . $_POST['fecha'] . '\' < (
												SELECT
													fecha + interval \'1 month\' - interval \'1 day\'
												FROM
													balances_pan
												ORDER BY
													fecha
														DESC
												LIMIT
													1
											)
					AS
						result
		';
		$result = $db->query($sql);
		
		echo $result[0]['result'];
	}
	else if ($_POST['action'] == 'validBim') {
		$sql = '
			SELECT
				id
			FROM
				facturas
			WHERE
					num_cia = ' . $_POST['num_cia'] . '
				AND
					anio = ' . $_POST['anio'] . '
				AND
					bimestre = ' . $_POST['bim'] . '
		';
		$result = $db->query($sql);
		
//		if ($result)
//			echo -1;
	}
	
	die;
}

if (isset($_POST['num_cia'])) {
	$_SESSION['fpe']['num_pro'] = $_POST['num_pro'];
	$_SESSION['fpe']['codgastos'] = $_POST['codgastos'];
	$_SESSION['fpe']['concepto'] = $_POST['concepto'];
	
	if ($_POST['num_cia'] < 900)
		$sql = '
			INSERT INTO
				facturas
					(
						num_cia,
						num_proveedor,
						num_fact,
						fecha,
						codgastos,
						concepto,
						tipo_factura,
						anio,
						bimestre,
						importe,
						piva,
						iva,
						pretencion_isr,
						pretencion_iva,
						retencion_isr,
						retencion_iva,
						total,
						fecha_captura,
						iduser
					)
			VALUES
					(
						' . $_POST['num_cia'] . ',
						' . $_POST['num_pro'] . ',
						\'' . $_POST['num_fact'] . '\',
						\'' . $_POST['fecha'] . '\',
						' . $_POST['codgastos'] . ',
						\'' . $_POST['concepto'] . '\',
						' . $_POST['tipo_factura'] . ',
						' . ($_POST['codgastos'] == 79 ? $_POST['anio'] : 'NULL') . ',
						' . ($_POST['codgastos'] == 79 ? $_POST['bimestre'] : 'NULL') . ',
						' . get_val($_POST['importe']) . ',
						' . get_val($_POST['piva']) . ',
						' . get_val($_POST['iva']) . ',
						0,
						0,
						0,
						0,
						' . get_val($_POST['total']) . ',
						now()::date,
						' . $_SESSION['iduser'] . '
					);

			INSERT INTO
				pasivo_proveedores
					(
						num_cia,
						num_proveedor,
						num_fact,
						fecha,
						codgastos,
						descripcion,
						total,
						copia_fac
					)
			VALUES
					(
						' . $_POST['num_cia'] . ',
						' . $_POST['num_pro'] . ',
						\'' . $_POST['num_fact'] . '\',
						\'' . $_POST['fecha'] . '\',
						' . $_POST['codgastos'] . ',
						\'' . $_POST['concepto'] . '\',
						' . get_val($_POST['total']) . ',
						COALESCE((
							SELECT
								TRUE
							FROM
								facturas_validacion
							WHERE
								num_cia = ' . $_REQUEST['num_cia'] . '
								AND num_pro = ' . $_REQUEST['num_pro'] . '
								AND num_fact = \'' . $_REQUEST['num_fact'] . '\'
								AND tsbaja IS NULL
						), FALSE)

					);

			UPDATE
				facturas_validacion
			SET
				tsvalid = NOW(),
				idvalid = ' . $_SESSION['iduser'] . '
			WHERE
				num_cia = ' . $_REQUEST['num_cia'] . '
				AND num_pro = ' . $_REQUEST['num_pro'] . '
				AND num_fact = \'' . $_REQUEST['num_fact'] . '\'
				AND tsbaja IS NULL;
		';
	else
		$sql = '';
	
	$db->query($sql);
	
	header('location: FacturasProveedoresEspeciales.php');
	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasProveedoresEspeciales.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

if (isset($_SESSION['fpe'])) {
	$tpl->assign('num_pro', $_SESSION['fpe']['num_pro']);
	$tpl->assign('codgastos', $_SESSION['fpe']['codgastos']);
	$tpl->assign('concepto', $_SESSION['fpe']['concepto']);
}

$tpl->printToScreen();
?>