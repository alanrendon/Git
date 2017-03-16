<?php
// CONSULTA DE TRABAJADORES
// Tabla 'catalogo_trabajadores'
// Menu Proveedores y facturas -> Trabajadores

//define ('IDSCREEN',3311); //ID de pantalla


// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_tra_mod_agu.tpl");
$tpl->prepare();

if (isset($_POST['i'])) {
	if ($_POST['idaguinaldo'] > 0) {
		if ($_POST['aguinaldo'] > 0)
			$sql = "UPDATE aguinaldos SET importe = $_POST[aguinaldo], tipo = 3 WHERE id = $_POST[idaguinaldo]";
		else
			$sql = "DELETE FROM aguinaldos WHERE id = $_POST[idaguinaldo]";
	}
	else {
		$sql = "INSERT INTO aguinaldos (importe, fecha, id_empleado, tipo) VALUES ($_POST[aguinaldo], '28/12/$_POST[anio]', $_POST[id], 3)";
		$lastid = $db->query("SELECT last_value FROM aguinaldos_id_seq");
	}
	$db->query($sql);
	
	
	$tpl->newBlock("cerrar");
	$tpl->assign("i", $_POST['i']);
	$tpl->assign("codigo_extra", $_POST['idaguinaldo'] == "" ? "if (window.opener.document.form.idaguinaldo.length == undefined) window.opener.document.form.idaguinaldo.value = {$lastid[0]['last_value']} + 1; else window.opener.document.form.idaguinaldo[$_POST[i]].value = {$lastid[0]['last_value']} + 1;" : "");
	$tpl->assign("aguinaldo", number_format($_POST['aguinaldo'], 2, ".", ","));
	$tpl->printToScreen();
	die;
}

if ($_GET['idaguinaldo'] > 0) {
	$sql = "SELECT importe FROM aguinaldos WHERE id = $_GET[idaguinaldo]";
	$aguinaldo = $db->query($sql);
}
else
	$aguinaldo = FALSE;

$sql = "SELECT nombre, ap_paterno, ap_materno, catalogo_puestos.descripcion AS puesto, catalogo_turnos.descripcion AS turno FROM catalogo_trabajadores LEFT JOIN catalogo_puestos USING (cod_puestos) LEFT JOIN catalogo_turnos USING (cod_turno)";
$sql .= " WHERE id = $_GET[id]";
$datos = $db->query($sql);

$tpl->newBlock("modificar");
$tpl->assign("nombre", "{$datos[0]['ap_paterno']} {$datos[0]['ap_materno']} {$datos[0]['nombre']}");
$tpl->assign("puesto", $datos[0]['puesto']);
$tpl->assign("turno", $datos[0]['turno']);
$tpl->assign("id", $_GET['id']);
$tpl->assign("i", $_GET['i']);
$tpl->assign("idaguinaldo", $_GET['idaguinaldo']);
$tpl->assign("anio", $_GET['anio']);
$tpl->assign("aguinaldo", $aguinaldo ? number_format($aguinaldo[0]['importe'], 2, ".", "") : "");

// Imprimir el resultado
$tpl->printToScreen();
?>