<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------

$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/bal/bal_pagant_con.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	$tpl->printToScreen();
	
	die;
}

$sql = "
	SELECT
		num_cia,
		nombre_corto,
		importe,
		fecha_ini,
		fecha_fin,
		concepto,
		CASE
			WHEN NOW()::DATE BETWEEN fecha_ini AND fecha_fin THEN
				TRUE
			ELSE
				FALSE
		END
			AS activo,
		CASE
			WHEN fecha_fin - NOW()::DATE > 0 THEN
				EXTRACT(MONTHS FROM AGE(fecha_fin, NOW()::DATE)) + 1
			ELSE
				NULL
		END
			AS meses_restantes
	FROM
		pagos_anticipados
		LEFT JOIN catalogo_companias
			USING (num_cia)";
$sql .=$_GET['num_cia'] > 0 ? " WHERE num_cia = $_GET[num_cia]" : "";
$sql .= " ORDER BY num_cia, fecha_ini, concepto";
$result = $db->query($sql);

if (!$result) {
	$db->desconectar();
	header("location: ./bal_pagant_con.php?codigo_error=1");
	die;
}

$tpl->newBlock("listado");

$num_cia = NULL;
for ($i = 0; $i < count($result); $i++) {
	if ($num_cia != $result[$i]['num_cia']) {
		if ($num_cia != NULL)
			if ($count > 1) {
				$tpl->newBlock("total");
				$tpl->assign("total", number_format($total, 2, ".", ","));
			}
		
		$num_cia = $result[$i]['num_cia'];
		
		$tpl->newBlock("cia");
		$tpl->assign("num_cia", $num_cia);
		$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
		
		$total = 0;
		$count = 0;
	}
	$tpl->newBlock("fila");
	$tpl->assign("fecha_ini", $result[$i]['fecha_ini']);
	$tpl->assign("fecha_fin", $result[$i]['fecha_fin']);
	$tpl->assign("concepto", $result[$i]['concepto']);
	$tpl->assign("importe", number_format($result[$i]['importe'], 2, ".", ","));
	$tpl->assign("activo", $result[$i]['activo'] == 't' ? 'bold' : 'normal');
	$tpl->assign("acumulado", $result[$i]['importe'] > 0 && $result[$i]['meses_restantes'] > 0 ? number_format($result[$i]['importe'] * $result[$i]['meses_restantes'], 2) : '&nbsp;');
	$tpl->assign("meses_restantes", $result[$i]['meses_restantes'] > 0 ? $result[$i]['meses_restantes'] : '&nbsp;');
	
	$total += $result[$i]['importe'];
}

if ($count > 1) {
	$tpl->newBlock("total");
	$tpl->assign("total", number_format($total, 2, ".", ","));
}

$tpl->printToScreen();
$db->desconectar();
die;
?>