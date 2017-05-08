<?php

include('includes/class.db.inc.php');
include('includes/class.session2.inc.php');
include('includes/class.TemplatePower.inc.php');
include('includes/dbstatus.php');
include('includes/cheques.inc.php');
include('includes/fpdf/fpdf.php');

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

function filter($value) {
	return $value != 0;
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
		case 'imprimirComprobantes':
			$condiciones = array();
			
			$condiciones[] = 'tsreg::DATE BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			
			$condiciones[] = 'reporte = TRUE';
			
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
						'importe'    => in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'],
						'separar'    => $r['separar'],
						'total'      => in_array($r['cod_mov'], array(19, 48)) ? -$r['total'] : $r['total']
					);
					
					$data[$cont]['fecha'] = $r['fecha'];
					$data[$cont]['importe'] += in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'];
					$data[$cont]['separar'] += $r['separar'];
					$data[$cont]['total'] += in_array($r['cod_mov'], array(19, 48)) ? -$r['total'] : $r['total'];
				}
				
				shuffle($data);
				
				$string = '';
				
				foreach ($data as $d) {
					$piezas = explode('/', $d['fecha']);
					
					$string .= str_pad('', 4, "\n");
					$string .= str_pad('', 1, ' ') . date('d-m-y', mktime(0, 0, 0, intval($piezas[1], 10), intval($piezas[0], 10), intval($piezas[2], 10)));
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
		
		case 'generarComprobantes':
			$condiciones = array();
			
			$condiciones[] = 'tsreg::DATE BETWEEN \'' . $_REQUEST['fecha1'] . '\' AND \'' . $_REQUEST['fecha2'] . '\'';
			
			$condiciones[] = 'reporte = TRUE';
			
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
					(
						SELECT
							nombre_fin
						FROM
							encargados
						WHERE
							num_cia = cc.num_cia_primaria
						ORDER BY
							id DESC
						LIMIT
							1
					)
						AS encargado,
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
							'encargado'       => $r['encargado'],
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
						'importe'    => in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'],
						'separar'    => $r['separar'],
						'total'      => in_array($r['cod_mov'], array(19, 48)) ? -$r['total'] : $r['total']
					);
					
					$data[$cont]['fecha'] = $r['fecha'];
					$data[$cont]['importe'] += in_array($r['cod_mov'], array(19, 48)) ? -$r['importe'] : $r['importe'];
					$data[$cont]['separar'] += $r['separar'];
					$data[$cont]['total'] += in_array($r['cod_mov'], array(19, 48)) ? -$r['total'] : $r['total'];
				}
				
				shuffle($data);
				
				class PDF extends FPDF {
					function Header() {
						$this->Image('imagenes/ficha_cometra.jpg', 0, 0, 158);
					}
				}
				
				$pagew = 158;
				$pageh = 198;
				
				$pdf = new PDF('P', 'mm', array($pagew, $pageh));
				
				$pdf->AddFont('font1', '', 'font1.php');
				$pdf->AddFont('font2', '', 'font2.php');
				$pdf->AddFont('font3', '', 'font3.php');
				$pdf->AddFont('font4', '', 'font4.php');
				$pdf->AddFont('font5', '', 'font5.php');
				$pdf->AddFont('font6', '', 'font6.php');
				
				$pdf->SetDisplayMode('fullpage', 'single');
				
				$pdf->SetMargins(0, 0, 0);
				
				$separador = array('-', '/');
				
				foreach ($data as $d) {
					$piezas = explode('/', $d['fecha']);
					
					//$paquetes = ceil($d['total'] / 2000);
					
					$pdf->AddPage('P', array($pagew, $pageh));
					
					$pdf->SetFont('Arial', 'B', 16);
					$pdf->SetTextColor(164, 0, 0);
					
					$pdf->Text(115, 16, $d['comprobante']);
					
					$pdf->SetFont('Arial', '', 10);
					$pdf->SetTextColor(0, 0, 0);
					
					//$pdf->Text(20, 7, 'PAQUETES:_______________');
					//$pdf->Text(55, 6.8, $paquetes);
					$pdf->Text(11, 26, date('d-m-y', mktime(0, 0, 0, intval($piezas[1], 10), intval($piezas[0], 10), intval($piezas[2], 10))));
					$pdf->Text(97, 26, 'X');
					$pdf->Text(14, 35, str_pad($d['cliente_cometra'], 8, '0'));
					$pdf->Text(73, 35, 'X');
					$pdf->Text(20, 43, substr($d['num_cia'] . '-' . $d['nombre_cia'], 0, 64));
					$pdf->Text(16, 52, substr($d['domicilio'], 0, 66));
					$pdf->Text(20, 60, '1');
					$pdf->Text(58, 60, 'X');
					$pdf->Text(121, 60, number_format($d['total'], 2));
					$pdf->Text(16, 69, substr(num2string($d['total']), 0, 66));
					$pdf->Text(10, 80, number_format($d['total'], 2));
					$pdf->Text(115, 80, number_format($d['total'], 2));
					$pdf->Text(20, 91, 'UNO');
					$pdf->Text(125, 91, 'UNO');
					$pdf->Text(20, 98, $d['banco'] . ' CAJA GENERAL');
					$pdf->Text(20, 105, 'CALLE IXNAHUALTONGO NO.129, COL. SAN LORENZO BOTURINI,');
					$pdf->Text(20, 109, 'DEL. VENUSTIANO CARRANZA, CP.15820, MEXICO, D.F.');
					$pdf->Text(20, 116, str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT));
					$pdf->Text(55, 116, str_pad(mt_rand(1, 99999999), 4, '0', STR_PAD_LEFT));
					$pdf->Text(12, 141, date('d-m-y', mktime(0, 0, 0, intval($piezas[1], 10), intval($piezas[0], 10), intval($piezas[2], 10))));
					$pdf->Text(12, 158, $d['encargado']);
					$pdf->Image('imagenes/firmas/firma' . mt_rand(1, 62) . '.jpg', 53, 134, 25, 20);
					
					$pdf->SetFont('font' . mt_rand(1, 6), '', 14);
					
					$sep = $separador[mt_rand(0, 1)];
					
					$pdf->Image('imagenes/firmas/cometra' . mt_rand(1, 40) . '.jpg', 137, 134, 20, 20);
					$pdf->Text(95, 141, date('d' . $sep . 'm' . $sep . 'y', mktime(0, 0, 0, intval($piezas[1], 10), intval($piezas[0], 10), intval($piezas[2], 10))));
					$pdf->Text(95, 148, mt_rand(18, 20) . ':' . str_pad(mt_rand(0, 9) * 5, 2, '0', STR_PAD_LEFT));
					$pdf->Text(130 + mt_rand(0, 10), 138 + mt_rand(0, 5), mt_rand(50001, 99999));
				}
				
				$pdf->Output('ComprobantesCometra.pdf', 'I');
			}
		break;
	}
	
	die;
}

$tpl = new TemplatePower('plantillas/cometra/CometraImprimirComprobantes.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->assign('fecha1', date('d/m/Y', mktime(0, 0, 0, date('n'), 1)));
$tpl->assign('fecha2', date('d/m/Y'));

$tpl->printToScreen();
?>
