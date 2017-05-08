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
$tpl->assignInclude("body", "./plantillas/ban/ban_dep_div_v2.tpl");
$tpl->prepare();

if (isset($_POST['id'])) {
	$sql = "";
	foreach ($_POST['importe_div'] as $imp) {
		
		$imp = get_val($imp);
		
		if ($imp > 0) {
			$sql .= '
				INSERT INTO
					estado_cuenta
						(
							num_cia,
							fecha,
							fecha_con,
							tipo_mov,
							importe,
							folio,
							cod_mov,
							concepto,
							cuenta,
							iduser,
							timestamp,
							tipo_con,
							num_cia_sec,
							num_doc,
							comprobante
						)
					SELECT
						num_cia,
						fecha,
						fecha_con,
						tipo_mov,
						' . $imp . ',
						folio,
						cod_mov,
						concepto,
						cuenta,
						iduser,
						timestamp,
						tipo_con,
						num_cia_sec,
						num_doc,
						comprobante
					FROM
						estado_cuenta
					WHERE
						id = ' .  $_POST['id'] . '
				' . ";\n";
		}
	}
	$sql .= "DELETE FROM estado_cuenta WHERE id = $_POST[id];\n";
	$db->query($sql);
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

$result = $db->query("SELECT importe FROM estado_cuenta WHERE id = $_GET[id]");

$tpl->newBlock("div");
$tpl->assign("id", $_GET['id']);
$tpl->assign("importe", number_format($result[0]['importe'], 2, ".", ","));

$tpl->printToScreen();
?>