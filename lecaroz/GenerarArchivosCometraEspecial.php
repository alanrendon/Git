<?php

include('includes/class.db.inc.php');
include('includes/dbstatus.php');

$db = new DBclass($dsn, 'autocommit=yes');

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
			AS
				cod_mov,
		concepto,
		importe,
		separar,
		total,
		DATE_TRUNC(\'second\', tsend)
			AS tsend
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
		tsend::DATE BETWEEN \'01/11/2011\' AND \'30/11/2011\'
		AND tsreg IS NOT NULL
	ORDER BY
		tsend,
		c.comprobante,
		c.fecha,
		cc.num_cia_primaria,
		c.num_cia
';

$result = $db->query($sql);

$rows = array();
if ($result) {
	$csv = '';
	
	$data = array();
	$comprobante = NULL;
	$cont = 0;
	foreach ($result as $r) {
		if ($comprobante != $r['comprobante']) {
			if ($comprobante != NULL) {
				$cont++;
			}
			
			$comprobante = $r['comprobante'];
			
			preg_match('/([0-9]{2})\/([0-9]{2})\/([0-9]{4}) ([0-9]{2})\:([0-9]{2})\:([0-9]{2})/', $r['tsend'], $match);
			
			$ts = mktime(intval($match[4], 10), intval($match[5], 10), intval($match[6], 10), intval($match[2], 10), intval($match[1], 10), intval($match[3], 10));
			
			$data[$cont] = array (
				'num_cia'     => $r['num_cia'] >= 900 ? $r['num_cia'] : $r['num_cia_primaria'],
				'nombre_cia'  => $r['nombre_cia_primaria'],
				'cuenta'      => $r['cuenta_primaria'],
				'comprobante' => $comprobante,
				'tipo'        => $r['tipo_comprobante'],
				'importe'     => 0,
				'separar'     => 0,
				'total'       => 0,
				'tsend'       => $r['tsend'],
				'ts'          => $ts
			);
		}
		
		$data[$cont]['depositos'][] = array(
			'fecha'      => $r['fecha'],
			'num_cia'    => $r['num_cia'],
			'nombre_cia' => $r['nombre_cia'],
			'cuenta'     => $r['cuenta'],
			'cod_mov'    => $r['cod_mov'],
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
	
	function cmp($a, $b) {
		if ($a['ts'] == $b['ts']) {
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
			return ($a['ts'] < $b['ts']) ? -1 : 1;
		}
	}
	
	usort($data, 'cmp');
	
	if (count($data) > 0) {
		$ts = NULL;
		
		foreach ($data as $d) {
			if ($ts != $d['ts']) {
				if ($ts != NULL) {
					$csv .= ',,,,"TOTAL GENERAL (' . count($data) . ')","' . number_format($importe, 2, '.', ',') . '","' . number_format($separar, 2, '.', ',') . '","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
					
					fwrite($fp, $csv);
					
					fclose($fp);
				}
				
				$ts = $d['ts'];
				
				$fp = fopen('infonavit/cometra_' . date('Y_m_d_H_i_s', $ts) . '.csv', 'wb+');
				
				$csv = utf8_decode('"#","COMPAÑIA","CUENTA","FECHA","COMPROBANTE","IMPORTE","SEPARAR","TOTAL"') . "\n";
				
				$importe = 0;
				$separar = 0;
				$total = 0;
				
				echo $d['tsend'] . '<br />';
			}
			
			$csv .= '"' . $d['num_cia'] . '",';
			$csv .= '"' . $d['nombre_cia'] . '",';
			$csv .= '"' . str_pad($d['cuenta'], 11, '0', STR_PAD_LEFT) . '",';
			$csv .= '"' . $d['fecha'] . '",';
			$csv .= '"' . $d['comprobante'] . '",';
			$csv .= '"' . number_format($d['importe'], 2, '.', ',') . '",';
			$csv .= '"' . number_format($d['separar'], 2, '.', ',') . '",';
			$csv .= '"' . number_format($d['total'], 2, '.', ',') . '",';
			$csv .= "\n";
			
			$importe += $d['importe'];
			$separar += $d['separar'];
			$total += $d['total'];
		}
		
		if ($ts != NULL) {
			$csv .= ',,,,"TOTAL GENERAL (' . count($data) . ')","' . number_format($importe, 2, '.', ',') . '","' . number_format($separar, 2, '.', ',') . '","' . number_format($total, 2, '.', ',') . '"' . "\n\n";
			
			fwrite($fp, $csv);
			
			fclose($fp);
		}
	}
}