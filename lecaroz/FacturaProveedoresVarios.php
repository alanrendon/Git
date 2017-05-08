<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

function toInt($value)
{
	return intval($value, 10);
}

function dmy_to_ymd($date)
{
	list($day, $month, $year) = explode('/', $date);

	return date('Y-m-d', mktime(0, 0, 0, $month, $day, $year));
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

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'obtener_cia':
			$result = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$_REQUEST['num_cia']}");

			if ($result)
			{
				echo $result[0]['nombre_corto'];
			}

			break;

		case 'obtener_pro':
			$result = $db->query("SELECT nombre FROM catalogo_proveedores WHERE num_proveedor = {$_REQUEST['num_pro']}");

			if ($result)
			{
				echo $result[0]['nombre'];
			}

			break;

		case 'obtener_gasto':
			$result = $db->query("SELECT descripcion FROM catalogo_gastos WHERE codgastos = {$_REQUEST['gasto']}");

			if ($result)
			{
				echo $result[0]['descripcion'];
			}

			break;

		case 'validar_fecha':
			/*$result = $db->query("SELECT '{$_REQUEST['fecha']}'::DATE < MAX(fecha) + INTERVAL '1 MONTH' AS status FROM balances_pan");

			if ($result[0]['status'] == 't')
			{
				echo -1;
			}
			else
			{
				echo 1;
			}*/echo 1;

			break;

		case 'validar_factura':
			$result = $db->query("SELECT TRUE AS status FROM facturas WHERE num_proveedor = {$_REQUEST['num_pro']} AND num_fact = '{$_REQUEST['num_fact']}'");

			if ($result[0]['status'] == 't')
			{
				echo -1;
			}
			else
			{
				echo 1;
			}

			break;

		case 'validar_agua':
			$result = $db->query("SELECT TRUE AS status FROM facturas WHERE num_cia = {$_POST['num_cia']} AND anio = {$_POST['anio']} AND bimestre = {$_POST['bimestre']}");

			if ($result[0]['status'] == 't')
			{
				echo -1;
			}
			else
			{
				echo 1;
			}

			break;

		case 'guardar':
			$sql = "INSERT INTO facturas (
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
				concepto_otros,
				importe_otros,
				total,
				fecha_captura,
				iduser
			)
			VALUES (
				{$_REQUEST['num_cia']},
				{$_REQUEST['num_pro']},
				'{$_REQUEST['num_fact']}',
				'{$_REQUEST['fecha']}',
				{$_REQUEST['codgastos']},
				'{$_REQUEST['concepto']}',
				{$_REQUEST['tipo_factura']},
				" . ($_REQUEST['codgastos'] == 79 ? $_REQUEST['anio'] : 'NULL') . ",
				" . ($_REQUEST['codgastos'] == 79 ? $_REQUEST['bimestre'] : 'NULL') . ",
				" . get_val($_REQUEST['subtotal']) . ",
				" . (get_val($_REQUEST['ieps']) + get_val($_REQUEST['ieps_libre']) > 0 ? get_val($_REQUEST['por_ieps']) : 0) . ",
				" . (get_val($_REQUEST['ieps']) + get_val($_REQUEST['ieps_libre'])) . ",
				" . (get_val($_REQUEST['iva']) > 0 ? get_val($_REQUEST['por_iva']) : 0) . ",
				" . get_val($_REQUEST['iva']) . ",
				" . (get_val($_REQUEST['ret_isr']) > 0 ? get_val($_REQUEST['por_ret_isr']) : 0) . ",
				" . (get_val($_REQUEST['ret_iva']) > 0 ? get_val($_REQUEST['por_ret_iva']) : 0) . ",
				" . get_val($_REQUEST['ret_isr']) . ",
				" . get_val($_REQUEST['ret_iva']) . ",
				'{$_REQUEST['concepto_otros']}',
				" . get_val($_REQUEST['importe_otros']) . ",
				" . get_val($_REQUEST['total']) . ",
				now()::date,
				{$_SESSION['iduser']}
			);

			INSERT INTO pasivo_proveedores (
				num_cia,
				num_proveedor,
				num_fact,
				fecha,
				codgastos,
				descripcion,
				total,
				copia_fac
			)
			VALUES (
				{$_REQUEST['num_cia']},
				{$_REQUEST['num_pro']},
				'{$_REQUEST['num_fact']}',
				'{$_REQUEST['fecha']}',
				{$_REQUEST['codgastos']},
				'{$_REQUEST['concepto']}',
				" . get_val($_REQUEST['total']) . ",
				COALESCE((
					SELECT
						TRUE
					FROM
						facturas_validacion
					WHERE
						num_cia = {$_REQUEST['num_cia']}
						AND num_pro = {$_REQUEST['num_pro']}
						AND num_fact = '{$_REQUEST['num_fact']}'
						AND tsbaja IS NULL
				), FALSE)
			);

			UPDATE
				facturas_validacion
			SET
				tsvalid = NOW(),
				idvalid = {$_SESSION['iduser']}
			WHERE
				num_cia = {$_REQUEST['num_cia']}
				AND num_pro = {$_REQUEST['num_pro']}
				AND num_fact = '{$_REQUEST['num_fact']}'
				AND tsbaja IS NULL;";

			if (isset($_REQUEST['aclaracion'])) {
				$sql .= "INSERT INTO facturas_pendientes (
					num_proveedor,
					num_fact,
					fecha_solicitud,
					obs
				)
				VALUES (
					{$_REQUEST['num_pro']},
					'{$_REQUEST['num_fact']}',
					NOW()::DATE,
					'{$_REQUEST['observaciones']}'
				);";
			}

			$db->query($sql);

			break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/fac/FacturaProveedoresVarios.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
