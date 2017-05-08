<?php
include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/cheques.inc.php');

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

function toInt($value) {
	return intval($value, 10);
}

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

//if ($_SESSION['iduser'] != 1) die('MODIFICANDO');

if (isset($_REQUEST['accion'])) {
	switch ($_REQUEST['accion']) {
		case 'modificarSeparar':
			$tpl = new TemplatePower('plantillas/cometra/GenerarArchivosCometraModificarSeparar.tpl');
			$tpl->prepare();

			$tpl->assign('index', $_REQUEST['index']);
			$tpl->assign('importe', $_REQUEST['importe']);
			$tpl->assign('separar', $_REQUEST['separar'] != 0 ? number_format($_REQUEST['separar'], 2) : '');

			echo $tpl->getOutputContent();
		break;

		case 'verificarBanco':
			$sql = '
				SELECT
					banco,
					tsreg
				FROM
					cometra
				WHERE
						tsend IS NULL
					AND
						tsreg IS NOT NULL
				LIMIT
					1
			';
			$result = $db->query($sql);

			if ($result) {
				$sql = '
					UPDATE
						cometra
					SET
						banco = ' . $result[0]['banco'] . '
					WHERE
							tsend IS NULL
						AND
							tsreg IS NULL
				';

				$db->query($sql);
			}
			else {
				$tpl = new TemplatePower('plantillas/cometra/GenerarArchivosCometraBanco.tpl');
				$tpl->prepare();

				echo $tpl->getOutputContent();
			}
		break;

		case 'actualizarBanco':
			$db->query("UPDATE cometra
			SET banco = {$_REQUEST['banco']}
			WHERE
				tsend IS NULL
				AND tsreg IS NULL");
		break;

		case 'incluirReporteServicios':
			$tpl = new TemplatePower('plantillas/cometra/GenerarArchivosCometraIncluirReporte.tpl');
			$tpl->prepare();

			echo $tpl->getOutputContent();
		break;

		case 'actualizarReporteServicios':
			$sql = '
				UPDATE
					cometra
				SET
					reporte = ' . $_REQUEST['status'] . '
				WHERE
					tsend IS NULL
					AND tsreg IS NULL
			';

			$db->query($sql);
		break;

		case 'reporteCSV':
			if (isset($_REQUEST['tsend'])) {
				$condiciones[] = "c.tsend = '{$_REQUEST['tsend']}'";
			}
			else {
				$condiciones[] = 'c.tsend IS NULL';
			}

			$condiciones[] = 'c.total != 0';

			$sql = "SELECT
				CASE
					WHEN c.banco = 1 THEN
						'BANORTE'
					WHEN c.banco = 2 THEN
						'SANTANDER'
					ELSE
						'SIN DEFINIR'
				END AS banco,
				c.comprobante,
				c.tipo_comprobante,
				cc.num_cia_primaria,
				ccp.nombre AS nombre_cia_primaria,
				CASE
					WHEN c.banco = 1 AND ccp.clabe_cuenta IS NOT NULL AND TRIM(ccp.clabe_cuenta) <> '' THEN
						ccp.clabe_cuenta
					WHEN c.banco = 2 AND ccp.clabe_cuenta2 IS NOT NULL AND TRIM(ccp.clabe_cuenta2) <> '' THEN
						ccp.clabe_cuenta2
					ELSE
						NULL
				END AS cuenta_primaria,
				COALESCE(c.num_cia_mov, c.num_cia) AS num_cia,
				cc.nombre AS nombre_cia,
				CASE
					WHEN c.banco = 1 AND cc.clabe_cuenta IS NOT NULL AND TRIM(cc.clabe_cuenta) <> '' THEN
						cc.clabe_cuenta
					WHEN c.banco = 2 AND cc.clabe_cuenta2 IS NOT NULL AND TRIM(cc.clabe_cuenta2) <> '' THEN
						cc.clabe_cuenta2
					ELSE
						NULL
				END AS cuenta,
				fecha,
				/*
				@ [24-Ago-2010] Todos los códigos 2 se cambiaran a 99 para reporte CSV
				*/
				/*
				@ [18-Ene-2012] Todos los códigos 13 se cambiaran a 1 para reporte CSV
				*/
				/*cod_mov,*/
				CASE
					WHEN cod_mov = 2 AND es_cheque = TRUE THEN
						99
					WHEN cod_mov = 13 THEN
						1
					ELSE
						cod_mov
				END AS cod_mov,
				concepto,
				importe,
				separar,
				total
			FROM
				cometra c
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = COALESCE(c.num_cia_mov, c.num_cia))
				LEFT JOIN catalogo_companias ccp ON (ccp.num_cia = cc.num_cia_primaria)
			WHERE
				" . implode(' AND ', $condiciones) . "
			ORDER BY
				c.comprobante,
				c.fecha,
				cc.num_cia_primaria,
				COALESCE(c.num_cia_mov, c.num_cia)";

			$result = $db->query($sql);

			$rows = array();
			if ($result) {
				$csv = ',"' . $result[0]['banco'] . '"' . "\n\n";

				$csv .= ',"GENERAL"' . "\n\n";

				//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
				$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

				$data = array();
				$comprobante = NULL;
				$cont = 0;
				foreach ($result as $r) {
					if ($comprobante != $r['comprobante']) {
						if ($comprobante != NULL) {
							$cont++;
						}

						$comprobante = $r['comprobante'];

						$data[$cont] = array (
							'num_cia'     => $r['num_cia'] >= 900 ? $r['num_cia'] : $r['num_cia_primaria'],
							'nombre_cia'  => $r['nombre_cia_primaria'],
							'cuenta'      => $r['cuenta_primaria'],
							'comprobante' => $comprobante,
							'tipo'        => $r['tipo_comprobante'],
							'importe'     => 0,
							'separar'     => 0,
							'total'       => 0
						);
					}

					$data[$cont]['depositos'][] = array(
						'fecha'      => $r['fecha'],
						'num_cia'    => $r['num_cia'],
						'nombre_cia' => $r['nombre_cia'],
						'cuenta'     => $r['cuenta'],
						'cod_mov'    => $r['cod_mov'] == 13 ? 1 : $r['cod_mov'],
						'concepto'   => $r['concepto'],
						'importe'    => in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['importe'] : $r['importe'],
						'separar'    => $r['separar'],
						'total'      => in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['total'] : $r['total']
					);

					$data[$cont]['fecha'] = $r['fecha'];
					$data[$cont]['importe'] += in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['importe'] : $r['importe'];
					$data[$cont]['separar'] += $r['separar'];
					$data[$cont]['total'] += in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['total'] : $r['total'];
				}

				function cmp($a, $b) {
					if ($a['num_cia'] == $b['num_cia']) {
						if ($a['fecha'] == $b['fecha']) {
							if ($a['comprobante'] == $b['comprobante']) {
								return 0;
							}
							else {
								return ($a['comprobante'] < $b['comprobante']) ? -1 : 1;
							}
						}
						else {
							return ($a['fecha'] < $b['fecha']) ? -1 : 1;
						}
					}
					else {
						return ($a['num_cia'] < $b['num_cia']) ? -1 : 1;
					}
				}

				usort($data, 'cmp');

				$importe = 0;
				$separar = 0;
				$total = 0;

				$tipo1 = array();
				$tipo2 = array();
				$tipo3 = array();
				$tipo4 = array();

				$otros = array();
				$depositos = array();
				$faltantes = array();
				$falsos = array();
				$sobrantes = array();
				$cheques = array();
				$cancelaciones = array();

				foreach ($data as $info) {
					$importe += $info['importe'];
					$separar += $info['separar'];
					$total += $info['total'];

					/*
					@ [28-Nov-2011] Separar por tipo de comprobante
					*/

					if ($info['tipo'] == 1) {
						$tipo1[] = $info;
					}

					if ($info['tipo'] == 2) {
						$tipo2[] = $info;
					}

					if ($info['tipo'] == 3) {
						$tipo3[] = $info;
					}

					if ($info['tipo'] == 4) {
						$tipo4[] = $info;
					}

					foreach ($info['depositos'] as $d) {
						switch ($d['cod_mov']) {
							case 1:
								if (stripos($d['concepto'], 'COMPLEMENTO VENTA') !== FALSE) {
									$tipo = 'COMPLEMENTO';
								}
								else if ($info['num_cia'] <= 300) {
									$tipo = 'PAN';
								}
								else if ($info['num_cia'] >= 900) {
									$tipo = 'ZAPATERIAS';
								}
							break;

							case 2:
								$tipo = 'RENTA';
							break;

							case 7:
								$tipo = 'PAGO FALTANTE';
							break;

							case 16:
								$tipo = 'POLLOS';
							break;

							case 13:
								$tipo = 'SOBRANTE';
							break;

							case 19:
								$tipo = 'FALTANTE';
							break;

							case 48:
								$tipo = 'FALSO';
							break;

							case 99:
								$tipo = 'CHEQUE';
							break;

							case 21:
								$tipo = 'CANCELACION DEPOSITO';
							break;

							default:
								$sql = '
									SELECT
										descripcion
									FROM
										catalogo_mov_santander
									WHERE
										cod_mov = ' . $d['cod_mov'] . '

									UNION

									SELECT
										descripcion
									FROM
										catalogo_mov_bancos
									WHERE
										cod_mov = ' . $d['cod_mov'] . '

									GROUP BY
										descripcion
									LIMIT
										1
								';
								$tmp = $db->query($sql);

								$tipo = $tmp[0]['descripcion'];

								/*
								*
								* @ [8-Nov-2010] El concepto por default sera el de catálogo de movimientos bancarios
								*
								$tipo = 'CODIGO ERRONEO';
								*/
						}

						$csv .= '"' . $d['num_cia'] . '",';
						$csv .= '"' . $d['nombre_cia'] . '",';
						$csv .= '"' . ($d['cuenta'] != '' ? str_pad($d['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $d['fecha'] . '",';
						$csv .= '"' . $d['cod_mov'] . '",';
						$csv .= '"' . $tipo . '",';
						$csv .= '"' . $d['concepto'] . '",';
						//$csv .= '"' . $info['comprobante'] . '",';
						$csv .= '"' . number_format($d['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						/*
						@ Separar por códigos
						*/

						if (in_array($d['cod_mov'], array(13, 19, 48, 99, 21))) {
							$otros[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (!in_array($d['cod_mov'], array(13, 19, 48, 99))) {
							$depositos[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (in_array($d['cod_mov'], array(19))) {
							$faltantes[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (in_array($d['cod_mov'], array(48))) {
							$falsos[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (in_array($d['cod_mov'], array(13))) {
							$sobrantes[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (in_array($d['cod_mov'], array(99))) {
							$cheques[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (in_array($d['cod_mov'], array(21))) {
							$cancelaciones[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}
					}

					//$csv .= ',,,,,,,"TOTAL COMPROBANTE","' . number_format($info['total'], 2, '.', ',') . '"' . "\n";
				}

				//$csv .= "\n" . ',,,,,,,"TOTAL COMPROBANTES","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
				$csv .= "\n" . ',,,,,"TOTAL COMPROBANTES","' . number_format($total, 2, '.', ',') . '"' . "\n\n";

				if (count($tipo1) > 0) {
					$csv .= ',"TOTAL COMPROBANTES (SERVIDOR)"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","COMPROBANTE","IMPORTE"' . "\n";

					$total_tipo = 0;

					foreach ($tipo1 as $info) {
						$csv .= '"' . $info['num_cia'] . '",';
						$csv .= '"' . $info['nombre_cia'] . '",';
						$csv .= '"' . str_pad($info['cuenta'], 11, '0', STR_PAD_LEFT) . '",';
						//$csv .= '"' . $info['fecha'] . '",';
						$csv .= '"' . $info['comprobante'] . '",';
						$csv .= '"' . number_format($info['total'], 2, '.', ',') . '",';
						$csv .= "\n";

						$total_tipo += $info['total'];
					}

					//$csv .= ',,,,"TOTAL GENERAL (' . count($tipo1) . ')","' . number_format($importe, 2, '.', ',') . '","' . number_format($separar, 2, '.', ',') . '","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,"TOTAL (' . count($tipo1) . ')","' . number_format($total_tipo, 2, '.', ',') . '"' . "\n\n";
				}

				if (count($tipo2) > 0) {
					$csv .= ',"TOTAL COMPROBANTES (CAPTURADOS)"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","COMPROBANTE","IMPORTE"' . "\n";

					$total_tipo = 0;

					foreach ($tipo2 as $info) {
						$csv .= '"' . $info['num_cia'] . '",';
						$csv .= '"' . $info['nombre_cia'] . '",';
						$csv .= '"' . str_pad($info['cuenta'], 11, '0', STR_PAD_LEFT) . '",';
						//$csv .= '"' . $info['fecha'] . '",';
						$csv .= '"' . $info['comprobante'] . '",';
						$csv .= '"' . number_format($info['total'], 2, '.', ',') . '",';
						$csv .= "\n";

						$total_tipo += $info['total'];
					}

					//$csv .= ',,,,"TOTAL GENERAL (' . count($tipo1) . ')","' . number_format($importe, 2, '.', ',') . '","' . number_format($separar, 2, '.', ',') . '","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,"TOTAL (' . count($tipo2) . ')","' . number_format($total_tipo, 2, '.', ',') . '"' . "\n\n";
				}

				if (count($tipo3) > 0) {
					$csv .= ',"TOTAL COMPROBANTES (ESPECIALES)"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","COMPROBANTE","IMPORTE"' . "\n";

					$total_tipo = 0;

					foreach ($tipo3 as $info) {
						$csv .= '"' . $info['num_cia'] . '",';
						$csv .= '"' . $info['nombre_cia'] . '",';
						$csv .= '"' . str_pad($info['cuenta'], 11, '0', STR_PAD_LEFT) . '",';
						//$csv .= '"' . $info['fecha'] . '",';
						$csv .= '"' . $info['comprobante'] . '",';
						$csv .= '"' . number_format($info['total'], 2, '.', ',') . '",';
						$csv .= "\n";

						$total_tipo += $info['total'];
					}

					//$csv .= ',,,,"TOTAL GENERAL (' . count($tipo1) . ')","' . number_format($importe, 2, '.', ',') . '","' . number_format($separar, 2, '.', ',') . '","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,"TOTAL (' . count($tipo3) . ')","' . number_format($total_tipo, 2, '.', ',') . '"' . "\n\n";
				}

				if (count($tipo4) > 0) {
					$csv .= ',"TOTAL COMPROBANTES (SOBRANTES, FALTANTES Y FALSOS)"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","COMPROBANTE","IMPORTE"' . "\n";

					$total_tipo = 0;

					foreach ($tipo4 as $info) {
						$csv .= '"' . $info['num_cia'] . '",';
						$csv .= '"' . $info['nombre_cia'] . '",';
						$csv .= '"' . str_pad($info['cuenta'], 11, '0', STR_PAD_LEFT) . '",';
						//$csv .= '"' . $info['fecha'] . '",';
						$csv .= '"' . $info['comprobante'] . '",';
						$csv .= '"' . number_format($info['total'], 2, '.', ',') . '",';
						$csv .= "\n";

						$total_tipo += $info['total'];
					}

					//$csv .= ',,,,"TOTAL GENERAL (' . count($tipo1) . ')","' . number_format($importe, 2, '.', ',') . '","' . number_format($separar, 2, '.', ',') . '","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,"TOTAL (' . count($tipo4) . ')","' . number_format($total_tipo, 2, '.', ',') . '"' . "\n\n";
				}

				//$csv .= ',"FALTANTES, SOBRANTES Y CHEQUES"' . "\n\n";
				//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
				//$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

				//$total_otros = 0;
				//$total_cheques = 0;
				//$cantidad_cheques = 0;
				//foreach ($otros as $o) {
					//$csv .= '"' . $o['num_cia'] . '",';
					//$csv .= '"' . $o['nombre_cia'] . '",';
					//$csv .= '"' . ($o['cuenta'] != '' ? str_pad($o['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
					//$csv .= '"' . $o['fecha'] . '",';
					//$csv .= '"' . $o['cod_mov'] . '",';
					//$csv .= '"' . $o['descripcion'] . '",';
					//$csv .= '"' . $o['concepto'] . '",';
					//$csv .= '"' . $o['comprobante'] . '",';
					//$csv .= '"' . number_format($o['importe'], 2, '.', ',') . '"';
					//$csv .= "\n";

					//$total_otros += $o['importe'];
					//$total_cheques += $o['cod_mov'] == 99 ? $o['importe'] : 0;
					//$cantidad_cheques += $o['cod_mov'] == 99 ? 1 : 0;
				//}

				//$csv .= ',,,,,,,"TOTAL DE FALTANTES, SOBRANTES Y OTROS","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				//$csv .= ',,,,,"TOTAL DE FALTANTES, SOBRANTES Y OTROS","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";

				$total_depositos = 0;

				if (count($depositos) > 0) {
					$csv .= "\n" . ',"DEPOSITOS"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($depositos as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_depositos += $mov['total'];
					}

					//$csv .= ',,,,,,,"TOTAL FALTANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL DEPOSITOS","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$total_faltantes = 0;

				if (count($faltantes) > 0) {
					$csv .= "\n" . ',"FALTANTES"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($faltantes as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_faltantes += $mov['total'];
					}

					//$csv .= ',,,,,,,"TOTAL FALTANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL FALTANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$total_falsos = 0;

				if (count($falsos) > 0) {
					$csv .= "\n" . ',"FALSOS"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($falsos as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_falsos += $mov['total'];
					}

					//$csv .= ',,,,,,,"TOTAL FALTANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL FALTANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$total_cancelaciones = 0;

				if (count($cancelaciones) > 0) {
					$csv .= "\n" . ',"CANCELACIONES"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($cancelaciones as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_cancelaciones += $mov['total'];
					}

					//$csv .= ',,,,,,,"TOTAL CANCELACIONES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL CANCELACIONES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$total_sobrantes = 0;

				if (count($sobrantes) > 0) {
					$csv .= "\n" . ',"SOBRANTES"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($sobrantes as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_sobrantes += $mov['total'];
					}

					//$csv .= ',,,,,,,"TOTAL SOBRANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL SOBRANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$total_cheques = 0;
				$cantidad_cheques = 0;

				if (count($cheques) > 0) {
					$csv .= "\n" . ',"CHEQUES"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($cheques as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_cheques += $mov['cod_mov'] == 99 ? $mov['total'] : 0;
						$cantidad_cheques += $mov['cod_mov'] == 99 ? 1 : 0;
					}

					//$csv .= ',,,,,,,"TOTAL CHEQUES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL CHEQUES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$csv .= "\n";
				$csv .= ',,,,,"DEPOSITOS","' . number_format($total_depositos, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"FALTANTES","' . number_format($total_faltantes, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"FALSOS","' . number_format($total_falsos, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"CANCELACIONES","' . number_format($total_cancelaciones, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"SOBRANTES","' . number_format($total_sobrantes, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"CHEQUES (' . $cantidad_cheques . ')","' . number_format($total_cheques, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"EFECTIVO","' . number_format($total - $total_cheques, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"TOTAL GENERAL (' . count($data) . ')","' . number_format($total, 2, '.', ',') . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=' . urlencode($result[0]['banco']) . '.CSV');

				echo $csv;
			}
		break;

		case 'archivoBanorte':
			if (isset($_REQUEST['tsend'])) {
				$condiciones[] = "c.tsend = '{$_REQUEST['tsend']}'";
			}
			else {
				$condiciones[] = 'c.tsend IS NULL';
			}

			$condiciones[] = 'banco = 1';

			$condiciones[] = 'total != 0';

			$sql = "SELECT
				CASE
					WHEN c.banco = 1 THEN
						'BANORTE'
					WHEN c.banco = 2 THEN
						'SANTANDER'
					ELSE
						'SIN DEFINIR'
				END AS banco,
				c.comprobante,
				c.tipo_comprobante,
				cc.num_cia_primaria,
				ccp.nombre AS nombre_cia_primaria,
				CASE
					WHEN c.banco = 1 AND ccp.clabe_cuenta IS NOT NULL AND TRIM(ccp.clabe_cuenta) <> '' THEN
						ccp.clabe_cuenta
					WHEN c.banco = 2 AND ccp.clabe_cuenta2 IS NOT NULL AND TRIM(ccp.clabe_cuenta2) <> '' THEN
						ccp.clabe_cuenta2
					ELSE
						NULL
				END AS cuenta_primaria,
				COALESCE(c.num_cia_mov, c.num_cia) AS num_cia,
				cc.nombre AS nombre_cia,
				CASE
					WHEN c.banco = 1 AND cc.clabe_cuenta IS NOT NULL AND TRIM(cc.clabe_cuenta) <> '' THEN
						cc.clabe_cuenta
					WHEN c.banco = 2 AND cc.clabe_cuenta2 IS NOT NULL AND TRIM(cc.clabe_cuenta2) <> '' THEN
						cc.clabe_cuenta2
					ELSE
						NULL
				END AS cuenta,
				fecha,
				/*
				@ [24-Ago-2010] Todos los códigos 2 se cambiaran a 99 para reporte CSV
				*/
				/*
				@ [18-Ene-2012] Todos los códigos 13 se cambiaran a 1 para reporte CSV
				*/
				/*cod_mov,*/
				CASE
					WHEN cod_mov = 2 AND es_cheque = TRUE THEN
						99
					WHEN cod_mov = 13 THEN
						1
					ELSE
						cod_mov
				END AS cod_mov,
				concepto,
				importe,
				separar,
				total
			FROM
				cometra c
				LEFT JOIN catalogo_companias cc ON (cc.num_cia = COALESCE(c.num_cia_mov, c.num_cia))
				LEFT JOIN catalogo_companias ccp ON (ccp.num_cia = cc.num_cia_primaria)
			WHERE
				" . implode(' AND ', $condiciones) . "
			ORDER BY
				c.comprobante,
				c.fecha,
				cc.num_cia_primaria,
				COALESCE(c.num_cia_mov, c.num_cia)";

			$result = $db->query($sql);

			$rows = array();
			if ($result) {

				$data = array();
				$comprobante = NULL;
				$cont = 0;

				$cantidad = 0;
				$total = 0;

				foreach ($result as $r) {
					if ($comprobante != $r['comprobante']) {
						if ($comprobante != NULL) {
							$cont++;
						}

						$comprobante = $r['comprobante'];

						$data[$cont] = array (
							'num_cia'     => $r['num_cia'] >= 900 ? $r['num_cia'] : $r['num_cia_primaria'],
							'nombre_cia'  => $r['nombre_cia_primaria'],
							'cuenta'      => $r['cuenta_primaria'],
							'comprobante' => $comprobante,
							'tipo'        => $r['tipo_comprobante'],
							'importe'     => 0,
							'separar'     => 0,
							'total'       => 0
						);
					}

					$data[$cont]['depositos'][] = array(
						'fecha'      => $r['fecha'],
						'num_cia'    => $r['num_cia'],
						'nombre_cia' => $r['nombre_cia'],
						'cuenta'     => $r['cuenta'],
						'cod_mov'    => $r['cod_mov'] == 13 ? 1 : $r['cod_mov'],
						'concepto'   => $r['concepto'],
						'importe'    => in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['importe'] : $r['importe'],
						'separar'    => $r['separar'],
						'total'      => in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['total'] : $r['total']
					);

					$data[$cont]['fecha'] = $r['fecha'];
					$data[$cont]['importe'] += in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['importe'] : $r['importe'];
					$data[$cont]['separar'] += $r['separar'];
					$data[$cont]['total'] += in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['total'] : $r['total'];

					$cantidad++;
					$total += in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['total'] : $r['total'];
				}

				function cmp($a, $b) {
					if ($a['num_cia'] == $b['num_cia']) {
						if ($a['fecha'] == $b['fecha']) {
							if ($a['comprobante'] == $b['comprobante']) {
								return 0;
							}
							else {
								return ($a['comprobante'] < $b['comprobante']) ? -1 : 1;
							}
						}
						else {
							return ($a['fecha'] < $b['fecha']) ? -1 : 1;
						}
					}
					else {
						return ($a['num_cia'] < $b['num_cia']) ? -1 : 1;
					}
				}

				usort($data, 'cmp');

				$D = array();

				$cont = 0;

				foreach ($data as $com) {
					foreach ($com['depositos'] as $dep) {
						/*
						@@ Consecutivo
						@
						@  Tipo:        Numérico
						@  Longitud:    Variable
						@  Descripción: Consecutivo de registros por archivo. Inicia en 1 para cada archivo
						*/
						$D[$cont] = ($cont + 1);
						/*
						@@ Cuenta de Cheques Cliente
						@
						@  Tipo:        Numérico
						@  Longitud:    Variable
						@  Descripción: Cuenta de Cheques a la que se le efectuara el cargo (Cambiar por Depósito)
						*/
						$D[$cont] .= ',' . $dep['cuenta'];
						/*
						@@ # Remesa o Comprobante
						@
						@  Tipo:        Numérico
						@  Longitud:    Variable
						@  Descripción: Número de remesa o comprobante del envio
						*/
						$D[$cont] .= ',' . $com['comprobante'];
						/*
						@@ # Referencia
						@
						@  Tipo:        Numérico
						@  Longitud:    15
						@  Descripción: Referencia del cliente
						*/
						$D[$cont] .= ',' . str_pad('', 15, '0') . ',01,';
						/*
						@@ Divisa
						@
						@  Tipo:        Numérico
						@  Longitud:    2
						@  Descripción: 01=Moneda Nacional 02=Dolares
						*/
						$D[$cont] .= ',01';
						/*
						@@ Importe Efectivo
						@
						@  Tipo:        Numérico
						@  Longitud:    Variable
						@  Descripción: Importe de efectivo en MN - Real (14 enteros 2 decimales)
						*/
						if (!in_array($dep['cod_mov'], array(13, 19, 48, 21))) {
							$D[$cont] .= ',' . number_format($dep['total'], 2, '.', '');
						}
						else {
							$D[$cont] .= ',0.00';
						}
						/*
						@@ Importe Chqs. Banorte
						@
						@  Tipo:        Numérico
						@  Longitud:    Variable
						@  Descripción: Importe total de los cheques Banorte contenidos en el depósito MN
						*/
						$D[$cont] .= ',0.00';
						/*
						@@ Importe Chqs. Otros Bancos
						@
						@  Tipo:        Numérico
						@  Longitud:    Variable
						@  Descripción: Importe total de cheques Otros Bancos contenidos en el depósito MN
						*/
						$D[$cont] .= ',0.00';
						/*
						@@ Importe del Depósito (FICHA)
						@
						@  Tipo:        Numérico
						@  Longitud:    Variable
						@  Descripción: Importe descrito en la ficha de depósito en base a la divisa del depósito
						*/
						$D[$cont] .= ',0.00';
						/*
						@@ Importe Diferencia
						@
						@  Tipo:        Numérico
						@  Longitud:    Variable
						@  Descripción: Importe de la Diferencia en base a la divisa del depósito (10 enteros 2 decimales)
						*/
						if (in_array($dep['cod_mov'], array(13, 19, 48, 21))) {
							$D[$cont] .= ',' . number_format($dep['total'], 2, '.', '');
						}
						else {
							$D[$cont] .= ',0.00';
						}
						/*
						@@ Tipo de diferencia
						@
						@  Tipo:        Alfabético
						@  Longitud:    1
						@  Descripción: F-Faltante, S-Sobrante, N-Ninguna
						*/
						if (in_array($dep['cod_mov'], array(13))) {
							$D[$cont] .= ',S';
						}
						else if (in_array($dep['cod_mov'], array(19, 48, 21))) {
							$D[$cont] .= ',F';
						}
						else {
							$D[$cont] .= ',N';
						}
						/*
						@@ Denominación
						@
						@  Tipo:        Numérico
						@  Longitud:    Variable
						@  Descripción: Denominación del Billete o Morralla
						*/
						$D[$cont] .= ',0';
						/*
						@@ Cantidad
						@
						@  Tipo:        Numérico
						@  Longitud:    Variable
						@  Descripción: Número de Billetes o Monedas de la denominación
						*/
						$D[$cont] .= ',0';
						/*
						@@ Importe
						@
						@  Tipo:        Numérico
						@  Longitud:    Variable
						@  Descripción: Importe total de la denominación
						*/
						$D[$cont] .= ',0.00';

						$cont++;
					}
				}

				$sql = '
					SELECT
						MAX(consecutivo) + 1
							AS consecutivo
					FROM
						banorte_consecutivo_depositos
					WHERE
						tsgen::DATE >= \'' . date('d/m/Y') . '\'
				';

				$tmp = $db->query($sql);

				if ($tmp[0]['consecutivo'] > 0) {
					$consecutivo = $tmp[0]['consecutivo'];
				}
				else {
					$consecutivo = 1;
				}

				/*
				@@ Tipo de archivo
				@
				@  Tipo:        Alfabético
				@  Longitud:    1
				@  Descripción: Constante D
				*/
				$H = 'D';
				/*
				@@ Fecha
				@
				@  Tipo:        Numérico
				@  Longitud:    8
				@  Descripción: Formato aaaammdd
				*/
				$H .= ',' . date('Ymd');
				/*
				@@ Proveedor
				@
				@  Tipo:        Numérico
				@  Longitud:    5
				@  Descripción: Número de Proveedor con el que existe en el sistema SAE
				*/
				$H .= ',00000';
				/*
				@@ Número de archivo
				@
				@  Tipo:        Numérico
				@  Longitud:    Variable
				@  Descripción: Número consecutivo diario de archivo por Proveedor
				*/
				$H .= ',' . $consecutivo;
				/*
				@@ Número Total Depósitos
				@
				@  Tipo:        Numérico
				@  Longitud:    Variable
				@  Descripción: Número total de depósitos contenidos en el archivo
				*/
				$H .= ',' . $cantidad;
				/*
				@@ Importe Total de los Depósitos
				@
				@  Tipo:        Numérico
				@  Longitud:    Variable
				@  Descripción: Importe total de los depósitos (10 enteros 2 decimales)
				*/
				$H .= ',' . number_format($total, 2, '.', '');

				$sql = '
					INSERT INTO
						banorte_consecutivo_depositos
							(
								iduser,
								tsgen,
								consecutivo,
								cantidad
							)
						VALUES
							(
								' . $_SESSION['iduser'] . ',
								now(),
								' . $consecutivo . ',
								' . $cantidad . '
							)
				' . ";\n";

				$db->query($sql);

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename="D00000' . date('ymd') . '_' . str_pad($consecutivo, 2, '0', STR_PAD_LEFT) . '.txt"');

				echo $H . "\r\n" . implode("\r\n", $D);
			}
		break;

		case 'registrarSistema':
			$sql = '
				SELECT
					banco
				FROM
					cometra
				LIMIT
					1
			';
			$banco = $db->query($sql);

			if (!$banco || $banco[0]['banco'] == '') {
				echo -1;
			}
			else {
				$sql = '';

				/*
				* [03-Nov-2011] Actualizar datos
				*/

				foreach ($_REQUEST['data'] as $data) {
					$datos = json_decode($data);

					if (!$datos->registrado) {
						$sql .= '
							UPDATE
								cometra
							SET
								separar = ' . abs($datos->separar) . ',
								total = ' . abs($datos->total) . ',
								num_cia_mov = COALESCE((SELECT cia_principal FROM cuentas_mancomunadas WHERE cia_secundaria = cometra.num_cia AND tsbaja IS NULL), num_cia)
							WHERE
								id = ' . $datos->id . '
						' . ";\n";
					}
				}

				$sql .= '
					INSERT INTO
						estado_cuenta
							(
								num_cia,
								fecha,
								tipo_mov,
								importe,
								cod_mov,
								concepto,
								cuenta,
								comprobante,
								iduser,
								local,
								fecha_renta,
								idreciborenta,
								idarrendatario,
								num_cia_sec
							)
						SELECT
							num_cia_mov,
							fecha,
							CASE
								WHEN cod_mov IN (19, 48, 21) THEN
									\'TRUE\'::boolean
								ELSE
									\'FALSE\'::boolean
							END,
							total,
							cod_mov,
							CASE
								WHEN cod_mov = 2 THEN
									concepto
								WHEN TRIM(concepto) <> \'\' THEN
									concepto || \' [\' || comprobante || \']\'
								WHEN cod_mov IN (1, 16) THEN
									\'DEPOSITO COMETRA \' || \' [\' || comprobante || \']\'
								WHEN cod_mov = 7 THEN
									\'PAGO FALTANTE \' || \' [\' || comprobante || \']\'
								WHEN cod_mov = 13 THEN
									\'SOBRANTE CAJA GENERAL \' || \' [\' || comprobante || \']\'
								WHEN cod_mov = 19 THEN
									\'FAL REP CAJA \' || \' [\' || comprobante || \']\'
								WHEN cod_mov = 48 THEN
									\'FALTANTE (FALSO) \' || \' [\' || comprobante || \']\'
								WHEN cod_mov = 99 THEN
									\'CHEQUE \' || \' [\' || comprobante || \']\'
								WHEN cod_mov = 21 THEN
									\'CANC DEP\' || \' [\' || comprobante || \']\'
								ELSE
									/*\'CODIGO ERRONEO \' || \' [\' || comprobante || \']\'*/
									/*
									*@@ [8-Nov-2010] Obtener el concepto del catálogo de movimientos bancarios
									*/
									(
										SELECT
											descripcion
										FROM
											catalogo_mov_santander
										WHERE
											cod_mov = cometra.cod_mov

										UNION

										SELECT
											descripcion
										FROM
											catalogo_mov_bancos
										WHERE
											cod_mov = cometra.cod_mov

										GROUP BY
											descripcion
										LIMIT
											1
									)
							END,
							banco,
							comprobante,
							' . $_SESSION['iduser'] . ',
							local,
							fecha_renta,
							idreciborenta,
							idarrendatario,
							num_cia
						FROM
							cometra
						WHERE
							tsreg IS NULL
							AND tsend IS NULL
							AND total > 0
						ORDER BY
							num_cia,
							fecha,
							importe
				' . ";\n";

				$sql .= '
					INSERT INTO
						faltantes_cometra
							(
								num_cia,
								fecha,
								importe,
								deposito,
								tipo,
								descripcion,
								imp,
								implis,
								comprobante,
								importe_comprobante,
								num_cia_mov
							)
						SELECT
							num_cia_mov,
							fecha,
							total,
							(
								SELECT
									SUM(
										CASE
											WHEN cod_mov IN (19, 48, 21) THEN
												-importe
											ELSE
												importe
										END
									)
								FROM
									cometra
								WHERE
									comprobante = c.comprobante
							),
							CASE
								WHEN cod_mov = 13 THEN
									TRUE
								ELSE
									FALSE
							END,
							CASE
								WHEN cod_mov = 13 THEN
									\'SOBRANTE CAJA GENERAL \' || comprobante
								WHEN cod_mov = 19 THEN
									\'FAL REP CAJA \' || comprobante
								WHEN cod_mov = 48 THEN
									\'FALTANTE (FALSO) \' || comprobante
								WHEN cod_mov = 21 THEN
									\'CANC DEP \' || comprobante
							END,
							\'FALSE\',
							\'TRUE\',
							comprobante,
							(
								SELECT
									SUM(
										importe
									)
								FROM
									cometra
								WHERE
									comprobante = c.comprobante
									AND cod_mov NOT IN (13, 19, 48, 21)
							),
							num_cia
						FROM
							cometra c
						WHERE
								tsreg IS NULL
							AND
								tsend IS NULL
							AND
								cod_mov
									IN
										(
											13,
											19,
											48,
											21
										)
						ORDER BY
							num_cia,
							fecha,
							importe
				' . ";\n";

				$sql .= '
					UPDATE
						otros_depositos
					SET
						acumulado = FALSE
					WHERE
						iduser = ' . $_SESSION['iduser'] . '
						AND acumulado = TRUE
				' . ";\n";

				$sql .= '
					INSERT INTO
						otros_depositos
							(
								num_cia,
								fecha,
								concepto,
								comprobante,
								importe,
								fecha_cap,
								acumulado,
								iduser,
								tsins,
								num_cia_mov
							)
						SELECT
							num_cia,
							fecha,
							fecha || \' (\' || comprobante || \')\',
							comprobante,
							separar,
							now()::DATE,
							TRUE,
							' . $_SESSION['iduser'] . ',
							NOW(),
							num_cia_mov
						FROM
							cometra c
						WHERE
							tsreg IS NULL
							AND tsend IS NULL
							AND separar > 0
						ORDER BY
							num_cia,
							fecha,
							importe
				' . ";\n";

				/*
				* [02-Ene-2012] Los depósitos 1 y 16 con concepto 'COMPLEMENTO VENTA' se ingresaran a otros depósitos como negativos
				*/
				$sql .= '
					INSERT INTO
						otros_depositos
							(
								num_cia,
								fecha,
								concepto,
								comprobante,
								importe,
								fecha_cap,
								acumulado,
								iduser,
								tsins,
								num_cia_mov
							)
						SELECT
							num_cia,
							fecha,
							\'COMPLEMENTO VENTA (\' || comprobante || \')\',
							comprobante,
							-total,
							now()::DATE,
							TRUE,
							' . $_SESSION['iduser'] . ',
							NOW(),
							num_cia_mov
						FROM
							cometra c
						WHERE
							tsreg IS NULL
							AND tsend IS NULL
							AND total > 0
							AND cod_mov IN (1, 16)
							AND concepto = \'COMPLEMENTO VENTA\'
						ORDER BY
							num_cia,
							fecha,
							importe
				' . ";\n";

				$sql .= '
					UPDATE
						cometra
					SET
						iduser_reg = ' . $_SESSION['iduser'] . ',
						tsreg = now()
					WHERE
						tsreg IS NULL
						AND tsend IS NULL
				' . ";\n";

				$db->query($sql);
			}
		break;

		case 'imprimirComprobantes':
			if (isset($_REQUEST['tsend'])) {
				$condiciones[] = 'tsend = \'' . $_REQUEST['tsend'] . '\'';
			}
			else {
				$condiciones[] = 'tsend IS NULL';
			}

			$condiciones[] = 'tsreg IS NOT NULL';

			$sql = '
				SELECT
					CASE
						WHEN c.banco = 1 THEN
							\'BANORTE\'
						WHEN c.banco = 2 THEN
							\'SANTANDER\'
						ELSE
							\'SIN DEFINIR\'
					END
						AS banco,
					c.comprobante,
					c.tipo_comprobante,
					cc.num_cia_primaria,
					ccp.nombre
						AS nombre_cia_primaria,
					CASE
						WHEN c.banco = 1 AND ccp.clabe_cuenta IS NOT NULL AND TRIM(ccp.clabe_cuenta) <> \'\' THEN
							ccp.clabe_cuenta
						WHEN c.banco = 2 AND ccp.clabe_cuenta2 IS NOT NULL AND TRIM(ccp.clabe_cuenta2) <> \'\' THEN
							ccp.clabe_cuenta2
						ELSE
							NULL
					END
						AS cuenta_primaria,
					TRIM(regexp_replace(ccp.direccion, \'\s+\', \' \', \'g\'))
						AS domicilio_primaria,
					ccp.cliente_cometra,
					c.num_cia,
					cc.nombre
						AS nombre_cia,
					CASE
						WHEN c.banco = 1 AND cc.clabe_cuenta IS NOT NULL AND TRIM(cc.clabe_cuenta) <> \'\' THEN
							cc.clabe_cuenta
						WHEN c.banco = 2 AND cc.clabe_cuenta2 IS NOT NULL AND TRIM(cc.clabe_cuenta2) <> \'\' THEN
							cc.clabe_cuenta2
						ELSE
							NULL
					END
						AS cuenta,
					TRIM(regexp_replace(cc.direccion, \'\s+\', \' \', \'g\'))
						AS domicilio,
					fecha - INTERVAL \'1 DAY\'
						AS fecha,
					CASE
						WHEN cod_mov = 2 AND es_cheque = \'TRUE\' THEN
							99
						WHEN cod_mov = 13 THEN
							1
						ELSE
							cod_mov
					END
						AS cod_mov,
					concepto,
					importe,
					separar,
					total
				FROM
					cometra c
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
					LEFT JOIN catalogo_companias ccp
						ON (ccp.num_cia = cc.num_cia_primaria)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					c.comprobante,
					c.fecha,
					cc.num_cia_primaria,
					c.num_cia
			';

			$result = $db->query($sql);

			if ($result) {
				$data = array();
				$comprobante = NULL;
				$cont = 0;
				foreach ($result as $r) {
					if ($comprobante != $r['comprobante']) {
						if ($comprobante != NULL) {
							$cont++;
						}

						$comprobante = $r['comprobante'];

						$data[$cont] = array (
							'num_cia'         => $r['num_cia'] >= 900 ? $r['num_cia'] : $r['num_cia_primaria'],
							'nombre_cia'      => $r['nombre_cia_primaria'],
							'domicilio'       => $r['domicilio_primaria'],
							'banco'           => $r['banco'],
							'cuenta'          => $r['cuenta_primaria'],
							'cliente_cometra' => $r['cliente_cometra'],
							'comprobante'     => $comprobante,
							'tipo'            => $r['tipo_comprobante'],
							'importe'         => 0,
							'separar'         => 0,
							'total'           => 0
						);
					}

					$data[$cont]['depositos'][] = array(
						'fecha'      => $r['fecha'],
						'num_cia'    => $r['num_cia'],
						'nombre_cia' => $r['nombre_cia'],
						'cuenta'     => $r['cuenta'],
						'cod_mov'    => $r['cod_mov'] == 13 ? 1 : $r['cod_mov'],
						'concepto'   => $r['concepto'],
						'importe'    => in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['importe'] : $r['importe'],
						'separar'    => $r['separar'],
						'total'      => in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['total'] : $r['total']
					);

					$data[$cont]['fecha'] = $r['fecha'];
					$data[$cont]['importe'] += in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['importe'] : $r['importe'];
					$data[$cont]['separar'] += $r['separar'];
					$data[$cont]['total'] += in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['total'] : $r['total'];
				}

//				function cmp($a, $b) {
//					if ($a['num_cia'] == $b['num_cia']) {
//						if ($a['fecha'] == $b['fecha']) {
//							if ($a['comprobante'] == $b['comprobante']) {
//								return 0;
//							}
//							else {
//								return ($a['comprobante'] < $b['comprobante']) ? -1 : 1;
//							}
//						}
//						else {
//							return ($a['fecha'] < $b['fecha']) ? -1 : 1;
//						}
//					}
//					else {
//						return ($a['num_cia'] < $b['num_cia']) ? -1 : 1;
//					}
//				}
//
//				usort($data, 'cmp');

				shuffle($data);

				$string = '';

				foreach ($data as $d) {
					$piezas = explode('/', $d['fecha']);

					$string .= str_pad('', 4, "\n");
					$string .= str_pad('', 1, ' ') . date('d-m-y', mktime(0, 0, 0, $piezas[1], $piezas[0], $piezas[2]));
					$string .= str_pad('', 33, ' ') . 'X';
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 3, ' ') . str_pad($d['cliente_cometra'], 8, '0') . str_pad('', 20, ' ') . 'X';
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 6, ' ') . substr($d['num_cia'] . '-' . $d['nombre_cia'], 0, 64);
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 4, ' ') . substr($d['domicilio'], 0, 66);
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 5, ' ') . '1' . str_pad('', 17, ' ') . 'X';
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 4, ' ') . substr(num2string($d['total']), 0, 66);
					$string .= str_pad('', 3, "\n");
					$string .= number_format($d['total'], 2);
					$string .= str_pad('', 4, "\n");
					$string .= str_pad('', 4, ' ') . $d['banco'] . ' CAJA GENERAL';
					$string .= str_pad('', 2, "\n");
					$string .= str_pad('', 4, ' ') . 'CALLE IXNAHUALTONGO NO.129, COL. SAN LORENZO BOTURINI,';
					$string .= str_pad('', 1, "\n");
					$string .= str_pad('', 4, ' ') . 'DEL. VENUSTIANO CARRANZA, CP.15820, MEXICO, D.F.';
					$string .= str_pad('', 27, "\n");
				}

				shell_exec("chmod ugo=rwx pcl");

				$fp = fopen('pcl/ComprobantesCometra.txt', 'w');

				fwrite($fp, $string);

				fclose($fp);

				shell_exec('lpr -l -P cometra pcl/ComprobantesCometra.txt');

				shell_exec("chmod ugo=r pcl");
			}
		break;

		case 'enviarEmailFalantes':
			if (isset($_REQUEST['tsend'])) {
				$condiciones[] = 'tsend = \'' . $_REQUEST['tsend'] . '\'';
			}
			else {
				$condiciones[] = 'tsend IS NULL';
			}

			$condiciones[] = 'comprobante IN (
				SELECT
					comprobante
				FROM
					cometra
				WHERE
					cod_mov IN (13, 19, 48, 99)
					AND tsend ' . (isset($_REQUEST['tsend']) ? '= \'' . $_REQUEST['tsend'] . '\'' : 'IS NULL') . '
			)';

			$sql = '
				SELECT
					CASE
						WHEN c.banco = 1 THEN
							\'BANORTE\'
						WHEN c.banco = 2 THEN
							\'SANTANDER\'
						ELSE
							\'SIN DEFINIR\'
					END
						AS
							banco,
					c.comprobante,
					c.tipo_comprobante,
					cc.num_cia_primaria,
					ccp.nombre
						AS
							nombre_cia_primaria,
					CASE
						WHEN c.banco = 1 AND ccp.clabe_cuenta IS NOT NULL AND TRIM(ccp.clabe_cuenta) <> \'\' THEN
							ccp.clabe_cuenta
						WHEN c.banco = 2 AND ccp.clabe_cuenta2 IS NOT NULL AND TRIM(ccp.clabe_cuenta2) <> \'\' THEN
							ccp.clabe_cuenta2
						ELSE
							NULL
					END
						AS
							cuenta_primaria,
					c.num_cia,
					cc.nombre
						AS
							nombre_cia,
					CASE
						WHEN c.banco = 1 AND cc.clabe_cuenta IS NOT NULL AND TRIM(cc.clabe_cuenta) <> \'\' THEN
							cc.clabe_cuenta
						WHEN c.banco = 2 AND cc.clabe_cuenta2 IS NOT NULL AND TRIM(cc.clabe_cuenta2) <> \'\' THEN
							cc.clabe_cuenta2
						ELSE
							NULL
					END
						AS
							cuenta,
					fecha,
					cod_mov,
					concepto,
					importe,
					separar,
					total
				FROM
						cometra c
					LEFT JOIN
						catalogo_companias cc
							USING
								(
									num_cia
								)
					LEFT JOIN
						catalogo_companias ccp
							ON
								(
									ccp.num_cia = cc.num_cia_primaria
								)
				WHERE
					' . implode(' AND ', $condiciones) . '
				ORDER BY
					c.comprobante,
					c.fecha,
					cc.num_cia_primaria,
					c.num_cia
			';

			$result = $db->query($sql);

			$rows = array();
			if ($result) {
				$data = array();
				$comprobante = NULL;
				$cont = 0;
				foreach ($result as $r) {
					if ($comprobante != $r['comprobante']) {
						if ($comprobante != NULL) {
							$cont++;
						}

						$comprobante = $r['comprobante'];

						$data[$cont] = array (
							'num_cia'     => $r['num_cia'] >= 900 ? $r['num_cia'] : $r['num_cia_primaria'],
							'nombre_cia'  => $r['nombre_cia_primaria'],
							'cuenta'      => $r['cuenta_primaria'],
							'comprobante' => $comprobante,
							'tipo'        => $r['tipo_comprobante'],
							'importe'     => 0,
							'separar'     => 0,
							'total'       => 0
						);
					}

					$data[$cont]['depositos'][] = array(
						'fecha'      => $r['fecha'],
						'num_cia'    => $r['num_cia'],
						'nombre_cia' => $r['nombre_cia'],
						'cuenta'     => $r['cuenta'],
						'cod_mov'    => $r['cod_mov'] == 13 ? 1 : $r['cod_mov'],
						'concepto'   => $r['concepto'],
						'importe'    => in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['importe'] : $r['importe'],
						'separar'    => $r['separar'],
						'total'      => in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['total'] : $r['total']
					);

					$data[$cont]['fecha'] = $r['fecha'];
					$data[$cont]['importe'] += in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['importe'] : $r['importe'];
					$data[$cont]['separar'] += $r['separar'];
					$data[$cont]['total'] += in_array($r['cod_mov'], array(19, 48, 21)) ? -$r['total'] : $r['total'];
				}

				function cmp($a, $b) {
					if ($a['num_cia'] == $b['num_cia']) {
						if ($a['fecha'] == $b['fecha']) {
							if ($a['comprobante'] == $b['comprobante']) {
								return 0;
							}
							else {
								return ($a['comprobante'] < $b['comprobante']) ? -1 : 1;
							}
						}
						else {
							return ($a['fecha'] < $b['fecha']) ? -1 : 1;
						}
					}
					else {
						return ($a['num_cia'] < $b['num_cia']) ? -1 : 1;
					}
				}

				usort($data, 'cmp');

				$importe = 0;
				$separar = 0;
				$total = 0;

				$tipo1 = array();
				$tipo2 = array();
				$tipo3 = array();
				$tipo4 = array();

				$otros = array();
				$depositos = array();
				$faltantes = array();
				$falsos = array();
				$sobrantes = array();
				$cheques = array();
				$cancelaciones = array();

				foreach ($data as $info) {
					$importe += $info['importe'];
					$separar += $info['separar'];
					$total += $info['total'];

					/*
					@ [28-Nov-2011] Separar por tipo de comprobante
					*/

					if ($info['tipo'] == 1) {
						$tipo1[] = $info;
					}

					if ($info['tipo'] == 2) {
						$tipo2[] = $info;
					}

					if ($info['tipo'] == 3) {
						$tipo3[] = $info;
					}

					if ($info['tipo'] == 4) {
						$tipo4[] = $info;
					}

					foreach ($info['depositos'] as $d) {
						switch ($d['cod_mov']) {
							case 1:
								if ($info['num_cia'] <= 300) {
									$tipo = 'PAN';
								}
								else if ($info['num_cia'] >= 900) {
									$tipo = 'ZAPATERIAS';
								}
							break;

							case 2:
								$tipo = 'RENTA';
							break;

							case 7:
								$tipo = 'PAGO FALTANTE';
							break;

							case 16:
								$tipo = 'POLLOS';
							break;

							case 13:
								$tipo = 'SOBRANTE';
							break;

							case 19:
								$tipo = 'FALTANTE';
							break;

							case 48:
								$tipo = 'FALSO';
							break;

							case 99:
								$tipo = 'CHEQUE';
							break;

							case 21:
								$tipo = 'CANC DEP';
							break;

							default:
								$sql = '
									SELECT
										descripcion
									FROM
										catalogo_mov_santander
									WHERE
										cod_mov = ' . $d['cod_mov'] . '

									UNION

									SELECT
										descripcion
									FROM
										catalogo_mov_bancos
									WHERE
										cod_mov = ' . $d['cod_mov'] . '

									GROUP BY
										descripcion
									LIMIT
										1
								';
								$tmp = $db->query($sql);

								$tipo = $tmp[0]['descripcion'];

								/*
								*
								* @ [8-Nov-2010] El concepto por default sera el de catálogo de movimientos bancarios
								*
								$tipo = 'CODIGO ERRONEO';
								*/
						}

						$csv .= '"' . $d['num_cia'] . '",';
						$csv .= '"' . $d['nombre_cia'] . '",';
						$csv .= '"' . ($d['cuenta'] != '' ? str_pad($d['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $d['fecha'] . '",';
						$csv .= '"' . $d['cod_mov'] . '",';
						$csv .= '"' . $tipo . '",';
						$csv .= '"' . $d['concepto'] . '",';
						//$csv .= '"' . $info['comprobante'] . '",';
						$csv .= '"' . number_format($d['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						/*
						@ Separar por códigos
						*/

						if (in_array($d['cod_mov'], array(13, 19, 48, 99, 21))) {
							$otros[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (!in_array($d['cod_mov'], array(13, 19, 48, 99))) {
							$depositos[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (in_array($d['cod_mov'], array(19))) {
							$faltantes[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (in_array($d['cod_mov'], array(48))) {
							$falsos[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (in_array($d['cod_mov'], array(13))) {
							$sobrantes[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (in_array($d['cod_mov'], array(99))) {
							$cheques[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}

						if (in_array($d['cod_mov'], array(21))) {
							$cancelaciones[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo));
						}
					}

					//$csv .= ',,,,,,,"TOTAL COMPROBANTE","' . number_format($info['total'], 2, '.', ',') . '"' . "\n";
				}

				//$csv .= "\n" . ',,,,,,,"TOTAL COMPROBANTES","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
				$csv .= "\n" . ',,,,,"TOTAL COMPROBANTES","' . number_format($total, 2, '.', ',') . '"' . "\n\n";

				if (count($tipo1) > 0) {
					$csv .= ',"TOTAL COMPROBANTES (SERVIDOR)"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","COMPROBANTE","IMPORTE"' . "\n";

					$total_tipo = 0;

					foreach ($tipo1 as $info) {
						$csv .= '"' . $info['num_cia'] . '",';
						$csv .= '"' . $info['nombre_cia'] . '",';
						$csv .= '"' . str_pad($info['cuenta'], 11, '0', STR_PAD_LEFT) . '",';
						//$csv .= '"' . $info['fecha'] . '",';
						$csv .= '"' . $info['comprobante'] . '",';
						$csv .= '"' . number_format($info['total'], 2, '.', ',') . '",';
						$csv .= "\n";

						$total_tipo += $info['total'];
					}

					//$csv .= ',,,,"TOTAL GENERAL (' . count($tipo1) . ')","' . number_format($importe, 2, '.', ',') . '","' . number_format($separar, 2, '.', ',') . '","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,"TOTAL (' . count($tipo1) . ')","' . number_format($total_tipo, 2, '.', ',') . '"' . "\n\n";
				}

				if (count($tipo2) > 0) {
					$csv .= ',"TOTAL COMPROBANTES (CAPTURADOS)"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","COMPROBANTE","IMPORTE"' . "\n";

					$total_tipo = 0;

					foreach ($tipo2 as $info) {
						$csv .= '"' . $info['num_cia'] . '",';
						$csv .= '"' . $info['nombre_cia'] . '",';
						$csv .= '"' . str_pad($info['cuenta'], 11, '0', STR_PAD_LEFT) . '",';
						//$csv .= '"' . $info['fecha'] . '",';
						$csv .= '"' . $info['comprobante'] . '",';
						$csv .= '"' . number_format($info['total'], 2, '.', ',') . '",';
						$csv .= "\n";

						$total_tipo += $info['total'];
					}

					//$csv .= ',,,,"TOTAL GENERAL (' . count($tipo1) . ')","' . number_format($importe, 2, '.', ',') . '","' . number_format($separar, 2, '.', ',') . '","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,"TOTAL (' . count($tipo2) . ')","' . number_format($total_tipo, 2, '.', ',') . '"' . "\n\n";
				}

				if (count($tipo3) > 0) {
					$csv .= ',"TOTAL COMPROBANTES (ESPECIALES)"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","COMPROBANTE","IMPORTE"' . "\n";

					$total_tipo = 0;

					foreach ($tipo3 as $info) {
						$csv .= '"' . $info['num_cia'] . '",';
						$csv .= '"' . $info['nombre_cia'] . '",';
						$csv .= '"' . str_pad($info['cuenta'], 11, '0', STR_PAD_LEFT) . '",';
						//$csv .= '"' . $info['fecha'] . '",';
						$csv .= '"' . $info['comprobante'] . '",';
						$csv .= '"' . number_format($info['total'], 2, '.', ',') . '",';
						$csv .= "\n";

						$total_tipo += $info['total'];
					}

					//$csv .= ',,,,"TOTAL GENERAL (' . count($tipo1) . ')","' . number_format($importe, 2, '.', ',') . '","' . number_format($separar, 2, '.', ',') . '","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,"TOTAL (' . count($tipo3) . ')","' . number_format($total_tipo, 2, '.', ',') . '"' . "\n\n";
				}

				if (count($tipo4) > 0) {
					$csv .= ',"TOTAL COMPROBANTES (SOBRANTES, FALTANTES Y FALSOS)"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","COMPROBANTE","IMPORTE"' . "\n";

					$total_tipo = 0;

					foreach ($tipo4 as $info) {
						$csv .= '"' . $info['num_cia'] . '",';
						$csv .= '"' . $info['nombre_cia'] . '",';
						$csv .= '"' . str_pad($info['cuenta'], 11, '0', STR_PAD_LEFT) . '",';
						//$csv .= '"' . $info['fecha'] . '",';
						$csv .= '"' . $info['comprobante'] . '",';
						$csv .= '"' . number_format($info['total'], 2, '.', ',') . '",';
						$csv .= "\n";

						$total_tipo += $info['total'];
					}

					//$csv .= ',,,,"TOTAL GENERAL (' . count($tipo1) . ')","' . number_format($importe, 2, '.', ',') . '","' . number_format($separar, 2, '.', ',') . '","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,"TOTAL (' . count($tipo4) . ')","' . number_format($total_tipo, 2, '.', ',') . '"' . "\n\n";
				}

				//$csv .= ',"FALTANTES, SOBRANTES Y CHEQUES"' . "\n\n";
				//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
				//$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

				//$total_otros = 0;
				//$total_cheques = 0;
				//$cantidad_cheques = 0;
				//foreach ($otros as $o) {
					//$csv .= '"' . $o['num_cia'] . '",';
					//$csv .= '"' . $o['nombre_cia'] . '",';
					//$csv .= '"' . ($o['cuenta'] != '' ? str_pad($o['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
					//$csv .= '"' . $o['fecha'] . '",';
					//$csv .= '"' . $o['cod_mov'] . '",';
					//$csv .= '"' . $o['descripcion'] . '",';
					//$csv .= '"' . $o['concepto'] . '",';
					//$csv .= '"' . $o['comprobante'] . '",';
					//$csv .= '"' . number_format($o['importe'], 2, '.', ',') . '"';
					//$csv .= "\n";

					//$total_otros += $o['importe'];
					//$total_cheques += $o['cod_mov'] == 99 ? $o['importe'] : 0;
					//$cantidad_cheques += $o['cod_mov'] == 99 ? 1 : 0;
				//}

				//$csv .= ',,,,,,,"TOTAL DE FALTANTES, SOBRANTES Y OTROS","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				//$csv .= ',,,,,"TOTAL DE FALTANTES, SOBRANTES Y OTROS","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";

				$total_depositos = 0;

				if (count($depositos) > 0) {
					$csv .= "\n" . ',"DEPOSITOS"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($depositos as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_depositos += $mov['total'];
					}

					//$csv .= ',,,,,,,"TOTAL FALTANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL DEPOSITOS","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$total_faltantes = 0;

				if (count($faltantes) > 0) {
					$csv .= "\n" . ',"FALTANTES"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($faltantes as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_faltantes += $mov['total'];
					}

					//$csv .= ',,,,,,,"TOTAL FALTANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL FALTANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$total_falsos = 0;

				if (count($falsos) > 0) {
					$csv .= "\n" . ',"FALSOS"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($falsos as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_falsos += $mov['total'];
					}

					//$csv .= ',,,,,,,"TOTAL FALTANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL FALTANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$total_cancelaciones = 0;

				if (count($cancelaciones) > 0) {
					$csv .= "\n" . ',"CANCELACIONES"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($cancelaciones as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_cancelaciones += $mov['total'];
					}

					//$csv .= ',,,,,,,"TOTAL CANCELACIONES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL CANCELACIONES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$total_sobrantes = 0;

				if (count($sobrantes) > 0) {
					$csv .= "\n" . ',"SOBRANTES"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($sobrantes as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_sobrantes += $mov['total'];
					}

					//$csv .= ',,,,,,,"TOTAL SOBRANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL SOBRANTES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$total_cheques = 0;
				$cantidad_cheques = 0;

				if (count($cheques) > 0) {
					$csv .= "\n" . ',"CHEQUES"' . "\n\n";
					//$csv .= '"#","COMPAÑIA","CUENTA","FECHA","CODIGO","TIPO","CONCEPTO","COMPROBANTE","IMPORTE"' . "\n";
					$csv .= '"#","COMPAÑIA","CUENTA","CODIGO","TIPO","CONCEPTO","IMPORTE"' . "\n";

					$total_otros = 0;

					foreach ($cheques as $mov) {
						$csv .= '"' . $mov['num_cia'] . '",';
						$csv .= '"' . $mov['nombre_cia'] . '",';
						$csv .= '"' . ($mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '') . '",';
						//$csv .= '"' . $mov['fecha'] . '",';
						$csv .= '"' . $mov['cod_mov'] . '",';
						$csv .= '"' . $mov['descripcion'] . '",';
						$csv .= '"' . $mov['concepto'] . '",';
						//$csv .= '"' . $mov['comprobante'] . '",';
						$csv .= '"' . number_format($mov['total'], 2, '.', ',') . '"';
						$csv .= "\n";

						$total_otros += $mov['total'];

						$total_cheques += $mov['cod_mov'] == 99 ? $mov['total'] : 0;
						$cantidad_cheques += $mov['cod_mov'] == 99 ? 1 : 0;
					}

					//$csv .= ',,,,,,,"TOTAL CHEQUES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
					$csv .= ',,,,,"TOTAL CHEQUES","' . number_format($total_otros, 2, '.', ',') . '"' . "\n\n";
				}

				$csv .= "\n";
				$csv .= ',,,,,"DEPOSITOS","' . number_format($total_depositos, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"FALTANTES","' . number_format($total_faltantes, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"FALSOS","' . number_format($total_falsos, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"CANCELACIONES","' . number_format($total_cancelaciones, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"SOBRANTES","' . number_format($total_sobrantes, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"CHEQUES (' . $cantidad_cheques . ')","' . number_format($total_cheques, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"EFECTIVO","' . number_format($total - $total_cheques, 2, '.', ',') . '"' . "\n";
				$csv .= ',,,,,"TOTAL GENERAL (' . count($data) . ')","' . number_format($total, 2, '.', ',') . '"' . "\n";

				header('Content-Type: application/download');
				header('Content-Disposition: attachment; filename=' . urlencode($result[0]['banco']) . '.CSV');

				echo $csv;
			}
		break;

		case 'reporteFaltantes':
			$sql = '
				SELECT
					num_cia,
					nombre
						AS nombre_cia,
					CASE
						WHEN banco = 1 THEN
							clabe_cuenta
						WHEN banco = 2 THEN
							clabe_cuenta2
						ELSE
							NULL
					END
						AS cuenta,
					fecha,
					cod_mov
						AS codigo,
					CASE
						WHEN cod_mov = 13 THEN
							\'SOBRANTES\'
						WHEN cod_mov = 19 THEN
							\'FALTANTES\'
						WHEN cod_mov = 48 THEN
							\'FALTANTES (FALSOS)\'
					END
						AS tipo,
					concepto,
					comprobante,
					CASE
						WHEN cod_mov = 13 THEN
							total
						ELSE
							-total
					END
						AS importe
				FROM
					cometra c
					LEFT JOIN catalogo_companias cc
						USING (num_cia)
				WHERE
					tsend IS NULL
					AND cod_mov IN (13, 19, 48)
				ORDER BY
					codigo,
					num_cia,
					fecha
			';

			$tpl = new TemplatePower('plantillas/cometra/ReporteSobrantesFaltantes.tpl');
			$tpl->prepare();

			$result = $db->query($sql);

			if ($result) {
				$tpl->newBlock('reporte');

				$codigo = NULL;

				foreach ($result as $rec) {
					if ($codigo != $rec['codigo']) {
						$codigo = $rec['codigo'];

						$tpl->newBlock('tipo');
						$tpl->assign('tipo', $rec['tipo']);

						$total = 0;
					}

					$tpl->newBlock('row');

					$tpl->assign('num_cia', $rec['num_cia']);
					$tpl->assign('nombre_cia', utf8_encode($rec['nombre_cia']));
					$tpl->assign('cuenta', $rec['cuenta'] != '' ? $rec['cuenta'] : '&nbsp;');
					$tpl->assign('fecha', $rec['fecha']);
					$tpl->assign('concepto', $rec['concepto'] != '' ? utf8_encode($rec['concepto']) : '');
					$tpl->assign('comprobante', $rec['comprobante']);
					$tpl->assign('importe', '<span class="' . ($rec['importe'] > 0 ? 'blue' : 'red') . '">' . number_format($rec['importe'], 2) . '</span>');

					$total += $rec['importe'];

					$tpl->assign('tipo.total', number_format($total, 2));
				}
			}

			$tpl->printToScreen();
		break;

	}

	die;
}

$tpl = new TemplatePower('plantillas/cometra/GenerarArchivosCometra.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$sql = '
	UPDATE
		cometra
	SET
		importe = CASE
				WHEN importe - TRUNC(importe) BETWEEN 0.3 AND 0.7 THEN
					TRUNC(importe) + 0.5
				WHEN importe - TRUNC(importe) > 0.7 THEN
					CEIL(importe)
				ELSE
					FLOOR(importe)
			END
	WHERE
		tsend IS NULL
		AND tsreg IS NULL
		AND cod_mov IN (13, 19, 48)
';

$db->query($sql);

$sql = '
	SELECT
		id,
		CASE
			WHEN c.banco = 1 THEN
				\'BANORTE\'
			WHEN c.banco = 2 THEN
				\'SANTANDER\'
			ELSE
				\'SIN DEFINIR\'
		END
			AS banco,
		c.comprobante,
		CASE
			WHEN c.tipo_comprobante IN (1, 2) THEN
				1
			ELSE
				2
		END
			AS "tipo_comprobante",
		cc.num_cia_primaria,
		ccp.nombre
			AS nombre_cia_primaria,
		CASE
			WHEN c.banco = 1 AND ccp.clabe_cuenta IS NOT NULL AND TRIM(ccp.clabe_cuenta) <> \'\' THEN
				ccp.clabe_cuenta
			WHEN c.banco = 2 AND ccp.clabe_cuenta2 IS NOT NULL AND TRIM(ccp.clabe_cuenta2) <> \'\' THEN
				ccp.clabe_cuenta2
			ELSE
				NULL
		END
			AS cuenta_primaria,
		c.num_cia,
		cc.nombre
			AS
				nombre_cia,
		CASE
			WHEN c.banco = 1 AND cc.clabe_cuenta IS NOT NULL AND TRIM(cc.clabe_cuenta) <> \'\' THEN
				cc.clabe_cuenta
			WHEN c.banco = 2 AND cc.clabe_cuenta2 IS NOT NULL AND TRIM(cc.clabe_cuenta2) <> \'\' THEN
				cc.clabe_cuenta2
			ELSE
				NULL
		END
			AS cuenta,
		fecha,
		/*
		@ [24-Ago-2010] Todos los códigos 2 se cambiaran a 99 para reporte CSV
		*/
		/*cod_mov,*/
		CASE
			WHEN cod_mov = 2 AND es_cheque = \'TRUE\' THEN
				99
			ELSE
				cod_mov
		END
			AS cod_mov,
		concepto,
		importe,
		separar,
		total,
		CASE
			WHEN tsreg IS NULL THEN
				FALSE
			WHEN (
				SELECT
					id
				FROM
					cometra
				WHERE
					num_cia = c.num_cia
					AND fecha = c.fecha
					AND cod_mov IN (1, 16)
					AND separar > 0
					AND tsreg IS NOT NULL
				LIMIT
					1
			) IS NOT NULL THEN
				TRUE
			ELSE
				TRUE
		END
			AS registrado,
		no_separar
	FROM
		cometra c
		LEFT JOIN catalogo_companias cc
			USING (num_cia)
		LEFT JOIN catalogo_companias ccp
			ON (ccp.num_cia = cc.num_cia_primaria)
	WHERE
		tsend IS NULL
	ORDER BY
		"tipo_comprobante",
		c.comprobante,
		c.fecha,
		cc.num_cia_primaria,
		c.num_cia
';

$result = $db->query($sql);

if ($result) {
	/*
	* [26-Dic-2011] Días festivos
	*/
	$dias_festivos = array(
		array(12, 24),
		array(12, 25),
		array(12, 31),
		array(1, 1),
		array(1, 6),
		array(5, 10)
	);

	/*
	* [03-Nov-2011] Actualizar totales
	*/

	$db->query('
		UPDATE
			cometra
		SET
			total = importe - separar
		WHERE
			tsend IS NULL
			AND (
				total = 0
				OR importe - separar <> total
			)
	');

	/*
	* [01-Nov-2011] Obtener importes a separar
	*/

	$fecha1 = date('j') > 6 ? date('01/m/Y') : date('d/m/Y', mktime(0, 0, 0, date('n') - 1, 1));
	$fecha2 = date('j') > 6 ? date('d/m/Y') : date('d/m/Y', mktime(0, 0, 0, date('n'), 0));

	$sql = '
		SELECT
			num_cia,
			importe,
			porcentaje,
			COALESCE((
				SELECT
					AVG(efectivo)
				FROM
					total_panaderias
				WHERE
					num_cia = cs.num_cia
					AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
					AND efe = TRUE
					AND exp = TRUE
					AND gas = TRUE
					AND pro = TRUE
					AND pas = TRUE
			), (
				SELECT
					AVG(efectivo)
				FROM
					total_companias
				WHERE
					num_cia = cs.num_cia
					AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
			), 0)
				AS promedio
		FROM
			cometra_separacion cs
		WHERE
			tsbaja IS NULL
		ORDER BY
			num_cia
	';

	$tmp = $db->query($sql);

	$separar = array();

	if ($tmp) {
		foreach ($tmp as $t) {
			$separar[$t['num_cia']] = array(
				'importe'    => floatval($t['importe']),
				'porcentaje' => floatval($t['porcentaje']),
				'promedio'   => floatval($t['promedio']),
				'fechas'     => array()
			);
		}
	}

	/*
	* [26-Dic-2011] Obtener importes a separar (días festivos)
	*/

	$sql = '
		SELECT
			num_cia,
			importe,
			porcentaje,
			COALESCE((
				SELECT
					AVG(efectivo)
				FROM
					total_panaderias
				WHERE
					num_cia = cs.num_cia
					AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
					AND efe = TRUE
					AND exp = TRUE
					AND gas = TRUE
					AND pro = TRUE
					AND pas = TRUE
			), (
				SELECT
					AVG(efectivo)
				FROM
					total_companias
				WHERE
					num_cia = cs.num_cia
					AND fecha BETWEEN \'' . $fecha1 . '\' AND \'' . $fecha2 . '\'
			), 0)
				AS promedio,
			mes,
			dia
		FROM
			cometra_separacion_festivos cs
		WHERE
			tsbaja IS NULL
		ORDER BY
			num_cia
	';

	$tmp = $db->query($sql);

	$separar_festivos = array();

	if ($tmp) {
		foreach ($tmp as $t) {
			$separar_festivos[$t['num_cia']][$t['mes']][$t['dia']] = array(
				'importe'    => floatval($t['importe']),
				'porcentaje' => floatval($t['porcentaje']),
				'promedio'   => floatval($t['promedio'])
			);
		}
	}

	/*
	* [27-ago-2012] Obtener importes a separar (zapaterias)
	*/

	$sql = '
		SELECT
			num_cia,
			fecha,
			importe
		FROM
			cometra_separacion_zapaterias csz
		WHERE
			tsbaja IS NULL
			AND (num_cia, fecha) IN (
				SELECT
					num_cia,
					fecha
				FROM
					cometra
				WHERE
					tsend IS NULL
					AND num_cia BETWEEN 900 AND 998
			)
		ORDER BY
			num_cia,
			fecha
	';

	$tmp = $db->query($sql);

	$separar_zapaterias = array();

	if ($tmp) {
		foreach ($tmp as $t) {
			$separar_zapaterias[$t['num_cia']][$t['fecha']] = array(
				'importe'  => floatval($t['importe']),
				'separado' => FALSE
			);
		}
	}

	$tpl->assign('banco', $result[0]['banco']);

	$data = array();
	$comprobante = NULL;
	$cont = 0;
	foreach ($result as $r) {
		if ($comprobante != $r['comprobante']) {
			if ($comprobante != NULL) {
				$cont++;
			}

			$comprobante = $r['comprobante'];

			$data[$cont] = array (
				'num_cia'          => $r['num_cia'] >= 900 ? $r['num_cia'] : $r['num_cia_primaria'],
				'nombre_cia'       => utf8_encode($r['nombre_cia_primaria']),
				'cuenta'           => $r['cuenta_primaria'],
				'comprobante'      => $comprobante,
				'tipo_comprobante' => $r['tipo_comprobante'],
				'importe'          => 0,
				'separar'          => 0,
				'total'            => 0,
				'diferencias'      => 0
			);

			$cont_depositos = 0;
		}

		$data[$cont]['depositos'][$cont_depositos] = array(
			'id'         => $r['id'],
			'fecha'      => $r['fecha'],
			'num_cia'    => intval($r['num_cia']),
			'nombre_cia' => utf8_encode($r['nombre_cia']),
			'cuenta'     => intval($r['cuenta']),
			'cod_mov'    => intval($r['cod_mov']),
			'concepto'   => $r['concepto'],
			'importe'    => in_array($r['cod_mov'], array(19, 48, 21)) ? -floatval($r['importe']) : floatval($r['importe']),
			'separar'    => floatval($r['separar']),
			'total'      => $r['total'] != 0 ? (in_array($r['cod_mov'], array(19, 48, 21)) ? -floatval($r['total']) : floatval($r['total'])) : (in_array($r['cod_mov'], array(19, 48, 21)) ? -floatval($r['importe']) : (floatval($r['separar']) > 0 ? floatval($r['total']) : $r['importe'])),
			'registrado' => $r['registrado'] == 't' ? TRUE : FALSE
		);

		if ($data[$cont]['depositos'][$cont_depositos]['separar'] > 0) {
			$separar[$r['num_cia']]['fechas'][$r['fecha']] = TRUE;
		}

		list($dia, $mes, $anio) = array_map('toInt', explode('/', $r['fecha']));

		$fecha = array(
			$mes,
			$dia
		);

		/*
		* [04-Ago-2014] Si la bandera de 'no_separar' es 'TRUE' no se aplicará separación de depósitos
		*/
		if ($r['no_separar'] == 't')
		{
			// No hacer nada
		}
		/*
		* [27-Ago-2012] Separación para zapaterias
		*/
		else if ($r['num_cia'] >= 900) {
			if (!$data[$cont]['depositos'][$cont_depositos]['registrado']
				&& stripos($data[$cont]['depositos'][$cont_depositos]['concepto'], 'COMPLEMENTO VENTA') === FALSE
				&& in_array($r['cod_mov'], array(1, 16))
				&& isset($separar_zapaterias[$r['num_cia']][$r['fecha']])
				&& !$separar_zapaterias[$r['num_cia']][$r['fecha']]['separado']) {
				if ($separar_zapaterias[$r['num_cia']][$r['fecha']]['importe'] > 0 && $data[$cont]['depositos'][$cont_depositos]['importe'] >= $separar_zapaterias[$r['num_cia']][$r['fecha']]['importe']) {
					$data[$cont]['depositos'][$cont_depositos]['separar'] = $separar_zapaterias[$r['num_cia']][$r['fecha']]['importe'];
					$data[$cont]['depositos'][$cont_depositos]['total'] = $data[$cont]['depositos'][$cont_depositos]['importe'] - $separar_zapaterias[$r['num_cia']][$r['fecha']]['importe'];

					$separar_zapaterias[$r['num_cia']][$r['fecha']]['separado'] = TRUE;
				}
			}
		}
		/*
		* [26-Dic-2011] Separación para días festivos
		*/
		else if (in_array($fecha, $dias_festivos)) {
			if (!$data[$cont]['depositos'][$cont_depositos]['registrado']
				&& stripos($data[$cont]['depositos'][$cont_depositos]['concepto'], 'COMPLEMENTO VENTA') === FALSE
				&& in_array($r['cod_mov'], array(1, 16))
				&& isset($separar_festivos[$r['num_cia']][$fecha[0]][$fecha[1]])) {
				if ($separar_festivos[$r['num_cia']][$fecha[0]][$fecha[1]]['importe'] > 0 && $data[$cont]['depositos'][$cont_depositos]['importe'] > $separar_festivos[$r['num_cia']][$fecha[0]][$fecha[1]]['importe']) {
					$data[$cont]['depositos'][$cont_depositos]['separar'] = $separar_festivos[$r['num_cia']][$fecha[0]][$fecha[1]]['importe'];
					$data[$cont]['depositos'][$cont_depositos]['total'] = $data[$cont]['depositos'][$cont_depositos]['importe'] - $separar_festivos[$r['num_cia']][$fecha[0]][$fecha[1]]['importe'];
				}
				else if ($separar_festivos[$r['num_cia']][$fecha[0]][$fecha[1]]['porcentaje'] > 0) {
					$data[$cont]['depositos'][$cont_depositos]['separar'] = $separar_festivos[$r['num_cia']][$fecha[0]][$fecha[1]]['porcentaje'] < 100 ? round($data[$cont]['depositos'][$cont_depositos]['importe'] * $separar_festivos[$r['num_cia']][$fecha[0]][$fecha[1]]['porcentaje'] / 100, -2) : $data[$cont]['depositos'][$cont_depositos]['importe'];
					$data[$cont]['depositos'][$cont_depositos]['total'] = $data[$cont]['depositos'][$cont_depositos]['importe'] - $data[$cont]['depositos'][$cont_depositos]['separar'];
				}
			}
		}
		else {
			if (!$data[$cont]['depositos'][$cont_depositos]['registrado']
				&& stripos($data[$cont]['depositos'][$cont_depositos]['concepto'], 'COMPLEMENTO VENTA') === FALSE
				&& in_array($r['cod_mov'], array(1, 16))
				&& isset($separar[$r['num_cia']])
				&& (!isset($separar[$r['num_cia']]['fechas'][$r['fecha']]) || ($r['num_cia'] > 300 && $r['num_cia'] < 599))
				/*&& (
					!in_array($dia, array(10, 20, 30))
					|| (
						in_array($dia, array(10, 20, 30))
						&& $data[$cont]['depositos'][$cont_depositos]['importe'] >= $separar[$r['num_cia']]['promedio']
					)
				)*/) {
				if ($separar[$r['num_cia']]['importe'] > 0 && $data[$cont]['depositos'][$cont_depositos]['importe'] > $separar[$r['num_cia']]['importe']) {
					$data[$cont]['depositos'][$cont_depositos]['separar'] = $separar[$r['num_cia']]['importe'];
					$data[$cont]['depositos'][$cont_depositos]['total'] = $data[$cont]['depositos'][$cont_depositos]['importe'] - $separar[$r['num_cia']]['importe'];

					// $separar[$r['num_cia']]['fechas'][$r['fecha']] = TRUE;
				}
				else if ($separar[$r['num_cia']]['porcentaje'] > 0) {
					if ($separar[$r['num_cia']]['porcentaje'] >= 100) {
						$data[$cont]['depositos'][$cont_depositos]['separar'] = $data[$cont]['depositos'][$cont_depositos]['importe'];
					}
					else {
						$data[$cont]['depositos'][$cont_depositos]['separar'] = round($data[$cont]['depositos'][$cont_depositos]['importe'] * $separar[$r['num_cia']]['porcentaje'] / 100, -2, PHP_ROUND_HALF_DOWN);
					}

					$data[$cont]['depositos'][$cont_depositos]['total'] = $data[$cont]['depositos'][$cont_depositos]['importe'] - $data[$cont]['depositos'][$cont_depositos]['separar'];

					// $separar[$r['num_cia']]['fechas'][$r['fecha']] = TRUE;
				}
			}
		}

		$data[$cont]['fecha'] = $r['fecha'];
		$data[$cont]['importe'] += $data[$cont]['depositos'][$cont_depositos]['importe'];
		$data[$cont]['separar'] += $data[$cont]['depositos'][$cont_depositos]['separar'];
		$data[$cont]['total'] += $data[$cont]['depositos'][$cont_depositos]['total'];

		if (in_array($r['cod_mov'], array(13, 19, 48, 99))) {
			$data[$cont]['diferencias'] = 1;
		}

		$cont_depositos++;
	}

	function cmp($a, $b) {
		if ($a['diferencias'] == $b['diferencias']) {
			if ($a['tipo_comprobante'] == $b['tipo_comprobante']) {
				if ($a['num_cia'] == $b['num_cia']) {
					if ($a['fecha'] == $b['fecha']) {
						if ($a['comprobante'] == $b['comprobante']) {
							return 0;
						}
						else {
							return ($a['comprobante'] < $b['comprobante']) ? -1 : 1;
						}
					}
					else {
						return ($a['fecha'] < $b['fecha']) ? -1 : 1;
					}
				}
				else {
					return ($a['num_cia'] < $b['num_cia']) ? -1 : 1;
				}
			}
			else {
				return ($a['tipo_comprobante'] < $b['tipo_comprobante']) ? -1 : 1;
			}
		}
		else {
			return ($a['diferencias'] < $b['diferencias']) ? -1 : 1;
		}
	}

	usort($data, 'cmp');

	$color = FALSE;

	$importe = 0;
	$separar = 0;
	$total = 0;

	$otros = array();
	$faltantes = array();
	$falsos = array();
	$sobrantes = array();
	$cheques = array();
	$cancelaciones = array();

	$index = 0;

	foreach ($data as $info) {
		$tpl->newBlock('comprobante');
		$tpl->assign('comprobante', $info['comprobante']);
		$tpl->assign('importe', number_format($info['importe'], 2, '.', ','));
		$tpl->assign('separar', $info['separar'] != 0 ? number_format($info['separar'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('total', number_format($info['total'], 2, '.', ','));

		$importe += $info['importe'];
		$separar += $info['separar'];
		$total += $info['total'];

		foreach ($info['depositos'] as $d) {
			switch ($d['cod_mov']) {
				case 1:
					if ($d['concepto'] == 'COMPLEMENTO VENTA') {
						$tipo = 'COMPLEMENTO';
					}
					else if ($info['num_cia'] <= 300) {
						$tipo = 'PAN';
					}
					else if ($info['num_cia'] >= 900) {
						$tipo = 'ZAPATERIAS';
					}

					$color_importe = '';
				break;

				case 2:
					$tipo = 'RENTA';

					$color_importe = ' class="blue"';
				break;

				case 7:
					$tipo = 'PAGO FALT.';

					$color_importe = ' class="blue"';
				break;

				case 16:
					$tipo = 'POLLOS';

					$color_importe = '';
				break;

				case 13:
					$tipo = 'SOBRANTE';

					$color_importe = ' class="blue"';
				break;

				case 19:
					$tipo = 'FALTANTE';

					$color_importe = ' class="red"';
				break;

				case 48:
					$tipo = 'FALSO';

					$color_importe = ' class="red"';
				break;

				case 99:
					$tipo = 'CHEQUE';

					$color_importe = ' class="green"';
				break;

				case 21:
					$tipo = 'CANC DEP';

					$color_importe = ' class="red"';
				break;

				default:
					$sql = '
						SELECT
							descripcion
						FROM
							catalogo_mov_santander
						WHERE
							cod_mov = ' . $d['cod_mov'] . '

						UNION

						SELECT
							descripcion
						FROM
							catalogo_mov_bancos
						WHERE
							cod_mov = ' . $d['cod_mov'] . '

						GROUP BY
							descripcion
						LIMIT
							1
					';
					$tmp = $db->query($sql);

					$tipo = $tmp[0]['descripcion'];

					$color_importe = ' class="blue"';

					/*
					*
					* @ [8-Nov-2010] El concepto por default sera el de catálogo de movimientos bancarios
					*
					$tipo = '<span style="color:#C00;">CODIGO ERRONEO</span>';

					$color_importe = ' class="red"';
					*/
			}

			$tpl->newBlock('deposito');
			$tpl->assign('index', $index);
			$tpl->assign('id', $d['id']);
			$tpl->assign('data', htmlentities(json_encode($d)));
			$tpl->assign('color', $color ? 'on' : 'off');
			$tpl->assign('num_cia', $d['num_cia']);
			$tpl->assign('nombre_cia', $d['nombre_cia']);
			$tpl->assign('cuenta', $d['cuenta'] != '' ? str_pad($d['cuenta'], 11, '0', STR_PAD_LEFT) : '&nbsp;');
			$tpl->assign('fecha', $d['fecha']);
			$tpl->assign('cod_mov', $d['cod_mov']);
			$tpl->assign('descripcion', $tipo);
			$tpl->assign('concepto', trim($d['concepto']) != '' ? trim($d['concepto']) : '&nbsp;');
			$tpl->assign('color_importe', $color_importe);
			$tpl->assign('importe', number_format($d['importe'], 2, '.', ','));
			$tpl->assign('separar', $d['separar'] != 0 ? number_format($d['separar'], 2, '.', ',') :'&nbsp;');
			$tpl->assign('total', $d['total'] != 0 ? number_format($d['total'], 2, '.', ',') : '&nbsp;');
			$tpl->assign('comprobante', $info['comprobante']);
			$tpl->assign('tipo_comprobante', $info['tipo_comprobante'] == 2 ? ' class="green"' : '');

			$color = !$color;

			$index++;

			if (in_array($d['cod_mov'], array(13, 19, 48, 99, 21))) {
				$otros[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo, 'color_importe' => $color_importe));
			}

			if (in_array($d['cod_mov'], array(19))) {
				$faltantes[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo, 'color_importe' => $color_importe));
			}

			if (in_array($d['cod_mov'], array(48))) {
				$falsos[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo, 'color_importe' => $color_importe));
			}

			if (in_array($d['cod_mov'], array(13))) {
				$sobrantes[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo, 'color_importe' => $color_importe));
			}

			if (in_array($d['cod_mov'], array(99))) {
				$cheques[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo, 'color_importe' => $color_importe));
			}

			if (in_array($d['cod_mov'], array(21))) {
				$cancelaciones[] = array_merge(array('comprobante' => $info['comprobante']), $d, array('descripcion' => $tipo, 'color_importe' => $color_importe));
			}
		}

		$tpl->assign('comprobante.color', $color ? 'on' : 'off');

		$color = !$color;
	}

	$color = FALSE;
	foreach ($data as $info) {
		$tpl->newBlock('row');
		$tpl->assign('color', $color ? 'on' : 'off');
		$tpl->assign('num_cia', $info['num_cia']);
		$tpl->assign('nombre_cia', $info['nombre_cia']);
		$tpl->assign('cuenta', str_pad($info['cuenta'], 11, '0', STR_PAD_LEFT));
		$tpl->assign('fecha', $info['fecha']);
		$tpl->assign('comprobante', $info['comprobante']);
		$tpl->assign('tipo_comprobante', in_array($info['tipo_comprobante'], array(3)) ? ' class="green"' : '');
		$tpl->assign('importe', number_format($info['importe'], 2, '.', ','));
		$tpl->assign('separar', $info['separar'] != 0 ? number_format($info['separar'], 2, '.', ',') : '&nbsp;');
		$tpl->assign('total', number_format($info['total'], 2, '.', ','));

		$color = !$color;
	}

	$tpl->assign('_ROOT.importe', number_format($importe, 2, '.', ','));
	$tpl->assign('_ROOT.separar', $separar != 0 ? number_format($separar, 2, '.', ',') : '&nbsp;');
	$tpl->assign('_ROOT.total', number_format($total, 2, '.', ','));
	$tpl->assign('_ROOT.num_comprobantes', number_format(count($data), 0, '.', ','));

	$color = FALSE;
	$total_otros = 0;
	$total_cheques = 0;
	$cantidad_cheques = 0;
	foreach ($otros as $o) {
		$tpl->newBlock('otro');
		$tpl->assign('color', $color ? 'on' : 'off');
		$tpl->assign('num_cia', $o['num_cia']);
		$tpl->assign('nombre_cia', $o['nombre_cia']);
		$tpl->assign('cuenta', $o['cuenta'] != '' ? str_pad($o['cuenta'], 11, '0', STR_PAD_LEFT) : '&nbsp;');
		$tpl->assign('fecha', $o['fecha']);
		$tpl->assign('cod_mov', $o['cod_mov']);
		$tpl->assign('descripcion', $o['descripcion']);
		$tpl->assign('concepto', trim($o['concepto']) != '' ? trim($o['concepto']) : '&nbsp;');
		$tpl->assign('color_importe', $o['color_importe']);
		$tpl->assign('importe', number_format($o['importe'], 2, '.', ','));
		$tpl->assign('comprobante', $o['comprobante']);

		$total_otros += $o['importe'];
		$total_cheques += $o['cod_mov'] == 99 ? $o['importe'] : 0;
		$cantidad_cheques += $o['cod_mov'] == 99 ? 1 : 0;

		$tpl->assign('_ROOT.total_otros', number_format($total_otros, 2, '.', ','));

		$color = !$color;
	}

	if (count($faltantes) > 0) {
		$tpl->newBlock('otros');
		$tpl->assign('otros', 'FALTANTES');

		$color = FALSE;

		$total_otros = 0;

		foreach ($faltantes as $mov) {
			$tpl->newBlock('mov');
			$tpl->assign('color', $color ? 'on' : 'off');
			$tpl->assign('num_cia', $mov['num_cia']);
			$tpl->assign('nombre_cia', $mov['nombre_cia']);
			$tpl->assign('cuenta', $mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '&nbsp;');
			$tpl->assign('fecha', $mov['fecha']);
			$tpl->assign('cod_mov', $mov['cod_mov']);
			$tpl->assign('descripcion', $mov['descripcion']);
			$tpl->assign('concepto', trim($mov['concepto']) != '' ? trim($mov['concepto']) : '&nbsp;');
			$tpl->assign('color_importe', $mov['color_importe']);
			$tpl->assign('importe', number_format($mov['importe'], 2, '.', ','));
			$tpl->assign('comprobante', $mov['comprobante']);

			$total_otros += $mov['importe'];

			$tpl->assign('otros.total', number_format($total_otros, 2, '.', ','));

			$color = !$color;
		}
	}

	if (count($falsos) > 0) {
		$tpl->newBlock('otros');
		$tpl->assign('otros', 'FALSOS');

		$color = FALSE;

		$total_otros = 0;

		foreach ($falsos as $mov) {
			$tpl->newBlock('mov');
			$tpl->assign('color', $color ? 'on' : 'off');
			$tpl->assign('num_cia', $mov['num_cia']);
			$tpl->assign('nombre_cia', $mov['nombre_cia']);
			$tpl->assign('cuenta', $mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '&nbsp;');
			$tpl->assign('fecha', $mov['fecha']);
			$tpl->assign('cod_mov', $mov['cod_mov']);
			$tpl->assign('descripcion', $mov['descripcion']);
			$tpl->assign('concepto', trim($mov['concepto']) != '' ? trim($mov['concepto']) : '&nbsp;');
			$tpl->assign('color_importe', $mov['color_importe']);
			$tpl->assign('importe', number_format($mov['importe'], 2, '.', ','));
			$tpl->assign('comprobante', $mov['comprobante']);

			$total_otros += $mov['importe'];

			$tpl->assign('otros.total', number_format($total_otros, 2, '.', ','));

			$color = !$color;
		}
	}

	if (count($cancelaciones) > 0) {
		$tpl->newBlock('otros');
		$tpl->assign('otros', 'CANCELACION DE DEPOSITOS');

		$color = FALSE;

		$total_otros = 0;

		foreach ($cancelaciones as $mov) {
			$tpl->newBlock('mov');
			$tpl->assign('color', $color ? 'on' : 'off');
			$tpl->assign('num_cia', $mov['num_cia']);
			$tpl->assign('nombre_cia', $mov['nombre_cia']);
			$tpl->assign('cuenta', $mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '&nbsp;');
			$tpl->assign('fecha', $mov['fecha']);
			$tpl->assign('cod_mov', $mov['cod_mov']);
			$tpl->assign('descripcion', $mov['descripcion']);
			$tpl->assign('concepto', trim($mov['concepto']) != '' ? trim($mov['concepto']) : '&nbsp;');
			$tpl->assign('color_importe', $mov['color_importe']);
			$tpl->assign('importe', number_format($mov['importe'], 2, '.', ','));
			$tpl->assign('comprobante', $mov['comprobante']);

			$total_otros += $mov['importe'];

			$tpl->assign('otros.total', number_format($total_otros, 2, '.', ','));

			$color = !$color;
		}
	}

	if (count($sobrantes) > 0) {
		$tpl->newBlock('otros');
		$tpl->assign('otros', 'SOBRANTES');

		$color = FALSE;

		$total_otros = 0;

		foreach ($sobrantes as $mov) {
			$tpl->newBlock('mov');
			$tpl->assign('color', $color ? 'on' : 'off');
			$tpl->assign('num_cia', $mov['num_cia']);
			$tpl->assign('nombre_cia', $mov['nombre_cia']);
			$tpl->assign('cuenta', $mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '&nbsp;');
			$tpl->assign('fecha', $mov['fecha']);
			$tpl->assign('cod_mov', $mov['cod_mov']);
			$tpl->assign('descripcion', $mov['descripcion']);
			$tpl->assign('concepto', trim($mov['concepto']) != '' ? trim($mov['concepto']) : '&nbsp;');
			$tpl->assign('color_importe', $mov['color_importe']);
			$tpl->assign('importe', number_format($mov['importe'], 2, '.', ','));
			$tpl->assign('comprobante', $mov['comprobante']);

			$total_otros += $mov['importe'];

			$tpl->assign('otros.total', number_format($total_otros, 2, '.', ','));

			$color = !$color;
		}
	}

	if (count($cheques) > 0) {
		$tpl->newBlock('otros');
		$tpl->assign('otros', 'CHEQUES');

		$color = FALSE;

		$total_otros = 0;

		foreach ($cheques as $mov) {
			$tpl->newBlock('mov');
			$tpl->assign('color', $color ? 'on' : 'off');
			$tpl->assign('num_cia', $mov['num_cia']);
			$tpl->assign('nombre_cia', $mov['nombre_cia']);
			$tpl->assign('cuenta', $mov['cuenta'] != '' ? str_pad($mov['cuenta'], 11, '0', STR_PAD_LEFT) : '&nbsp;');
			$tpl->assign('fecha', $mov['fecha']);
			$tpl->assign('cod_mov', $mov['cod_mov']);
			$tpl->assign('descripcion', $mov['descripcion']);
			$tpl->assign('concepto', trim($mov['concepto']) != '' ? trim($mov['concepto']) : '&nbsp;');
			$tpl->assign('color_importe', $mov['color_importe']);
			$tpl->assign('importe', number_format($mov['importe'], 2, '.', ','));
			$tpl->assign('comprobante', $mov['comprobante']);

			$total_otros += $mov['importe'];

			$tpl->assign('otros.total', number_format($total_otros, 2, '.', ','));

			$color = !$color;
		}
	}

	$tpl->assign('_ROOT.num_cheques', $cantidad_cheques);
	$tpl->assign('_ROOT.cheques', number_format($total_cheques, 2, '.', ','));
	$tpl->assign('_ROOT.efectivo', number_format($importe - $total_cheques, 2, '.', ','));
	$tpl->assign('_ROOT.total_general', number_format($importe, 2, '.', ','));
	$tpl->assign('_ROOT.total_separado', number_format($separar, 2, '.', ','));
	$tpl->assign('_ROOT.total_depositado', number_format($total, 2, '.', ','));
}

$tpl->printToScreen();
?>
