<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

if (isset($_GET['num_cia'])) {
	$fecha1 = "01/$_GET[mes]/$_GET[anio]";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	ereg("([0-9]{1,2})/([0-9]{1,2})/([0-9]{2,4})", $_GET['fecha_fac'], $fecha_fac);
	
	$sql = "SELECT num_cia, nombre, direccion, rfc, litros, fecha FROM facturas_rancho_litros LEFT JOIN catalogo_companias USING (num_cia) WHERE fecha BETWEEN '$fecha1' AND '$fecha2' AND facturas_rancho_litros.status = 0";
	$sql .= $_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "";
	// Linea provisional
	//$sql .= " AND id IN (306, 307)";
	//******************
	$sql .= " ORDER BY num_cia, fecha";
	$result = $db->query($sql);
	
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower("./plantillas/pan/factura_rancho_v2.tpl");
	$tpl->prepare();
	
	if (!$result) {
		$tpl->newBlock("cerrar");
		$tpl->printToScreen();
		die;
	}
	
	$tmp = $db->query("SELECT precio FROM precio_fac_rancho");
	$precio = $tmp ? $tmp[0]['precio'] : 0;
	
	$num_cia = NULL;
	$numfilas = 9;
	foreach ($result as $reg) {
		if ($num_cia != $reg['num_cia']) {
			$num_cia = $reg['num_cia'];
			
			$cia = $reg['nombre'];
			$dir = $reg['direccion'];
			$rfc = $reg['rfc'];
			$filas = $numfilas + 1;
		}
		if ($filas > $numfilas) {
			$tpl->newBlock("factura");
			$tpl->assign("cia", $cia);
			$tpl->assign("dir", $dir);
			$tpl->assign("rfc", $rfc);
			$tpl->assign("dia", $fecha_fac[1]);
			$tpl->assign("mes", $fecha_fac[2]);
			$tpl->assign("anio", $fecha_fac[3]);
			
			$total = 0;
			$filas = 1;
		}
		$tpl->assign("litros" . $filas, number_format($reg['litros'], 0, ".", ","));
		$tpl->assign("descripcion" . $filas, "LITROS DE LECHE: $reg[fecha]");
		$tpl->assign("precio" . $filas, number_format($precio, 2, ".", ","));
		$tpl->assign("importe" . $filas, number_format($reg['litros'] * $precio, 2, ".", ","));
		
		$filas++;
		
		$total += $reg['litros'] * $precio;
		$tpl->assign("total", number_format($total, 2, ".", ","));
	}
	$tpl->printToScreen();
	
	// Insertar movimientos de inventario
//	$sql = "INSERT INTO mov_inv_real (num_cia, codmp, fecha, tipo_mov, cantidad, precio, total_mov, precio_unidad, descripcion, num_proveedor)";
//	$sql .= " SELECT num_cia, 580, fecha, 'FALSE', litros, $precio, litros * $precio, $precio, 'COMPRA F. NO.' || num_fact, 617 FROM facturas_rancho_litros";
//	$sql .= " WHERE status = 0" . ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "") . ";\n";
	// Insertar facturas
	$sql = "INSERT INTO facturas (num_proveedor, num_cia, num_fact, fecha, importe, piva, codgastos, total, tipo_factura, fecha_captura, iduser,";
	$sql .= " concepto) SELECT 617, num_cia, num_fact::varchar(50), fecha, litros * $precio, 0, 33, litros * $precio, 0, CURRENT_DATE, $_SESSION[iduser], 'FACTURA MATERIA PRIMA'";
	$sql .= " FROM facturas_rancho_litros WHERE status = 0 AND fecha BETWEEN '$fecha1' AND '$fecha2'" . ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "") . ";\n";
	// Insertar en pasivo
	$sql .= "INSERT INTO pasivo_proveedores (num_cia, num_fact, total, descripcion, fecha, num_proveedor, codgastos)";
	$sql .= " SELECT num_cia, num_fact::varchar(50), litros * $precio, 'FACTURA MATERIA PRIMA', fecha, 617, 33 FROM facturas_rancho_litros WHERE status = 0 AND fecha BETWEEN '$fecha1' AND '$fecha2'" . ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "") . ";\n";
	// Insertar entradas de facturas
	$sql .= "INSERT INTO entrada_mp (num_fact, num_cia, codmp, fecha, contenido, pdesc1, pdesc2, piva, num_proveedor,";
	$sql .= " pdesc3, pagado, importe, precio, iduser, cantidad, regalado) SELECT num_fact::varchar(50), num_cia, 580, fecha, 20,";
	$sql .= " 0, 0, 0, 617, 0, 'FALSE', litros * $precio, $precio * 20, $_SESSION[iduser], litros / 20, 'FALSE' FROM facturas_rancho_litros";
	$sql .= " WHERE status = 0 AND fecha BETWEEN '$fecha1' AND '$fecha2'" . ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "") . ";\n";
	// Actualizar inventario
//	foreach ($result as $reg)
//		if ($id = $db->query("SELECT idinv FROM inventario_real WHERE num_cia = $reg[num_cia] AND codmp = 580"))
//			$sql .= "UPDATE inventario_real SET existencia = existencia + $reg[litros], precio_unidad = $precio WHERE idinv = {$id[0]['idinv']};\n";
//		else
//			$sql .= "INSERT INTO inventario_real (num_cia, codmp, existencia, precio_unidad) VALUES ($reg[num_cia], 580, $reg[litros], $precio);\n";
	// Actualizar captura de facturas
	$sql .= "UPDATE facturas_rancho_litros SET status = 1, tsreg = now() WHERE status = 0 AND fecha BETWEEN '$fecha1' AND '$fecha2'" . ($_GET['num_cia'] > 0 ? " AND num_cia = $_GET[num_cia]" : "") . ";\n";
	$db->query($sql);
	//echo "<pre>$sql</pre>";
	die;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower("./plantillas/header.tpl");

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/pan/pan_fac_ran_imp.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$precio = $db->query("SELECT precio FROM precio_fac_rancho");
$tpl->assign("precio", $precio ? number_format($precio[0]['precio'], 2, ".", "") : "");

$tpl->assign(date("n"), " selected");
$tpl->assign("anio", date("Y"));

$tpl->printToScreen();
?>