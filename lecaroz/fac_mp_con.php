<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No tenemos materias primas";
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
$tpl->assignInclude("body","./plantillas/fac/fac_mp_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['tipo_mat'])) {
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
$sql="SELECT * FROM catalogo_mat_primas WHERE ";
$tpl->newBlock("listado");

if($_GET['materia']==0){
	$sql.="tipo='1' ";
	$tpl->assign("concepto","MATERIAS PRIMAS");
	}
else if($_GET['materia']==1){
	$sql.="tipo='2' ";
	$tpl->assign("concepto","MATERIAL DE EMPAQUE");
	}
else if($_GET['materia']==2){
	if($_GET['control']==0){
		$sql.="controlada='TRUE' ";
		$tpl->assign("concepto","MATERIAS PRIMAS CONTROLADAS");
	}
	else if($_GET['control']==1){
		$sql.="controlada='FALSE' ";
		$tpl->assign("concepto","MATERIAS PRIMAS NO CONTROLADAS");
	}
}

if($_GET['materia']==2){
	if($_GET['orden']==0)
		$sql.="order by tipo,nombre";
	else
		$sql.="order by tipo,codmp";
}
else{
	if($_GET['orden']==0)
		$sql.="order by nombre";
	else
		$sql.="order by codmp";
}
$mat=ejecutar_script($sql,$dsn);

if(!$mat)
{
	header("location: ./fac_mp_con.php?codigo_error=1");
	die();
}
$tipo=$mat[0]['tipo'];
for($i=0;$i<count($mat);$i++){
	$tpl->newBlock("rows");
	if($tipo!=$mat[$i]['tipo']){
		$tpl->newBlock("empaque");
		$tpl->gotoBlock("rows");
	}
	$tipo=$mat[$i]['tipo'];
	$tpl->assign("codmp",$mat[$i]['codmp']);
	$tpl->assign("nombre",$mat[$i]['nombre']);
	$unidad_consumo=obtener_registro("tipo_unidad_consumo",array('idunidad'),array($mat[$i]['unidadconsumo']),"","",$dsn);
	$tpl->assign("unidadconsumo",strtoupper($unidad_consumo[0]['descripcion']));
//	echo $mat[$i]['controlada']."<br>";
	if($mat[$i]['controlada']=='FALSE'){
		$tpl->assign("controlada","NO");
	}
	else{
		$tpl->assign("controlada","SI");
	}
	$presentacion=obtener_registro("tipo_presentacion",array('idpresentacion'),array($mat[$i]['presentacion']),"","",$dsn);
	$tpl->assign("presentacion",strtoupper($presentacion[0]['descripcion']));
	if($mat[$i]['procpedautomat']=='t')
		$tpl->assign("pedido","NO");
	else
		$tpl->assign("pedido","SI");
	$tpl->assign("porcentaje",$mat[$i]['porcientoincremento']);
	$tpl->assign("entregas",$mat[$i]['entregasfinmes']);
	
}

$tpl->printToScreen();

?>