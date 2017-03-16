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
		
		if ($result)
			echo -1;
	}
	
	die;
}

if (isset($_POST['num_cia'])) {
	$_SESSION['fpv']['num_pro'] = $_POST['num_pro'];
	$_SESSION['fpv']['codgastos'] = $_POST['codgastos'];
	$_SESSION['fpv']['concepto'] = $_POST['concepto'];
	$_SESSION['fpv']['fecha'] = $_POST['fecha'];
	
	if ($_POST['num_cia'] < 900) {
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
						pieps,
						ieps,
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
						' . get_val($_POST['pieps']) . ',
						' . get_val($_POST['ieps']) . ',
						' . get_val($_POST['piva']) . ',
						' . get_val($_POST['iva']) . ',
						' . get_val($_POST['prisr']) . ',
						' . get_val($_POST['priva']) . ',
						' . get_val($_POST['risr']) . ',
						' . get_val($_POST['riva']) . ',
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
		
		if (isset($_REQUEST['aclaracion'])) {
			$sql .= '
				INSERT INTO
					facturas_pendientes
						(
							num_proveedor,
							num_fact,
							fecha_solicitud,
							obs
						)
					VALUES
						(
							' . $_REQUEST['num_pro'] . ',
							\'' . $_REQUEST['num_fact'] . '\',
							now()::date,
							\'' . $_REQUEST['observaciones'] . '\'
						)
			' . ";\n";
		}
	}
	else {
		$sql = '
			INSERT INTO
				facturas_zap
					(
						num_cia,
						num_proveedor,
						num_fact,
						fecha,
						concepto,
						codgastos,
						importe,
						iva,
						pisr,
						isr,
						pivaret,
						ivaret,
						total,
						iduser,
						tscap
					)
				VALUES
					(
						' . $_POST['num_cia'] . ',
						' . $_POST['num_pro'] . ',
						' . $_POST['num_fact'] . ',
						\'' . $_POST['fecha'] . '\',
						\'' . $_POST['concepto'] . '\',
						' . $_POST['codgastos'] . ',
						' . get_val($_POST['importe']) . ',
						' . get_val($_POST['iva']) . ',
						' . get_val($_POST['prisr']) . ',
						' . get_val($_POST['risr']) . ',
						' . get_val($_POST['priva']) . ',
						' . get_val($_POST['riva']) . ',
						' . get_val($_POST['total']) . ',
						' . $_SESSION['iduser'] . ',
						now()
					)
		';
	}
	
	$db->query($sql);
	
	header('location: FacturasProveedoresVarios.php');
	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturasProveedoresVarios.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

/*
@ [31-Ene-2014] A partir del 1 de enero del 2014 el I.E.P.S. es del 16% en lugar del 15%
*/
$tpl->assign('pieps', in_array($_SESSION['iduser'], array(17)) ? '8.00' : '');

/*
@ [08-Ene-2010] A partir del 1 de enero del 2010 el I.V.A. es del 16% en lugar del 15%
*/
$tpl->assign('piva', in_array($_SESSION['iduser'], array(17, 2, 22, 10)) ? '' : '16.00');

if (isset($_SESSION['fpv'])) {
	$tpl->assign('num_pro', $_SESSION['fpv']['num_pro']);
	$tpl->assign('codgastos', $_SESSION['fpv']['codgastos']);
	$tpl->assign('concepto', $_SESSION['fpv']['concepto']);
	$tpl->assign('fecha', $_SESSION['fpv']['fecha']);
}

$tpl->printToScreen();
?>