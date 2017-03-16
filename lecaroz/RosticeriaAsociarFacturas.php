<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/auxinv.inc.php');

function toInt($value)
{
	return intval($value, 10);
}

function toNumberFormat($value)
{
	return number_format($value, 2);
}

$_meses = array(
	1  => 'Ene',
	2  => 'Feb',
	3  => 'Mar',
	4  => 'Abr',
	5  => 'May',
	6  => 'Jun',
	7  => 'Jul',
	8  => 'Ago',
	9  => 'Sep',
	10 => 'Oct',
	11 => 'Nov',
	12 => 'Dic'
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

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'obtener_pro':
			$sql = "SELECT
				nombre
			FROM
				catalogo_proveedores
			WHERE
				num_proveedor < 9000
				AND num_proveedor = {$_REQUEST['num_pro']}";

			$result = $db->query($sql);

			if ($result)
			{
				echo $result[0]['nombre'];
			}

			break;

		case 'validar_fac':
			$sql = "SELECT
				id
			FROM
				facturas
			WHERE
				num_proveedor = {$_REQUEST['num_pro']}
				AND num_fact = '{$_REQUEST['num_fact']}'";

			$result = $db->query($sql);

			if ($result)
			{
				$data['status'] = 1;
			}
			else
			{
				$data['status'] = -1;
			}

			echo json_encode($data);

			break;

		case 'obtener_remision':
			$condiciones[] = "cd.num_proveedor = '{$_REQUEST['num_pro']}'";
			$condiciones[] = "cd.numero_fact = '{$_REQUEST['num_rem']}'";
			// $condiciones[] = 'cd.tsfac IS NULL';

			$sql = "SELECT
				cd.num_proveedor AS num_pro,
				cp.nombre AS nombre_pro,
				cd.numero_fact AS num_rem,
				cd.num_cia,
				cc.nombre_corto AS nombre_cia,
				cd.fecha_mov AS fecha,
				SUM(cd.total) AS total,
				cd.num_fact,
				cd.tsfac
			FROM
				compra_directa cd
				LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
				LEFT JOIN catalogo_companias cc USING (num_cia)
			WHERE
				" . implode(' AND ', $condiciones) . "
			GROUP BY
				num_pro,
				nombre_pro,
				num_rem,
				cd.num_cia,
				nombre_cia,
				fecha,
				cd.num_fact,
				cd.tsfac
			ORDER BY
				fecha DESC";

			$result = $db->query($sql);

			if ($result)
			{
				$data = array();

				foreach ($result as $row)
				{
					$data[] = array(
						'text'				=> utf8_encode("{$row['num_cia']} {$row['nombre_cia']}"),
						'value'				=> json_encode(array(
							'num_rem'		=> $row['num_rem'],
							'num_cia'		=> intval($row['num_cia']),
							'nombre_cia'	=> utf8_encode($row['nombre_cia']),
							'fecha'			=> $row['fecha'],
							'total'			=> floatval($row['total']),
							'status'		=> $row['tsfac'] != '' ? FALSE : TRUE,
							'num_fact'		=> $row['num_fact']
						)),
						'class'				=> $row['tsfac'] != '' ? 'red' : ''
					);
				}

				echo json_encode($data);
			}

			break;

		case 'registrar':
			$remisiones = array();
			$num_remisiones = array();

			foreach ($_REQUEST['num_rem'] as $i => $num_rem)
			{
				if ($num_rem == '')
				{
					continue;
				}

				$data = json_decode($_REQUEST['num_cia'][$i]);

				if ( ! $data->status)
				{
					continue;
				}

				$remisiones[] = "({$_REQUEST['num_pro']}, '{$num_rem}', '{$data->fecha}'::DATE, {$data->num_cia})";

				$num_remisiones[] = $num_rem;
			}

			$sql = "SELECT
				cd.idcompra_directa AS id,
				cd.num_cia,
				cd.num_proveedor,
				cd.numero_fact AS num_rem,
				cd.fecha_mov AS fecha,
				cd.codmp,
				CASE
					WHEN cd.kilos > 0 THEN
						cd.kilos
					ELSE
						cd.cantidad
				END AS cantidad,
				CASE
					WHEN cd.kilos > 0 THEN
						cd.cantidad / cd.kilos
					ELSE
						1
				END AS contenido,
				cd.precio_unit / (1 + (COALESCE(cmp.porcentaje_ieps, 0) + COALESCE(cmp.porcentaje_iva, 0)) / 100) AS precio,
				cd.precio_unidad / (1 + (COALESCE(cmp.porcentaje_ieps, 0) + COALESCE(cmp.porcentaje_iva, 0)) / 100) AS precio_unidad,
				COALESCE(cmp.porcentaje_ieps, 0) AS pieps,
				(cd.total / (1 + (COALESCE(cmp.porcentaje_ieps, 0) + COALESCE(cmp.porcentaje_iva, 0)) / 100)) * COALESCE(cmp.porcentaje_ieps / 100, 0) AS ieps,
				COALESCE(cmp.porcentaje_iva, 0) AS piva,
				(cd.total / (1 + (COALESCE(cmp.porcentaje_ieps, 0) + COALESCE(cmp.porcentaje_iva, 0)) / 100)) * COALESCE(cmp.porcentaje_iva / 100, 0) AS iva,
				COALESCE(cd.total) AS total
			FROM
				compra_directa cd
				LEFT JOIN catalogo_proveedores cp USING (num_proveedor)
				LEFT JOIN catalogo_mat_primas cmp USING (codmp)
			WHERE
				(cd.num_proveedor, cd.numero_fact, cd.fecha_mov, cd.num_cia) IN (VALUES " . implode(', ', $remisiones) . ")
			ORDER BY
				cd.numero_fact,
				cd.codmp";

			$result = $db->query($sql);

			$subtotal = 0;
			$ieps = 0;
			$iva = 0;
			$total = 0;
			$num_cia = $result[0]['num_cia'];
			$ids = array();

			$sql = '';

			foreach ($result as $row)
			{
				$sql .= "INSERT INTO entrada_mp (
					num_cia,
					num_proveedor,
					num_fact,
					fecha,
					codmp,
					cantidad,
					contenido,
					precio,
					importe,
					pieps,
					ieps,
					piva,
					iva,
					iduser
				) VALUES (
					{$row['num_cia']},
					{$_REQUEST['num_pro']},
					'{$_REQUEST['num_fact']}',
					'{$_REQUEST['fecha']}',
					{$row['codmp']},
					{$row['cantidad']},
					{$row['contenido']},
					{$row['precio']},
					{$row['total']},
					{$row['pieps']},
					{$row['ieps']},
					{$row['piva']},
					{$row['iva']},
					{$_SESSION['iduser']}
				);\n";

				$ieps += $row['ieps'];
				$iva += $row['iva'];
				$total += $row['total'];

				$ids[] = $row['id'];
			}

			$subtotal = $total - $ieps - $iva;

			$sql .= "INSERT INTO facturas (
				num_cia,
				num_proveedor,
				num_fact,
				fecha,
				codgastos,
				concepto,
				importe,
				pieps,
				ieps,
				piva,
				iva,
				total,
				tipo_factura,
				fecha_captura,
				iduser
			) VALUES (
				{$num_cia},
				{$_REQUEST['num_pro']},
				'{$_REQUEST['num_fact']}',
				'{$_REQUEST['fecha']}',
				33,
				'ESTA FACTURA SUSTITUYE A LAS REMISIONES " . implode(', ', $num_remisiones) . "',
				{$subtotal},
				" . ($ieps > 0 ? '8' : '0') . ",
				{$ieps},
				" . ($iva > 0 ? '16' : '0') . ",
				{$iva},
				{$total},
				0,
				NOW()::DATE,
				{$_SESSION['iduser']}
			);\n";

			$sql .= "INSERT INTO pasivo_proveedores (
				num_cia,
				num_proveedor,
				num_fact,
				fecha,
				codgastos,
				descripcion,
				total
			) VALUES (
				{$num_cia},
				{$_REQUEST['num_pro']},
				'{$_REQUEST['num_fact']}',
				'{$_REQUEST['fecha']}',
				33,
				'ESTA FACTURA SUSTITUYE A LAS REMISIONES " . implode(', ', $num_remisiones) . "',
				{$total}
			);\n";

			$sql .= "UPDATE compra_directa
			SET
				num_fact = '{$_REQUEST['num_fact']}',
				tsfac = NOW()
			WHERE
				idcompra_directa IN (" . implode(', ', $ids) . ");\n";

			$db->query($sql);

			break;
	}

	die;
}

$tpl = new TemplatePower('plantillas/ros/RosticeriaAsociarFacturas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
