<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

$descripcion_error[1] = "No hay resultados";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/zap/zap_cop_fac.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_POST['num_cia'])) {
	$update = "";
	$facts = array();
	$cont = 0;
	$anio = date('Y');
	for ($i = 0; $i < count($_POST['num_cia']); $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['num_pro'][$i] > 0 && $_POST['num_fact'][$i] != '') {
			$sql = "SELECT fz.id, num_cia, cc.nombre_corto AS nombre_cia, fz.num_proveedor AS num_pro, cp.nombre AS nombre_pro, num_fact FROM facturas_zap AS fz LEFT JOIN";
			$sql .= " catalogo_proveedores AS cp ON (cp.num_proveedor = fz.num_proveedor) LEFT JOIN catalogo_companias AS cc USING (num_cia) WHERE fz.num_proveedor";
			$sql .= " = {$_POST['num_pro'][$i]} AND num_fact = '{$_POST['num_fact'][$i]}' AND tspago IS NULL AND copia_fac = 'FALSE' ORDER BY fecha DESC LIMIT 1";
			$fact = $db->query($sql);
			
			if ($fact) {
				if ($_POST['num_cia'][$i] == $fact[0]['num_cia'])
					$update .= "UPDATE facturas_zap SET copia_fac = 'TRUE' WHERE id = {$fact[0]['id']};\n";
				else {
					$facs[$cont]['num_cia_cap'] = $_POST['num_cia'][$i];
					$facs[$cont]['nombre_cia_cap'] = $_POST['nombre_cia'][$i];
					$facs[$cont]['num_cia_fac'] = $fact[0]['num_cia'];
					$facs[$cont]['nombre_cia_fac'] = $fact[0]['nombre_cia'];
					$facs[$cont]['num_pro'] = $_POST['num_pro'][$i];
					$facs[$cont]['nombre_pro'] = $_POST['nombre_pro'][$i];
					$facs[$cont]['num_fact'] = $_POST['num_fact'][$i];
					$facs[$cont]['status'] = 2;	// No corresponde la compañía de la factura con la compañía capturada
					$cont++;
				}
			}
			else {
				$facs[$cont]['num_cia_cap'] = $_POST['num_cia'][$i];
				$facs[$cont]['nombre_cia_cap'] = $_POST['nombre_cia'][$i];
				$facs[$cont]['num_cia_fac'] = NULL;
				$facs[$cont]['nombre_cia_fac'] = "NO EXISTE LA FACTURA";
				$facs[$cont]['num_pro'] = $_POST['num_pro'][$i];
				$facs[$cont]['nombre_pro'] = $_POST['nombre_pro'][$i];
				$facs[$cont]['num_fact'] = $_POST['num_fact'][$i];
				$facs[$cont]['status'] = 1;	// No existe la factura
				$cont++;
			}
		}
	
	if ($update != "") $db->query($update);
	
	if ($cont == 0) {
		header("location: ./zap_cop_fac.php");
		die;
	}
	else {
		$tpl->newBlock("errores");
		foreach ($facs as $fac) {
			$tpl->newBlock("fac");
			$tpl->assign("num_pro", $fac['num_pro']);
			$tpl->assign("nombre_pro", $fac['nombre_pro']);
			$tpl->assign("factura", $fac['num_fact']);
			$tpl->assign("num_cia_fac", $fac['num_cia_fac']);
			$tpl->assign("nombre_cia_fac", $fac['nombre_cia_fac']);
			$tpl->assign("num_cia_cap", $fac['num_cia_cap']);
			$tpl->assign("nombre_cia_cap", $fac['nombre_cia_cap']);
		}
		$tpl->printToScreen();
		die;
	}
}

$numfilas = 50;

$tpl->newBlock("captura");
for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

$cias = $db->query("SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 998 ORDER BY num_cia");
foreach ($cias as $cia) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia['num_cia']);
	$tpl->assign("nombre", $cia['nombre_corto']);
}

$pros = $db->query("SELECT num_proveedor AS num_pro, nombre FROM catalogo_proveedores ORDER BY num_pro");
foreach ($pros as $pro) {
	$tpl->newBlock("pro");
	$tpl->assign("num_pro", $pro['num_pro']);
	$tpl->assign("nombre", $pro['nombre']);
}

$tpl->printToScreen();
?>