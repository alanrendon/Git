<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');

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

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {

		case 'obtener_cia':
			if ($result = $db->query("
				SELECT
					nombre_corto
				FROM
					catalogo_companias cc
				WHERE
					num_cia < 900
					AND num_cia = {$_REQUEST['num_cia']}
			"))
			{
				echo utf8_encode($result[0]['nombre_corto']);
			}

			break;

		case 'obtener_pro':
			if ($result = $db->query("
				SELECT
					nombre
				FROM
					catalogo_proveedores cp
				WHERE
					num_proveedor < 9000
					AND num_proveedor = {$_REQUEST['num_pro']}
			"))
			{
				echo utf8_encode($result[0]['nombre']);
			}
			
			break;
		
		case 'validar_factura':
			if ($result = $db->query("
				SELECT
					pp.id
						AS idpp,
					fv.id
						AS idfv,
					pp.copia_fac
				FROM
					pasivo_proveedores pp
					LEFT JOIN facturas_validacion fv
						ON (fv.num_cia = pp.num_cia AND fv.num_pro = pp.num_proveedor AND fv.num_fact = pp.num_fact)
				WHERE
					pp.num_cia = {$_REQUEST['num_cia']}
					AND pp.num_proveedor = {$_REQUEST['num_pro']}
					AND pp.num_fact = UPPER('{$_REQUEST['num_fact']}')
			"))
			{
				$row = $result[0];

				if ($row['copia_fac'] == 't')
				{
					$status = 1;
				}
				else
				{
					$status = -1;
				}

				$data = array(
					'idpp'		=> get_val($row['idpp']),
					'idfv'		=> $row['idfv'] > 0 ? get_val($row['idfv']) : NULL,
					'status'	=> $status
				);
			}
			else if ($result = $db->query("
				SELECT
					fv.id
				FROM
					facturas_validacion fv
					LEFT JOIN auth a
						ON (a.iduser = fv.idalta)
				WHERE
					fv.num_cia = {$_REQUEST['num_cia']}
					AND fv.num_pro = {$_REQUEST['num_pro']}
					AND fv.num_fact = UPPER('{$_REQUEST['num_fact']}')
					AND tsbaja IS NULL
				ORDER BY
					fv.id DESC
				LIMIT
					1
			"))
			{
				$row = $result[0];

				$data = array(
					'idpp'		=> NULL,
					'idfv'		=> $row['id'],
					'status'	=> 1
				);
			}
			else if ($result = $db->query("
				SELECT
					fp.id,
					fp.fecha_cheque
				FROM
					facturas_pagadas fp
				WHERE
					fp.num_proveedor = {$_REQUEST['num_pro']}
					AND fp.num_fact = UPPER('{$_REQUEST['num_fact']}')
				ORDER BY
					fp.fecha_cheque DESC
				LIMIT
					1
			"))
			{
				$row = $result[0];

				$data = array(
					'fecha_pago'	=> $row['fecha_cheque'],
					'status'		=> -2
				);
			}
			else
			{
				$data = array(
					'status'	=> -1
				);
			}

			header('Content-Type: application/json');
			echo json_encode($data);

			break;

		case 'cancelar_validaciones':
			$sql = '';

			foreach ($_REQUEST['data'] as $data_row)
			{
				if ($data_row != '')
				{
					$data = json_decode($data_row);

					if ($data->idpp > 0)
					{
						$sql .= "
							UPDATE
								pasivo_proveedores
							SET
								copia_fac = FALSE
							WHERE
								id = {$data->idpp};
						";
					}

					if ($data->idfv > 0)
					{
						$sql .= "
							UPDATE
								facturas_validacion
							SET
								tsbaja = NOW(),
								idbaja = {$_SESSION['iduser']}
							WHERE
								id = {$data->idfv};
						";
					}
				}
			}

			if ($sql != '')
			{
				$db->query($sql);
			}

			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/fac/ValidarFacturasCancelar.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
