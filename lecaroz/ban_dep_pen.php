<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$users = array(28, 29, 30, 31);

$descripcion_error[1] = "No hay resultados";

if (isset($_POST['id'])) {
	include_once('includes/phpmailer/class.phpmailer.php');
	
	$cuenta = $_POST['cuenta'];
	$mov_pen = $cuenta == 1 ? "mov_banorte" : "mov_santander";
	
	$sql = "";
	for ($i = 0; $i < count($_POST['id']); $i++) {
		$sql .= "UPDATE $mov_pen SET concepto = '" . strtoupper($_POST['concepto'][$i]) . "', cod_mov = {$_POST['cod_mov'][$i]}, fecha_con = now()::date, imprimir = TRUE  WHERE id = {$_POST['id'][$i]};\n";
		$sql .= "INSERT INTO estado_cuenta (num_cia, fecha, fecha_con, tipo_mov, importe, cod_mov, concepto, cuenta, iduser, timestamp, tipo_con) SELECT num_cia, '{$_POST['fecha'][$i]}', fecha, tipo_mov, importe, cod_mov, concepto, $cuenta, $_SESSION[iduser], now(), 4 FROM $mov_pen WHERE id = {$_POST['id'][$i]};\n";
		$sql .= "UPDATE saldos SET saldo_bancos = saldo_bancos + {$_POST['importe'][$i]}, saldo_libros = saldo_libros + {$_POST['importe'][$i]} WHERE num_cia = {$_POST['num_cia'][$i]} AND cuenta = $cuenta;\n";
		
		if ($_POST['cod_mov'][$i] == 1 && strtoupper($_POST['concepto'][$i]) != strtoupper($_POST['concepto_ant'][$i])) {//echo strtoupper($_POST['concepto'][$i]) . ' ' . strtoupper($_POST['concepto_ant'][$i]) . strtoupper($_POST['concepto'][$i]) != strtoupper($_POST['concepto_ant'][$i]);die;
			$sql_tmp = '
				SELECT
					cc.num_cia,
					cc.nombre_corto
						AS nombre_cia,
					cc.email
						AS email_cia,
					ca.email
						AS email_admin,
					COALESCE((
						SELECT
							TRUE
						FROM
							catalogo_expendios
						WHERE
							num_cia = ' . $_REQUEST['num_cia'][$i] . '
							AND idagven > 0
						LIMIT
							1
					), FALSE)
						AS agente_ventas,
					idadministrador
						AS admin
				FROM
					catalogo_companias cc
					LEFT JOIN catalogo_administradores ca
						USING (idadministrador)
				WHERE
					num_cia = ' . $_POST['num_cia'][$i] . ';
			';
			
			$query = $db->query($sql_tmp);
			
			$cia = $query[0];
			
			$mail = new PHPMailer();
			
			$mail->IsSMTP();
			$mail->Host = 'mail.lecaroz.com';
			$mail->Port = 587;
			$mail->SMTPAuth = true;
			$mail->Username = 'mollendo@lecaroz.com';
			$mail->Password = 'L3c4r0z*';
			
			$mail->From = 'mollendo@lecaroz.com';
			$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');
			
			if ($cia['email_cia'] != '') {
				$mail->AddAddress($cia['email_cia']);
			}
			
			if ($cia['email_admin'] != '') {
				$mail->AddCC($cia['email_admin']);
			}
			
			$mail->AddBCC('miguelrebuelta@lecaroz.com');
			
			if ($cia['agente_ventas'] == 't') {
				$mail->AddCC('liliabalcazar@hotmail.com');
				$mail->AddCC('lilia.balcazar@lecaroz.com');
			}
			
			if ($cia['admin'] == 13) {
				$mail->AddBCC('ilarracheai@hotmail.com');
				$mail->AddBCC('jmjuan68@hotmail.com');
			}
			
			$mail->Subject = 'Depósito de pago de cliente';
			
			$tpl = new TemplatePower('plantillas/ban/email_deposito_cliente.tpl');
			$tpl->prepare();
			
			$tpl->assign('num_cia', $cia['num_cia']);
			$tpl->assign('nombre_cia', $cia['nombre_cia']);
			
			$tpl->assign('cliente', strtoupper($_POST['concepto'][$i]));
			$tpl->assign('importe', number_format(get_val($_POST['importe'][$i]), 2));
			$tpl->assign('fecha', $_POST['fecha'][$i]);
			
			$mail->Body = $tpl->getOutputContent();
			
			$mail->IsHTML(true);
			
			@$mail->Send();
		}
	}
	
	$db->query($sql);
	header("location: ./ban_dep_pen.php");
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dep_pen.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['cuenta'])) {
	$tpl->newBlock("datos");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	$tpl->printToScreen();
	die;
}

$cuenta = $_GET['cuenta'];
$mov_pen = $cuenta == 1 ? "mov_banorte" : "mov_santander";
$cat_mov = $cuenta == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";
$clabe_cuenta = $cuenta == 1 ? "clabe_cuenta" : "clabe_cuenta2";

$sql = "SELECT id, num_cia, (fecha - interval '1 day')::date AS fecha, fecha AS fecha_con, importe, concepto FROM $mov_pen WHERE num_cia BETWEEN " . (!in_array($_SESSION['iduser'], $users) ? "1 AND 899" : "900 AND 998") . " AND fecha_con IS NULL AND tipo_mov = FALSE ORDER BY num_cia, fecha, importe DESC";
$result = $db->query($sql);

if (!$result) {
	header("location: ./ban_dep_pen.php?codigo_error=1");
	//$tpl->newBlock("no_result");
	//$tpl->printToScreen();
	die;
}

$tpl->newBlock("pendientes");
$tpl->assign("cuenta", $cuenta);

$cod_mov = $db->query("SELECT cod_mov, descripcion FROM $cat_mov WHERE tipo_mov = 'FALSE' GROUP BY cod_mov, descripcion ORDER BY cod_mov");

$num_cia = NULL;
for ($i = 0; $i < count($result); $i++) {
	if ($num_cia != $result[$i]['num_cia']) {
		$num_cia = $result[$i]['num_cia'];
		
		$tpl->newBlock("cia");
		$datos_cia = $db->query("SELECT nombre, nombre_corto, $clabe_cuenta FROM catalogo_companias WHERE num_cia = $num_cia");
		$tpl->assign("num_cia", $num_cia);
		$tpl->assign("nombre_cia", $datos_cia[0]['nombre']);
		$tpl->assign("nombre_corto", $datos_cia[0]['nombre_corto']);
		$tpl->assign("cuenta", $datos_cia[0][$clabe_cuenta]);
		$tpl->assign("ini", $i);
		
		$total = 0;
	}
	$tpl->newBlock("fila");
	$tpl->assign("i", count($result) > 1 ? "$i" : "");
	$tpl->assign("next", count($result) > 1 ? '[' . ($i < count($result) - 1 ? $i + 1 : 0) . ']' : "");
	$tpl->assign("id", $result[$i]['id']);
	$tpl->assign("fecha", $result[$i]['fecha']);
	$tpl->assign('fecha_con', $result[$i]['fecha_con']);
	$tpl->assign("num_cia", $num_cia);
	$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ""));
	$tpl->assign("fimporte", number_format($result[$i]['importe'], 2, ".", ","));
	$tpl->assign("concepto", $result[$i]['concepto']);
	$tpl->assign("cia.fin", $i);
	
	foreach ($cod_mov as $value) {
		$tpl->newBlock("cod_mov");
		$tpl->assign("cod_mov", $value['cod_mov']);
		$tpl->assign("descripcion", $value['descripcion']);
	}
	
	$total += $result[$i]['importe'];
	$tpl->assign("cia.total", number_format($total, 2, ".", ","));
}

// Imprimir el resultado
$tpl->printToScreen();
?>