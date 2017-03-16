<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$db = new DBclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Delaracion de variables -------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body", "./plantillas/bal/bal_ifm_con_v2.tpl" );
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("datos");

	$cia = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias ORDER BY num_cia");
	for ($i = 0; $i < count($cia); $i++) {
		$tpl->newBlock("num_cia");
		$tpl->assign("num_cia", $cia[$i]['num_cia']);
		$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
	}

	$admins = $db->query("SELECT idadministrador AS id, nombre_administrador AS admin FROM catalogo_administradores ORDER BY admin");
	foreach ($admins as $a) {
		$tpl->newBlock('admin');
		$tpl->assign('id', $a['id']);
		$tpl->assign('admin', $a['admin']);
	}

	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);
	}

	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die;
}

if ($_GET['tipo'] == "inventario") {
	$day = date("d");
	$month = date("n");
	$year = date("Y");
	$fecha = date("d/m/Y", mktime(0, 0, 0, $month - 1, 1, $year));

	$sql = "SELECT num_cia, nombre_corto, codmp, catalogo_mat_primas.nombre AS nombre_mp, tipo_unidad_consumo.descripcion AS unidad, tipo";
	$sql .= " FROM inventario_real LEFT JOIN catalogo_mat_primas USING (codmp) LEFT JOIN tipo_unidad_consumo ON (idunidad = unidadconsumo) LEFT JOIN catalogo_companias USING (num_cia) WHERE ";
	$sql .= $_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : "num_cia <= 300";
	$sql .= $_GET['admin'] > 0 ? " AND idadministrador = $_GET[admin]" : '';
	$sql .= " AND ((num_cia, codmp) IN (SELECT num_cia, codmp FROM mov_inv_real WHERE fecha >= '$fecha' AND num_cia <= 300 GROUP BY num_cia, codmp) OR existencia != 0)";
	$sql .= " ORDER BY " . ($_GET['admin'] < 0 ? 'idadministrador, ' : '') . "num_cia, tipo, catalogo_mat_primas.nombre";
	$result = $db->query($sql);

	if (!$result) {
		$db->desconectar();
		header("location: ./bal_ifm_con_v2.php?codigo_error=1");
		die;
	}

	$num_cia = NULL;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			if ($num_cia != NULL) {
//				$tpl->newBlock('notas');
//
//				if (($numfilas += 6) > $numfilas_x_hoja)
//					$numhojas++;

				if ($numhojas % 2 != 0)
					$tpl->newBlock("salto_pagina");

				$tpl->newBlock("salto_pagina");
			}

			$num_cia = $result[$i]['num_cia'];
			$tpl->newBlock("cia");
			$tpl->assign("nombre_mes", mes_escrito(date("n"), TRUE));
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);

			$tpl->newBlock("hoja");

			$tipo = $result[$i]['tipo'];
			$numhojas = 1;
			$numfilas_x_hoja = /*38*/37;
			$numfilas = 0;
		}
		if ($numfilas >= $numfilas_x_hoja) {
			$numfilas_x_hoja = /*46*/45;
			$numfilas = 0;
			$numhojas++;

			$tpl->newBlock("salto_pagina");

			//if ($numhojas % 2 == 0)
				//$tpl->newBlock("salto_hoja_par");

			$tpl->newBlock("hoja");
			$tpl->newBlock("nombre_cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
			$numfilas++;
		}
		$tpl->newBlock("fila");
		if ($tipo != $result[$i]['tipo']) {
			$tipo = $result[$i]['tipo'];
			$tpl->newBlock("empaque");
			$tpl->gotoBlock("fila");
			$numfilas++;
		}
		$tpl->assign("codmp", $result[$i]['codmp']);
		$tpl->assign("nombre_mp", $result[$i]['nombre_mp']);
		$tpl->assign("unidad", strtoupper($result[$i]['unidad']));
		$numfilas++;
	}
//	if ($num_cia != NULL) {
//		$tpl->newBlock('notas');
//	}
}
else {
	$cia = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias WHERE " . ($_GET['num_cia'] > 0 ? " num_cia = $_GET[num_cia]" : " num_cia <= 300") . " ORDER BY num_cia");

	if (!$cia) {
		$db->desconectar();
		header("location: ./bal_ifm_con_v2.php?codigo_error=1");
		die;
	}

	for($i = 0; $i < count($cia); $i++) {
		$tpl->newBlock("recibo_avio");
		$tpl->assign("num_cia", $cia[$i]['num_cia']);
		$tpl->assign("nombre", $cia[$i]['nombre_corto']);
		if(($i + 1) % 2 == 0)
			$tpl->newBlock("jump");
	}
}

$tpl->printToScreen();
$db->desconectar();
?>
