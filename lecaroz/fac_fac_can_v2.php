<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';
include('./includes/auxinv.inc.php');

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if (!in_array($_SESSION['iduser'], array(1))) die("la estoy modificando");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/fac/fac_fac_can_v2.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_pro'])) {
	$sql = "";
	if ($_POST['tipo'] == 1 && isset($_POST['desc'])) {
		$mp = $db->query("SELECT num_cia, codmp, contenido, cantidad, precio FROM entrada_mp WHERE num_proveedor = $_POST[num_pro] AND num_fact = '$_POST[num_fact]'");
		foreach ($mp as $pro)
			$sql .= "UPDATE inventario_real SET existencia = existencia - $pro[contenido] * $pro[cantidad] WHERE num_cia = $pro[num_cia] AND codmp = $pro[codmp];\n";
		
		$sql .= "DELETE FROM mov_inv_real WHERE (num_cia, codmp, fecha) IN (SELECT num_cia, codmp, fecha FROM entrada_mp WHERE num_proveedor = $_POST[num_pro]";
		$sql .= " AND num_fact = '$_POST[num_fact]') AND tipo_mov = 'FALSE' AND /*descripcion LIKE '%$_POST[num_fact]%'*/num_fact = '$_POST[num_fact]';\n";
		$sql .= "DELETE FROM entrada_mp WHERE num_proveedor = $_POST[num_pro] AND num_fact = '$_POST[num_fact]';\n";
	}
	else if ($_POST['tipo'] == 2 && isset($_POST['desc'])) {
		$tan = $db->query("SELECT num_cia, fecha, sum(litros) AS litros FROM factura_gas WHERE num_proveedor = $_POST[num_pro] AND num_fact = '$_POST[num_fact]' GROUP BY num_cia, fecha");
		$sql .= "UPDATE inventario_real SET existencia = existencia - {$tan[0]['litros']} WHERE num_cia = {$tan[0]['num_cia']} AND codmp = 90;\n";
		$sql .= "DELETE FROM factura_gas WHERE num_proveedor = $_POST[num_pro] AND num_fact = '$_POST[num_fact]';\n";
		$sql .= "DELETE FROM mov_inv_real WHERE num_cia = {$tan[0]['num_cia']} AND codmp = 90 AND fecha = '{$tan[0]['fecha']}' AND tipo_mov = 'FALSE' AND descripcion LIKE";
		$sql .= " '%$_POST[num_fact]%';\n";
	}
	
	$sql .= "INSERT INTO facturas_borradas (num_proveedor, num_cia, num_fact, fecha_mov, fecha_ven, imp_sin_iva, porciento_iva, importe_iva, porciento_ret_isr, porciento_ret_iva,";
	$sql .= " codgastos, importe_total, tipo_factura, fecha_captura, iduser, concepto) SELECT num_proveedor, num_cia, num_fact, fecha, fecha, importe, piva,";
	$sql .= " iva, pretencion_isr, pretencion_iva, codgastos, total, tipo_factura, fecha_captura, iduser, concepto FROM facturas WHERE num_proveedor = $_POST[num_pro] AND";
	$sql .= " num_fact = '$_POST[num_fact]';\n";
	$sql .= "DELETE FROM pasivo_proveedores WHERE num_proveedor = $_POST[num_pro] AND num_fact = '$_POST[num_fact]';\n";
	$sql .= "DELETE FROM facturas WHERE num_proveedor = $_POST[num_pro] AND num_fact = '$_POST[num_fact]';\n";
	// [28-Marzo-2008] Borrar de facturas pendientes
	$sql .= "DELETE FROM facturas_pendientes WHERE num_proveedor = $_POST[num_pro] AND num_fact = '$_POST[num_fact]';\n";
	
	$sql .= '
		UPDATE
			fruta_remisiones
		SET
			num_fact = NULL,
			idfac = NULL,
			tsfac = NULL
		WHERE
			num_proveedor = ' . $_POST['num_pro'] . '
			AND num_fact = \'' . $_POST['num_fact'] . '\'
	' . ";\n";
	
	$sql .= '
		UPDATE
			huevo_remisiones
		SET
			num_fact = NULL,
			idfac = NULL,
			tsfac = NULL
		WHERE
			num_proveedor = ' . $_POST['num_pro'] . '
			AND num_fact = \'' . $_POST['num_fact'] . '\'
	' . ";\n";
	
	$db->query($sql);
	
	header("location: ./fac_fac_can_v2.php");
	die;
}

if (isset($_GET['num_pro'])) {
	$sql = "SELECT f.num_proveedor AS num_pro, cp.nombre AS nombre_pro, f.num_cia AS num_cia, cc.nombre_corto AS nombre_cia, num_fact, f.fecha, f.total,";
	$sql .= " piva AS por_iva FROM facturas AS f LEFT JOIN catalogo_proveedores as cp USING (num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia)";
	$sql .= " WHERE f.num_proveedor = $_GET[num_pro] AND num_fact = '$_GET[num_fact]' LIMIT 1";
	$fac = $db->query($sql);
	
	if (!$fac) {
		header("location: ./fac_fac_can_v2.php?codigo_error=1");
		die;
	}
	
	$tpl->newBlock("result");
	if (isset($_GET['desc'])) $tpl->assign('desc', '<input name="desc" type="hidden" id="desc" value="1" />');
	foreach ($fac as $reg)
		foreach ($reg as $key => $value)
			$tpl->assign($key, $key != 'total' ? $value : number_format($value, 2, ".", ","));
	
	$sql = "
		SELECT
			emp.codmp,
			cmp.nombre,
			emp.contenido,
			tp.descripcion
				AS unidad,
			emp.cantidad,
			emp.precio,
			emp.pdesc1
				AS desc1,
			emp.pdesc2
				AS desc2,
			emp.pdesc3
				AS desc3,
			emp.piva
				AS iva,
			emp.ieps,
			emp.importe,
			emp.regalado
		
		FROM
			entrada_mp emp
			LEFT JOIN catalogo_productos_proveedor cpp
				USING (num_proveedor, codmp, contenido, precio)
			LEFT JOIN catalogo_mat_primas cmp
				USING (codmp)
			LEFT JOIN tipo_presentacion tp
				ON (tp.idpresentacion = cpp.presentacion)
		WHERE
			emp.num_proveedor = $_GET[num_pro]
			AND emp.num_fact = '$_GET[num_fact]'
		ORDER BY
			emp.id";
	$mp = $db->query($sql);
	
	$sql = "SELECT num_tanque, capacidad, precio_unit AS precio, litros, porc_inic, porc_final, total AS importe FROM factura_gas LEFT JOIN catalogo_tanques USING (num_cia, num_tanque)";
	$sql .= " WHERE num_proveedor = $_GET[num_pro] AND num_fact = '$_GET[num_fact]' ORDER BY num_tanque";
	$tan = $db->query($sql);
	
	if ($mp) {
		$tpl->newBlock("productos");
		$tpl->assign("result.tipo", 1);
		$total = 0;
		foreach ($mp as $pro) {
			$tpl->newBlock("pro");
			$tpl->assign("codmp", $pro['codmp']);
			$tpl->assign("nombre", $pro['nombre']);
			$tpl->assign("contenido", $pro['contenido'] != 0 ? number_format($pro['contenido']) : "&nbsp;");
			$tpl->assign("unidad", $pro['unidad']);
			$tpl->assign("cantidad", $pro['cantidad'] != 0 ? number_format($pro['cantidad']) : "&nbsp;");
			$tpl->assign("precio", $pro['precio'] != 0 ? number_format($pro['precio'], 4, ".", ",") : "&nbsp;");
			$tpl->assign("desc1", $pro['desc1'] != 0 ? "%" . number_format($pro['desc1']) : "&nbsp;");
			$tpl->assign("desc2", $pro['desc2'] != 0 ? "%" . number_format($pro['desc2']) : "&nbsp;");
			$tpl->assign("desc3", $pro['desc3'] != 0 ? "%" . number_format($pro['desc3']) : "&nbsp;");
			$tpl->assign("iva", $pro['iva'] != 0 ? "%" . number_format($pro['iva']) : "&nbsp;");
			$tpl->assign("ieps", $pro['ieps'] != 0 ? "%" . number_format($pro['ieps']) : "&nbsp;");
			
			$importe = $pro['importe'];
			$importe = $pro['desc1'] != 0 ? $importe * (1 - $pro['desc1'] / 100) : $importe;
			$importe = $pro['desc2'] != 0 ? $importe * (1 - $pro['desc2'] / 100) : $importe;
			$importe = $pro['desc3'] != 0 ? $importe * (1 - $pro['desc3'] / 100) : $importe;
			
			$importe = $pro['iva'] != 0 ? $importe * (1 + $pro['iva'] / 100) : $importe;
			$importe = $pro['ieps'] != 0 ? $importe * (1 + $pro['ieps'] / 100) : $importe;
			$total += $pro['regalado'] == 't' ? 0 : $importe;
			
			$tpl->assign("importe", $pro['regalado'] == 't' ? "REGALADO" : number_format($importe, 2, ".", ","));
			$tpl->assign("productos.total", number_format($total, 2, ".", ","));
		}
	}
	else if ($tan) {
		$tpl->newBlock("tanques");
		$tpl->assign("result.tipo", 2);
		$total = 0;
		foreach ($tan as $t) {
			$tpl->newBlock("tanque");
			$tpl->assign("num_tanque", $t['num_tanque']);
			$tpl->assign("capacidad", number_format($t['capacidad']));
			$tpl->assign("precio", number_format($t['precio'], 4, ".", ","));
			$tpl->assign("iva", $fac[0]['por_iva'] != 0 ? "%" . number_format($fac[0]['por_iva']) : "&nbsp;");
			$tpl->assign("litros", number_format($t['litros']));
			$tpl->assign("porc_ini", number_format($t['porc_inic']));
			$tpl->assign("porc_fin", number_format($t['porc_final']));
			$tpl->assign("importe", number_format($t['importe'], 2, ".", ","));
			
			$total += $t['importe'];
			
			$tpl->assign("tanques.total", number_format($total, 2, ".", ","));
		}
	}
	else
		$tpl->assign("tipo", 0);
	
	$sql = "SELECT fecha_cheque, cuenta, folio_cheque FROM facturas_pagadas WHERE num_proveedor = $_GET[num_pro] AND num_fact = '$_GET[num_fact]'";
	$che = $db->query($sql);
	
	if ($che) {
		$tpl->newBlock("pagado");
		$tpl->assign("result.status", 1);
		$tpl->assign("fecha", $che[0]['fecha_cheque']);
		$tpl->assign("banco", $che[0]['cuenta'] == 1 ? "BANORTE" : "SANTANDER");
		$tpl->assign("folio", $che[0]['folio_cheque']);
	}
	else
		$tpl->assign("result.status", 0);
	
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("datos");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
}

$tpl->printToScreen();
?>
