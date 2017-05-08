<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos registrados usuarios";
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
$tpl->assignInclude("body","./plantillas/adm/adm_efe_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");



if(isset($_POST['cont'])){
	for($i=0;$i<$_POST['cont'];$i++){
		if($_POST['modificar'.$i] == 1)
			ejecutar_script("delete from permiso_revision where id_user=".$_POST['id'.$i],$dsn);
	}
	header("location: ./adm_efe_con.php");
	die();
}

	
// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message", $descripcion_error[$_GET['codigo_error']]);	
	$tpl->printToScreen();
	die();

}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
	$tpl->printToScreen();

}


//$tpl->newBlock('listado');
$tpl->assign("tabla","catalogo_accionistas");

$sql="select iduser, nombre from permiso_revision join catalogo_operadoras on(iduser=id_user)";

$reg=ejecutar_script($sql,$dsn);
//print_r ($reg);
$tpl->assign("count",count($reg));
if($reg){
	for($i=0;$i<count($reg);$i++){
		$tpl->newBlock("rows");
		$tpl->assign("i",$i);
		$tpl->assign('id',$reg[$i]['iduser']);
		$tpl->assign('nombre',$reg[$i]['nombre']);
	}
}
else
{
	header("location: ./adm_efe_con.php?codigo_error=1");
	die;
}
// Imprimir el resultado
$tpl->printToScreen();


?>