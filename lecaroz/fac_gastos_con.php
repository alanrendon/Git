<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No se encontraron registros";
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
$tpl->assignInclude("body","./plantillas/fac/fac_gastos_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['estado'])) {
	$tpl->newBlock("obtener_datos");
	$tpl->assign("tabla","catalogo_mat_primas");

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
	die();
}
//------------------------------------------------------------------
$sql="SELECT * FROM catalogo_gastos";
$tpl->newBlock("listado");

if($_GET['estado']==0){
	$tpl->assign("concepto","NO INCLUIDOS");
	$sql.=" WHERE codigo_edo_resultados = 0";
	}
else if($_GET['estado']==1){
	$tpl->assign("concepto","DE OPERACION");
	$sql.=" WHERE codigo_edo_resultados = 0";
	}
else if($_GET['estado']==2){
	$tpl->assign("concepto","GENERALES");
	$sql.=" WHERE codigo_edo_resultados = 0";
	}
else{
	$tpl->newBlock("estado");
	$tpl->gotoBlock("listado");
}

if($_GET['orden']==0)
	$sql.=" order by descripcion";
else
	$sql.=" order by codgastos";

$gastos=ejecutar_script($sql,$dsn);
$estado=ejecutar_script("select * from catalogo_edo_cod_res order by idcodigoedoresultados",$dsn);


if(!$gastos)
{
	header("location: ./fac_gastos_con.php?codigo_error=1");
	die();
}

for($i=0;$i<count($gastos);$i++){
	$tpl->newBlock("rows");
	$tpl->assign("codgastos",$gastos[$i]['codgastos']);
	$tpl->assign("nombre",strtoupper($gastos[$i]['descripcion']));
	if($gastos[$i]['tipo_gasto']=='t')
		$tpl->assign("tipo","Fijo");
	else
		$tpl->assign("tipo","Variable");
	$tpl->assign("ap", $gastos[$i]['aplicacion_gasto'] == "f" ? "Panadería" : "Reparto");
	if($_GET['estado']==3){
		$tpl->newBlock("edo_resul");
		$tpl->assign("edo_resul",strtoupper($estado[$gastos[$i]['codigo_edo_resultados']]['descripcion']));
	}

}

$tpl->printToScreen();

?>