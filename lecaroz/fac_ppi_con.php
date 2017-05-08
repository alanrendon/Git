<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "";

$db = new DBclass($dsn, "autocommit=yes");

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_ppi_con.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

$sql = "SELECT num_cia, nombre_corto, num_fact, entrada_mp_temp.num_proveedor, catalogo_proveedores.nombre AS nombre_proveedor, fecha, codmp, catalogo_mat_primas.nombre AS nombre_mp, cantidad, precio_unidad";
$sql .= " FROM entrada_mp_temp LEFT JOIN catalogo_proveedores USING (num_proveedor) LEFT JOIN catalogo_companias USING (num_cia) LEFT JOIN catalogo_mat_primas USING (codmp)";
$sql .= " ORDER BY num_cia,fecha,codmp";
$result = $db->query($sql);

$tpl->assign("dia", date("d"));
$tpl->assign("mes", mes_escrito(date("n")));
$tpl->assign("anio", date("Y"));

if ($result) {
	$num_cia = NULL;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
		}
		$tpl->newBlock("fila");
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("proveedor", $result[$i]['nombre_proveedor']);
		$tpl->assign("producto", $result[$i]['nombre_mp']);
		$tpl->assign("cantidad", number_format($result[$i]['cantidad']));
		$tpl->assign("importe", number_format($result[$i]['cantidad'] * $result[$i]['precio_unidad'], 2, ".", ","));
	}
}
else
	$tpl->newBlock("no_result");

$tpl->printToScreen();
$db->desconectar();
?>