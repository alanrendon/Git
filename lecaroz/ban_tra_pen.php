<?php
// LISTADO DE ESTADOS DE CUENTA
// Tabla 'estado_cuenta'
// Menu 'pendiente'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$db = new DBclass($dsn, "autocommit=yes");
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31, 32);

// --------------------------------- Descripción de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_tra_pen.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT num_cia AS num_cia, catalogo_companias.nombre AS nombre_cia, transferencias_electronicas.num_proveedor AS num_pro, a_nombre AS nombre_pro, folio, facturas,";
$sql .= " transferencias_electronicas.importe, fecha_gen, cheques.concepto AS concepto FROM transferencias_electronicas LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN";
$sql .= " cheques USING (num_cia, folio, cuenta) WHERE transferencias_electronicas.status = 0 AND num_cia BETWEEN " . (in_array($_SESSION['iduser'], $users) ? '900 AND 950' : '1 AND 800') . " ORDER BY num_pro, num_cia, folio";
$result = $db->query($sql);

if (!$result)
	$tpl->newBlock("no_result");
else {
	$num_pro = NULL;
	$gran_total = 0;
	foreach ($result as $reg) {
		if ($num_pro != $reg['num_pro']) {
			if ($num_pro != NULL)
				$tpl->assign("pro.total", number_format($total, 2, ".", ","));
			
			$num_pro = $reg['num_pro'];
			
			$tpl->newBlock("pro");
			$tpl->assign("num_pro", $num_pro);
			$tpl->assign("nombre", $reg['nombre_pro']);
			$total = 0;
		}
		$tpl->newBlock("fila");
		$tpl->assign("num_cia", $reg['num_cia']);
		$tpl->assign("nombre", $reg['nombre_cia']);
		$tpl->assign("fecha", $reg['fecha_gen']);
		$tpl->assign("folio", $reg['folio']);
		$tpl->assign("concepto", $reg['concepto']);
		$tpl->assign("facturas", $reg['facturas']);
		$tpl->assign("importe", number_format($reg['importe'], 2, ".", ","));
		$total += $reg['importe'];
		$gran_total += $reg['importe'];
	}
	if ($num_pro != NULL)
		$tpl->assign("pro.total", number_format($total, 2, ".", ","));
	$tpl->newBlock("total");
	$tpl->assign("gran_total", number_format($gran_total, 2, ".", ","));
}
$tpl->printToScreen();
?>