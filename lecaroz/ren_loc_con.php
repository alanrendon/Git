<?php
include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

$session = new sessionclass($dsn);
$db = new DBclass($dsn, "autocommit=yes");

//if ($_SESSION['iduser'] != 1) die("Modificando pantalla");

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/ren/ren_loc_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['arr'])) {
	if ($_GET['tipo'] == 1) {
		$sql = "SELECT num_local, fecha_inicio, fecha_final, renta_con_recibo AS renta, agua, mantenimiento, retencio_isr, retencion_iva, nombre_arrendatario, bloque, cod_arrendador,";
		$sql .= " nombre, nombre_local FROM catalogo_arrendatarios LEFT JOIN catalogo_arrendadores WHERE status = 1";
		$sql .= $_GET['arr'] > 0 ? " AND cod_arrendador = $_GET[arr]" : "";
		$sql .= $_GET['local'] > 0 ? " AND num_local = $_GET[local]" : "";
		$sql .= " ORDER BY cod_arrendador, num_local";
		$result = $db->query($sql);
		
		if (!$result) {
			header("location: ./ren_loc_con.php?codigo_error=1");
			die;
		}
		
		$tpl->newBlock("por_arr");
		
		$arr = NULL;
		foreach ($result as $reg) {
			if ($arr != $reg['cod_arrendador']) {
				$arr = $reg['cod_arrendador'];
				
				$tpl->newBlock();
			}
		}
	}
}

$tpl->newBlock("datos");

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	$tpl->printToScreen();
	die();
}

$tpl->printToScreen();
?>