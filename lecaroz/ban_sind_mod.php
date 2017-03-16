<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos productos registrados para este proveedor";
//$descripcion_error[2] = "Número de Gasto no existe en la Base de Datos, revisa bien codigo del gasto";

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);
// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);
// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();
// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );
// Incluir el cuerpo del documento
//$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->assignInclude("body","./plantillas/ban/ban_sind_mod.tpl");
$tpl->prepare();
// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

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

$tpl->assign("tabla","catalogo_sindicatos");


$var=0;
if($_POST['cont'] >0)
{
	for($i=0;$i<$_POST['cont'];$i++)
	{
		if($_POST['modificar'.$i]==1)
		{
			$tpl->newBlock("rows");
			$tpl->assign("i",$var);
			$var++;
			$tpl->assign('idsindicato',$_POST['idsindicato'.$i]);
			$cia = obtener_registro("catalogo_sindicatos",array("idsindicato"),array($_POST['idsindicato'.$i]),"","",$dsn);

			$tpl->assign('nombre_sindicato',$cia[0]['nombre_sindicato']);
		}
	}
	$tpl->newBlock("contador");
	$tpl->assign("cont",$var);
	
}
else
{
	header("location: ./ban_sind_con.php");
	die;
}
// Imprimir el resultado
$tpl->printToScreen();

?>