<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, 'autocommit=yes');
$session = new sessionclass($dsn);

if (isset($_GET['pagos'])) {
	$sql = '
		SELECT
				lpad
					(
						fp.num_proveedor,
						5,
						\'0\'
					)
			||
				lpad
					(
						num_fact,
						10,
						\'0\'
					)
			||
				lpad
					(
						replace
							(
								round
									(
										total::numeric,
										2
									),
								\'.\',
								\'\'
							),
						20,
						\'0\'
					)
			||
				lpad
					(
						num_cia,
						5,
						\'0\'
					)
			||
				rpad
					(
						nombre,
						60,
						\' \'
					)
			||
				rpad
					(
						CASE
							WHEN cuenta = 1 THEN
								\'BANORTE\'
							ELSE
								\'SANTANDER\'
						END,
						20,
						\' \'
					)
			||
				lpad
					(
						folio_cheque,
						10,
						\'0\'
					)
			||
				lpad
					(
						extract
							(
								year
									from
										fecha_cheque
							),
						4,
						\'0\'
					)
			||
				lpad
					(
						extract
							(
								month
									from
										fecha_cheque
							),
						2,
						\'0\'
					)
			||
				lpad
					(
						extract
							(
								day
									from
										fecha_cheque
							),
						2,
						\'0\'
					)
			||
				rpad
					(
						\'\',
						117,
						\' \'
					)
				AS
					data
		FROM
				facturas_pagadas
					fp
			LEFT JOIN
				catalogo_companias
					cc
						USING
							(
								num_cia
							)
		WHERE
			(
				num_cia,
				folio_cheque,
				cuenta
			)
				IN
					(
						SELECT
							num_cia,
							folio,
							cuenta
						FROM
							cheques
						WHERE
							site = \'TRUE\'
					)
		ORDER BY
			fp.num_proveedor,
			fp.num_cia,
			fp.cuenta,
			fp.folio_cheque,
			fp.num_fact
	';
	$result = $db->query($sql);
	
	if (!$result) {
		echo -1;
		die;
	}
	
	$db->query('UPDATE cheques SET site = \'FALSE\' WHERE site = \'TRUE\'');
	
	$data = '';
	foreach ($result as $r)
		$data .= $r['data'] . "\r\n";
	
	if (!($fp = fopen('pagos/PAGOS.txt', 'w'))) {
		echo -2;
		die;
	}
	fwrite($fp, $data);
	fclose($fp);
	
	if (!($ftp = @ftp_connect('www.lecaroz.com'))) {
		echo -3;
		die;
	}
	
	if (!@ftp_login($ftp, 'lecar866', 'efadc506')) {
		echo -4;
		die;
	}
	
	if (!@ftp_chdir($ftp, 'www/proveedores')) {
		echo -5;
		die;
	}
	
	if (!@ftp_delete($ftp, 'PAGOS.txt')) {
		echo -6;
		die;
	}
	
	if (!@ftp_put($ftp, 'PAGOS.txt', 'pagos/PAGOS.txt', FTP_BINARY)) {
		echo -7;
		die;
	}
	
	ftp_close($ftp);
	
	die;
}

if (isset($_GET['pendientes'])) {
	$sql = '
		SELECT
				lpad
					(
						pp.num_proveedor,
						5,
						\'0\'
					)
			||
				lpad
					(
						num_fact,
						10,
						\'0\'
					)
			||
				lpad
					(
						replace
							(
								round
									(
										total::numeric,
										2
									),
								\'.\',
								\'\'
							),
						20,
						\'0\'
					)
			||
				lpad
					(
						num_cia,
						5,
						\'0\'
					)
			||
				rpad
					(
						nombre,
						60,
						\' \'
					)
			||
				rpad
					(
						\'\',
						20,
						\' \'
					)
			||
				lpad
					(
						\'\',
						10,
						\'0\'
					)
			||
				lpad
					(
						\'\',
						8,
						\'0\'
					)
			||
				rpad
					(
						\'\',
						117,
						\' \'
					)
				AS
					data
		FROM
				pasivo_proveedores
					pp
			LEFT JOIN
				catalogo_companias
					cc
						USING
							(
								num_cia
							)
		WHERE
			num_cia < 900
		ORDER BY
			pp.num_proveedor,
			pp.num_cia,
			pp.num_fact
	';
	$result = $db->query($sql);
	
	if (!$result) {
		echo -1;
		die;
	}
	
	$db->query('UPDATE cheques SET site = \'FALSE\' WHERE site = \'TRUE\'');
	
	$data = '';
	foreach ($result as $r)
		$data .= $r['data'] . "\r\n";
	
	if (!($fp = fopen('pagos/PENDIENTES.txt', 'w'))) {
		echo -2;
		die;
	}
	fwrite($fp, $data);
	fclose($fp);
	
	if (!($ftp = @ftp_connect('www.lecaroz.com'))) {
		echo -3;
		die;
	}
	
	if (!@ftp_login($ftp, 'lecar866', 'efadc506')) {
		echo -4;
		die;
	}
	
	if (!@ftp_chdir($ftp, 'www/proveedores')) {
		echo -5;
		die;
	}
	
	if (!@ftp_delete($ftp, 'PENDIENTES.txt')) {
		echo -6;
		die;
	}
	
	if (!@ftp_put($ftp, 'PENDIENTES.txt', 'pagos/PENDIENTES.txt', FTP_BINARY)) {
		echo -7;
		die;
	}
	
	ftp_close($ftp);
	
	die;
}

if (isset($_GET['catalogo'])) {
	$sql = '
		SELECT
				lpad
				(
					num_proveedor,
					5,
					\'0\'
				)
			||
				rpad
					(
						nombre,
						100,
						\' \'
					)
			||
				rpad(pass_site, 10, \' \')
			||
				rpad
					(
						\'\',
						140,
						\' \'
					)
				AS
					data
		FROM
			catalogo_proveedores
		WHERE
				num_proveedor < 9000
			AND
				(
						pass_site IS NOT NULL
					OR
						trim(pass_site) <> \'\'
				)
		ORDER BY
			num_proveedor
	';
	$result = $db->query($sql);
	
	if (!$result) {
		echo -1;
		die;
	}
	
	$data = '';
	foreach ($result as $r)
		$data .= $r['data'] . "\r\n";
	
	if (!($fp = fopen('pagos/CATALOGO_PROVEEDORES.txt', 'w'))) {
		echo -2;
		die;
	}
	fwrite($fp, $data);
	fclose($fp);
	
	if (!($ftp = @ftp_connect('www.lecaroz.com'))) {
		echo -3;
		die;
	}
	
	if (!@ftp_login($ftp, 'lecar866', 'efadc506')) {
		echo -4;
		die;
	}
	
	if (!@ftp_chdir($ftp, 'www/proveedores')) {
		echo -5;
		die;
	}
	
	if (!@ftp_delete($ftp, 'CATALOGO_PROVEEDORES.txt')) {
		echo -6;
		die;
	}
	
	if (!@ftp_put($ftp, 'CATALOGO_PROVEEDORES.txt', 'pagos/CATALOGO_PROVEEDORES.txt', FTP_BINARY)) {
		echo -7;
		die;
	}
	
	ftp_close($ftp);
	
	die;
}

$tpl = new TemplatePower('plantillas/ban/ArchivoPagos.tpl');
$tpl->prepare();

$tpl->assign('menucnt', $_SESSION['menu'] . '_cnt.js');

$tpl->printToScreen();
?>