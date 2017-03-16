<?php
// CONTROL DE BLOCKS
// Tabla 'BLOCKS'
// Menu

//define ('IDSCREEN',1620); //ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/dbstatus.php';
include './includes/class.TemplatePower.inc.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No existen registros para modificar";
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_mnc_con.tpl");
$tpl->prepare();
//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// Seleccionar tabla
// Generar listado de turnos
// Si viene de una página que genero error

if(!isset($_GET['anio']))
{
	$tpl->newBlock("obtener_anio");
	$tpl->assign("anio_actual",date("Y"));
	
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
	die();
}




$fecha1="1/1/".$_GET['anio'];
$fecha2="31/12/".$_GET['anio'];
$sql=
"
SELECT 
num_cia,
nombre,
concepto,
fecha,
importe,
fecha_con,
fecha_con - fecha as diferencia_dias
FROM estado_cuenta JOIN catalogo_companias using(num_cia)
where";
$sql .= in_array($_SESSION['iduser'], $users) ? " num_cia BETWEEN 900 AND 950 AND" : "";
$sql .= " fecha between '$fecha1' and '$fecha2' and cod_mov in(1,16,44) and (fecha_con - fecha) > 30 order by num_cia,fecha";

$movimientos=ejecutar_script($sql,$dsn);

$tpl->newBlock("listado");
$tpl->assign("anio",$_GET['anio']);
$aux=0;
for($i=0;$i<count($movimientos);$i++){
	if($movimientos[$i]['num_cia'] != $aux){
		$tpl->newBlock("cias");
		$tpl->assign("num_cia",$movimientos[$i]['num_cia']);
		$tpl->assign("nombre_cia",$movimientos[$i]['nombre']);
		$aux = $movimientos[$i]['num_cia'];
	}
	$tpl->newBlock("rows");
	$tpl->assign("concepto",$movimientos[$i]['concepto']);
	$tpl->assign("fecha_mov",$movimientos[$i]['fecha']);
	$tpl->assign("fecha_con",$movimientos[$i]['fecha_con']);
	$tpl->assign("importe",number_format($movimientos[$i]['importe'],2,'.',','));
	$tpl->assign("dias",$movimientos[$i]['diferencia_dias']);
}


$tpl->printToScreen();
?>