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
					pp.id,
					pp.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					pp.num_proveedor AS num_pro,
					pp.num_fact,
					pp.copia_fac
				FROM
					pasivo_proveedores pp
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					pp.num_proveedor = {$_REQUEST['num_pro']}
					AND num_fact = UPPER('{$_REQUEST['num_fact']}')
			"))
			{
				$row = $result[0];

				if ($row['num_cia'] == $_REQUEST['num_cia'] && $row['copia_fac'] == 'f')
				{
					$status = 1;
				}
				else if ($row['num_cia'] == $_REQUEST['num_cia'] && $row['copia_fac'] == 't')
				{
					$status = -4;
				}
				else
				{
					$status = -5;
				}

				$data = array(
					'id'			=> get_val($row['id']),
					'num_cia'		=> get_val($row['num_cia']),
					'nombre_cia'	=> utf8_encode($row['nombre_cia']),
					'num_pro'		=> get_val($row['num_pro']),
					'num_fact'		=> mb_strtoupper(utf8_encode($row['num_fact'])),
					'status'		=> $status
				);
			}
			else if ($result = $db->query("
				SELECT
					fv.tsalta::DATE
						AS fecha_alta,
					a.username
						AS usuario
				FROM
					facturas_validacion fv
					LEFT JOIN auth a
						ON (a.iduser = fv.idalta)
				WHERE
					fv.num_pro = {$_REQUEST['num_pro']}
					AND fv.num_fact = UPPER('{$_REQUEST['num_fact']}')
					AND tsbaja IS NULL
			"))
			{
				$row = $result[0];

				$data = array(
					'fecha_alta'	=> $row['fecha_alta'],
					'usuario'		=> $row['usuario'],
					'status'		=> -3
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
					'num_cia'	=> get_val($_REQUEST['num_cia']),
					'num_pro'	=> get_val($_REQUEST['num_pro']),
					'num_fact'	=> mb_strtoupper(utf8_encode($_REQUEST['num_fact'])),
					'status'	=> -1
				);
			}

			header('Content-Type: application/json');
			echo json_encode($data);

			break;

		case 'registrar_facturas':
			$sql = '';

			foreach ($_REQUEST['data'] as $data_row)
			{
				if ($data_row != '')
				{
					$data = json_decode($data_row);

					if ($data->status == 1)
					{
						$sql .= "
							INSERT INTO
								facturas_validacion (
									num_cia,
									num_pro,
									num_fact,
									idalta,
									tsvalid,
									idvalid
								)
								VALUES (
									{$data->num_cia},
									{$data->num_pro},
									UPPER('{$data->num_fact}'),
									{$_SESSION['iduser']},
									NOW(),
									{$_SESSION['iduser']}
								);
						";

						$sql .= "
							UPDATE
								pasivo_proveedores
							SET
								copia_fac = TRUE
							WHERE
								id = $data->id;
						";
					}
					else if ($data->status == -1)
					{
						$sql .= "
							INSERT INTO
								facturas_validacion (
									num_cia,
									num_pro,
									num_fact,
									idalta
								)
								VALUES (
									{$data->num_cia},
									{$data->num_pro},
									UPPER('{$data->num_fact}'),
									{$_SESSION['iduser']}
								);
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

$tpl = new TemplatePower('plantillas/fac/ValidarFacturas.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
