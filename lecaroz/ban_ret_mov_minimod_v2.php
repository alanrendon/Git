<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ban/ban_ret_mov_minimod_v2.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$cuenta = $_POST['cuenta'];
	$num_cia = $_POST['num_cia'];
	$tabla_mov = $cuenta == 1 ? "mov_banorte" : "mov_santander";
	$concepto = strtoupper($_POST['concepto']);
	$importe = str_replace(",", "", $_POST['importe']);

	$sql = "UPDATE $tabla_mov SET cod_mov = $_POST[cod_mov], concepto = '$concepto', fecha = '$_POST[fecha]', /*fecha_con = '$_POST[fecha_con]',*/ imprimir = 'TRUE' WHERE id = $_POST[id]";

	$db->query($sql);

	$tpl->newBlock("cerrar");
	$tpl->assign("num_cia", $num_cia);
	$tpl->printToScreen();

	if ($_POST['cod_mov'] == 5 && ($num_cia == 628 || $num_cia == 619)) {
		include_once('includes/phpmailer/class.phpmailer.php');

		$sql = '
			SELECT
				cc.num_cia,
				cc.nombre_corto
					AS nombre_cia,
				' . ($cuenta == 1 ? 'clabe_cuenta' : 'clabe_cuenta2') . '
					AS cuenta
			FROM
				catalogo_companias cc
			WHERE
				num_cia = ' . $num_cia . ';
		';

		$query = $db->query($sql);

		$cia = $query[0];

		$sql = '
			SELECT
				*
			FROM
				' . $tabla_mov . '
			WHERE
				id = ' . $_POST['id'] . '
		';

		$query = $db->query($sql);

		$mov = $query[0];

		$mail = new PHPMailer();

		$mail->IsSMTP();
		$mail->Host = 'mail.lecaroz.com';
		$mail->Port = 587;
		$mail->SMTPAuth = true;
		$mail->Username = 'mollendo@lecaroz.com';
		$mail->Password = 'L3c4r0z*';

		$mail->From = 'mollendo@lecaroz.com';
		$mail->FromName = utf8_decode('Oficinas Administrativas Mollendo, S. de R.L. de C.V.');

		if ($cia['num_cia'] == 628) {
			$mail->AddAddress('ilarracheai@hotmail.com');
			$mail->AddAddress('jmjuan68@hotmail.com');
		}

		if ($cia['num_cia'] == 619) {
			$mail->AddAddress('goniaguirremj@hotmail.com');
		}

		$mail->AddBCC('miguelrebuelta@lecaroz.com');
		// $mail->AddBCC('sistemas@lecaroz.com');

		$mail->Subject = utf8_decode('Cheque cobrado');

		$tpl = new TemplatePower('plantillas/ban/email_cheque_cobrado.tpl');
		$tpl->prepare();

		$tpl->assign('importe', number_format(get_val($_POST['importe']), 2));
		$tpl->assign('fecha', $_POST['fecha']);
		$tpl->assign('folio', $mov['num_documento']);
		$tpl->assign('cuenta', $cia['cuenta']);
		$tpl->assign('banco', $cuenta == 1 ? 'Banorte' : 'Santander');

		$mail->Body = $tpl->getOutputContent();

		$mail->IsHTML(true);

		@$mail->Send();
	}

	die;
}

$id = $_GET['id'];
$cuenta = $_GET['cuenta'];
$tabla_mov = $cuenta == 1 ? "mov_banorte" : "mov_santander";
$cat_mov = $cuenta == 1 ? "catalogo_mov_bancos" : "catalogo_mov_santander";

// Si no se ha cargado archivo, solicitarlo
$tpl->newBlock("datos");
$tpl->assign("id", $id);
$tpl->assign("cuenta", $cuenta);

$result = $db->query("SELECT num_cia, fecha, cod_banco, concepto, importe FROM $tabla_mov WHERE id = $id");
$cat = $db->query("SELECT cod_mov, descripcion FROM $cat_mov WHERE tipo_mov = 'TRUE' GROUP BY cod_mov, descripcion ORDER BY cod_mov");

$tpl->assign("num_cia", $result[0]['num_cia']);
$nombre = $db->query("SELECT nombre FROM catalogo_companias WHERE num_cia = {$result[0]['num_cia']}");
$tpl->assign("nombre", $nombre[0]['nombre']);
$tpl->assign("fecha", $result[0]['fecha']);
$tpl->assign("concepto", $result[0]['concepto']);
$tpl->assign("importe", number_format($result[0]['importe'], 2, ".", ","));

foreach ($cat as $cod) {
	$tpl->newBlock("cod_mov");
	$tpl->assign("cod_mov", $cod['cod_mov']);
	$tpl->assign("descripcion", $cod['descripcion']);
}

$tpl->printToScreen();
?>
