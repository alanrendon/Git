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

$_dias_semana = array(
	0	=> 'Do',
	1	=> 'Lu',
	2	=> 'Ma',
	3	=> 'Mi',
	4	=> 'Ju',
	5	=> 'Vi',
	6	=> 'Sa'
);

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_REQUEST['accion']))
{
	switch ($_REQUEST['accion'])
	{

		case 'obtener_producto':
			$sql = "
				SELECT
					nombre
				FROM
					catalogo_mat_primas
				WHERE
					codmp = {$_REQUEST['codmp']}
			";

			$result = $db->query($sql);

			if ($result)
			{
				echo utf8_decode($result[0]['nombre']);
			}
			
			break;
		
		case 'consultar':
			$fecha1 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'], 1, $_REQUEST['anio']));
			$fecha2 = date('d/m/Y', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

			$condiciones = array();

			$condiciones[] = "mir.fecha BETWEEN '{$fecha1}' AND '{$fecha2}'";

			$condiciones[] = "mir.codmp = {$_REQUEST['codmp']}";

			$condiciones[] = "mir.tipo_mov = FALSE";

			$condiciones[] = "mir.descripcion != 'DIFERENCIA INVENTARIO'";
			
			$condiciones[] = "mir.descripcion NOT LIKE 'TRASPASO DE AVIO%'";
			
			if (isset($_REQUEST['cias']) && trim($_REQUEST['cias']) != '')
			{
				$cias = array();
				
				$pieces = explode(',', $_REQUEST['cias']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$cias[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$cias[] = $piece;
					}
				}
				
				if (count($cias) > 0)
				{
					$condiciones[] = 'mir.num_cia IN (' . implode(', ', $cias) . ')';
				}
			}

			if (isset($_REQUEST['pros']) && trim($_REQUEST['pros']) != '')
			{
				$pros = array();
				
				$pieces = explode(',', $_REQUEST['pros']);
				foreach ($pieces as $piece)
				{
					if (count($exp = explode('-', $piece)) > 1)
					{
						$pros[] =  implode(', ', range($exp[0], $exp[1]));
					}
					else
					{
						$pros[] = $piece;
					}
				}
				
				if (count($pros) > 0)
				{
					$condiciones[] = 'mir.num_proveedor IN (' . implode(', ', $pros) . ')';
				}
			}

			if (isset($_REQUEST['admin']) && $_REQUEST['admin'] > 0)
			{
				$condiciones[] = "idadministrador = {$_REQUEST['admin']}";
			}
			
			$sql = "
				SELECT
					mir.num_cia,
					mir.fecha,
					EXTRACT(DAY FROM mir.fecha)
						AS dia,
					SUM(mir.cantidad)
						AS cantidad
				FROM
					mov_inv_real mir
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					" . implode(' AND ', $condiciones) . "
				GROUP BY
					num_cia,
					fecha
				ORDER BY
					num_cia,
					dia
			";
			
			$result = $db->query($sql);
			
			$tpl = new TemplatePower('plantillas/bal/ReporteProductosMensualImpreso.tpl');
			$tpl->prepare();
			
			if ($result)
			{
				$tpl->newBlock('reporte');

				$tpl->assign('codmp', $_REQUEST['codmp']);

				$producto = $db->query("SELECT nombre FROM catalogo_mat_primas WHERE codmp = {$_REQUEST['codmp']}");

				$tpl->assign('producto', utf8_decode($producto[0]['nombre']));

				$tpl->assign('mes', $_meses[$_REQUEST['mes']]);
				$tpl->assign('anio', $_REQUEST['anio']);

				$dias_mes = date('j', mktime(0, 0, 0, $_REQUEST['mes'] + 1, 0, $_REQUEST['anio']));

				$datos = array();

				$num_cia = NULL;

				foreach ($result as $row)
				{
					if ($num_cia != $row['num_cia'])
					{
						$num_cia = $row['num_cia'];

						$nombre_cia = $db->query("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = {$num_cia}");

						$datos[$num_cia] = array(
							'nombre_cia'	=> utf8_decode($nombre_cia[0]['nombre_corto']),
							'surtido'		=> array_fill(1, $dias_mes, array('cantidad' => 0, 'info' => array()))
						);
					}

					if ($_REQUEST['codmp'] == 1)
					{
						$datos[$num_cia]['surtido'][$row['dia']]['cantidad'] = $row['cantidad'] / 44;
					}
					else if(in_array($_REQUEST['codmp'], array(3, 4)))
					{
						$datos[$num_cia]['surtido'][$row['dia']]['cantidad'] = $row['cantidad'] / 50;
					}
					else
					{
						$datos[$num_cia]['surtido'][$row['dia']]['cantidad'] = $row['cantidad'];
					}
				}

				$sql = "
					SELECT
						mir.num_cia,
						mir.fecha,
						EXTRACT(DAY FROM mir.fecha)
						AS dia,
						mir.num_proveedor
							AS num_pro,
						cp.nombre
							AS nombre_pro,
						mir.num_fact,
						mir.precio_unidad,
						f.total,
						fp.fecha_cheque
							AS pagado,
						ec.fecha_con
							AS cobrado
					FROM
						mov_inv_real mir
						LEFT JOIN facturas f
							USING (num_proveedor, num_fact)
						LEFT JOIN facturas_pagadas fp
							USING (num_proveedor, num_fact)
						LEFT JOIN cheques c
							ON (c.num_cia = fp.num_cia AND c.cuenta = fp.cuenta AND c.folio = fp.folio_cheque AND c.fecha = fp.fecha)
						LEFT JOIN estado_cuenta ec
							ON (ec.num_cia = fp.num_cia AND ec.cuenta = fp.cuenta AND ec.folio = fp.folio_cheque AND ec.fecha = fp.fecha_cheque)
						LEFT JOIN catalogo_proveedores cp
							ON (cp.num_proveedor = mir.num_proveedor)
						LEFT JOIN catalogo_companias cc
							ON (cc.num_cia = mir.num_cia)
					WHERE
						" . implode(' AND ', $condiciones) . "
					ORDER BY
						mir.num_cia,
						dia,
						num_pro,
						mir.num_fact
				";

				$result = $db->query($sql);

				if ($result)
				{
					foreach ($result as $row)
					{
						$datos[$row['num_cia']]['surtido'][$row['dia']]['info'][] = array(
							'num_pro'		=> $row['num_pro'],
							'nombre_pro'	=> utf8_decode($row['nombre_pro']),
							'num_fact'		=> utf8_decode($row['num_fact']),
							'precio_unidad'	=> $row['precio_unidad'],
							'total'			=> $row['total'],
							'pagado'		=> $row['pagado'],
							'cobrado'		=> $row['cobrado']
						);
					}
				}

				for ($dia = 1; $dia <= $dias_mes; $dia++)
				{
					$tpl->newBlock('dia_th');
					$tpl->assign('dia_mes', $dia);
					$tpl->assign('dia_semana', $_dias_semana[date('w', mktime(0, 0, 0, $_REQUEST['mes'], $dia, $_REQUEST['anio']))]);
				}

				$total_dia = array_fill(1, $dias_mes, 0);

				foreach ($datos as $num_cia => $datos_cia) {
					$tpl->newBlock('cia');

					$tpl->assign('num_cia', $num_cia);
					$tpl->assign('nombre_cia', $datos_cia['nombre_cia']);

					$total_cia = 0;

					foreach ($datos_cia['surtido'] as $dia => $datos_dia)
					{
						$info_tip = '<table class="info_tip">';

						$num_pro = NULL;

						foreach ($datos_dia['info'] as $info)
						{
							if ($num_pro != $info['num_pro'])
							{
								$num_pro = $info['num_pro'];

								$info_tip .= '<tr><th colspan="5">' . $info['num_pro'] . ' ' . $info['nombre_pro'] . '</th></tr>';
								$info_tip .= '<tr><th>Factura</th><th>Precio<br />unidad</th><th>Total<br />factura</th><th>Pagado</th><th>Cobrado</th></tr>';
							}

							$info_tip .= '<tr>';
							$info_tip .= '<td>' . $info['num_fact'] . '</td>';
							$info_tip .= '<td style="text-align:right;color:#0C0;">' . number_format($info['precio_unidad'], 2) . '</td>';
							$info_tip .= '<td style="text-align:right;color:#00C;">' . number_format($info['total'], 2) . '</td>';
							$info_tip .= '<td style="text-align:center;color:#C00;">' . $info['pagado'] . '</td>';
							$info_tip .= '<td style="text-align:center;color:#F30;">' . $info['cobrado'] . '</td>';
							$info_tip .= '</tr>';
						}

						$info_tip .= '</table>';

						$tpl->newBlock('dia_td');

						$tpl->assign('cantidad', $datos_dia['cantidad'] != 0 ? '<a class="info" data-info="' . htmlentities($info_tip) . '">' . number_format($datos_dia['cantidad'], 2) . '</a>' : '&nbsp;');

						$total_cia += $datos_dia['cantidad'];

						$total_dia[$dia] += $datos_dia['cantidad'];
					}

					$tpl->assign('cia.total_cia', number_format($total_cia, 2));
				}

				foreach ($total_dia as $dia => $total)
				{
					$tpl->newBlock('total_dia');
					$tpl->assign('total_dia', $total != 0 ? number_format($total, 2) : '&nbsp;');
				}

				$tpl->assign('reporte.gran_total', number_format(array_sum($total_dia), 2));
				
			}
			
			echo $tpl->getOutputContent();
			
			break;
		
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/bal/ReporteProductosMensual.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('anio', date('Y'));
$tpl->assign(date('n'), ' selected="selected"');

$sql = "
	SELECT
		idadministrador
			AS value,
		nombre_administrador
			AS text
	FROM
		catalogo_administradores
	ORDER BY
		text
";

$admins = $db->query($sql);

if ($admins)
{
	foreach ($admins as $a)
	{
		$tpl->newBlock('admin');
		$tpl->assign('value', $a['value']);
		$tpl->assign('text', utf8_decode($a['text']));
	}
}

$tpl->printToScreen();
