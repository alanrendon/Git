<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

$users = array(28, 29, 30, 31, 32);

//if ($_SESSION['iduser'] != 1) die("MODIFICANDO LA PANTALLA... GOMEN ^_^|");

if (isset($_GET['dias'])) {
	$sql = '
		SELECT
			clabe_cuenta
				AS
					cuenta,
			folio,
			a_nombre,
			importe,
			CURRENT_DATE
				AS
					fecha,
			CURRENT_DATE + interval \'' . $_GET['dias'] . ' days\'
				AS
					fecha_limite
		FROM
				cheques
			LEFT JOIN
				catalogo_companias
					USING
						(
							num_cia
						)
		WHERE
				cuenta = 1
			AND
				archivo = \'TRUE\'
			AND
				num_cheque > 0
			AND
				fecha_cancelacion IS NULL
			AND
				num_cia
					BETWEEN
						' . (in_array($_SESSION['iduser'], $users) ? '900 AND 998' : '1 AND 800') . '
		ORDER BY
			clabe_cuenta,
			folio LIMIT 10
	';
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_che_seg_ban.php?codigo_error=1");
		die;
	}
	
	$sql = '
		UPDATE
			cheques
		SET
			archivo = \'FALSE\'
		WHERE
				cuenta = 1
			AND
				archivo = \'TRUE\'
			AND
				num_cia
					BETWEEN
						' . (in_array($_SESSION['iduser'], $users) ? '900 AND 998' : '1 AND 800') . '
	';
	$db->query($sql);
	
	$fecha = date('Ymd');
	$fecha_desproteccion = date('Ymd', mktime(0, 0, 0, date('n'), date('j') + $_GET['dias'], date('Y')));
	
	/*
	@ Datos
	*/
	$data = '';
	$cuenta = NULL;
	$total = 0;
	$cuentas = 0;
	foreach ($result as $i => $reg) {
		if ($cuenta != $reg['cuenta']) {
			$cuenta = $reg['cuenta'];
			
			$cuentas++;
		}
		$data .= 'D';																			// [1]  Tipo registro
		$data .= '11';																			// [2]  Tipo de servicio: (10) 24hrs, (11) En línea por archivo, (20) En línea cheque por cheque
		$data .= str_pad($i + 1, 5, '0', STR_PAD_LEFT);											// [5]  Consecutivo
		$data .= '60';																			// [2]  Código de operación: (60) Protección, (65) Cancelar protección
		$data .= substr($reg['cuenta'], 1, 10);													// [10] Número de cuenta
		$data .= str_pad($reg['folio'], 7, '0', STR_PAD_LEFT);									// [7]  Cheque
		$data .= $fecha;																		// [8]  Fecha de protección
		$data .= $fecha_desproteccion;															// [8]  Fecha de desprotección
		$data .= str_pad(number_format($reg['importe'], 2, '', ''), 13, '0', STR_PAD_LEFT);		// [13] Importe
		$data .= '0000N';																		// [5]  Validar beneficiario
		$data .= str_pad(substr($reg['a_nombre'], 0, 40), 40);									// [40] Beneficiario
		$data .= 'MXP';																			// [3]  Divisa: (MXP) Pesos mexicanos, (DLS) Dólares
		$data .= str_pad('', 78, '0');															// [78] Relleno (0)
		$data .= "\r\n";
		
		$total += $reg['importe'];
	}
	
	/*
	@ Encabezado
	*/
	$header = 'H';																				// [1] Tipo de registro
	$header .= str_pad($cuentas, 5, '0', STR_PAD_LEFT);											// [5] Número de cuentas
	$header .= $fecha;																			// [8] Fecha de aplicación
	$header .= str_pad('1', 3, '0', STR_PAD_LEFT);												// [3] Consecutivo del día
	$header .= str_pad(count($result), 5, '0', STR_PAD_LEFT);									// [5] Número de cheques a proteger
	$header .= str_pad(number_format($total, 2, '', ''), 18, '0', STR_PAD_LEFT);				// [18] Importe total de cheques protegidos
	$header .= str_pad('', 5, '0');																// [5] Número de cheques a desproteger
	$header .= str_pad('', 18, '0');															// [18] Importe total de cheques desprotegidos
	$header .= str_pad(count($result), 5, '0', STR_PAD_LEFT);									// [5] Total de registros
	$header .= str_pad(number_format($total, 2, '', ''), 18, '0', STR_PAD_LEFT);				// [18] Importe total
	$header .= str_pad('', 106, '0');															// [106] Relleno (0)
	$header .= "\r\n";
	
	$nombre_archivo = "CHESEG_BANORTE_" . (in_array($_SESSION['iduser'], $users) ? '_ELITE' : '') . date("Ymd") . ".TXT";
	
	header("Content-Type: application/download");
	header("Content-Disposition: attachment; filename=$nombre_archivo");
	
	echo $header . $data;
	
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_che_seg_ban.tpl");
$tpl->prepare();

// Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>