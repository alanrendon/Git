<?php
// CONSULTA DE GASTOS
// Tabla 'Rosticerías'
// Menu 'Rosticerías->Producción'
//define ('IDSCREEN',1241); // ID de pantalla
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';
// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

// --------------------------------- Generar pantalla --------------------------------------------------------

$tpl = new TemplatePower( "./plantillas/header.tpl" );
$tpl->assignInclude("body","./plantillas/ros/ros_gas_minimod.tpl");
$tpl->prepare();


if (isset($_GET['codgastos'])) {
	$sql = "UPDATE movimiento_gastos SET codgastos=".$_GET['codgastos'].", fecha='".$_GET['fecha']."' WHERE idmovimiento_gastos=".$_GET['idmovimiento_gastos'];
	ejecutar_script($sql,$dsn);
	if($_GET['num_cia'] < 101 or ($_GET['num_cia']==702 or $_GET['num_cia']==703 or $_GET['num_cia']==999)) 
	{
		$sql="UPDATE total_panaderias set gastos=gastos - ".$_GET['importe'].", efectivo=efectivo + ".$_GET['importe']." where num_cia=".$_GET['num_cia']." and fecha='".$_GET['fecha_oculta']."'";
		ejecutar_script($sql,$dsn);
		$sql="UPDATE total_panaderias set gastos=gastos + ".$_GET['importe'].", efectivo=efectivo - ".$_GET['importe']." where num_cia=".$_GET['num_cia']." and fecha='".$_GET['fecha']."'";
		ejecutar_script($sql,$dsn);
	}
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();	
	die;
}

// Incluir el cuerpo del documento
$tpl->newBlock("modificar");
$tpl->assign("dia_actual",date("d"));
$tpl->assign("mes_actual",date("m"));
$tpl->assign("anio_actual",date("Y"));
$tpl->assign("id",$_GET['id']);
$datos = ejecutar_script("SELECT * FROM movimiento_gastos WHERE idmovimiento_gastos = $_GET[id]",$dsn);
$tpl->assign("codgastos",$datos[0]['codgastos']);
$tpl->assign("num_cia",$datos[0]['num_cia']);
$tpl->assign("fecha",$datos[0]['fecha']);
$tpl->assign("concepto",$datos[0]['concepto']);
$tpl->assign("importe",number_format($datos[0]['importe'],2,".",","));
$tpl->assign("importe1",number_format($datos[0]['importe'],2,".",""));

if (ejecutar_script('
	SELECT
		idoperadora
	FROM
		catalogo_operadoras
	WHERE
		iduser IS NOT NULL
		AND iduser = ' . $_SESSION['iduser'] . '
', $dsn)) {
	$tpl->assign('readonly', ' readonly="readonly"');
}

$_fecha=explode("/",$datos[0]['fecha']);
//echo  $_fecha[1]."/".$_fecha[2];
if($_fecha[1] == date("m") and $_fecha[2] == date("Y"))
	$tpl->assign("estado"," ");
else if($_fecha[1] == (date("m") - 1) and $_fecha[2]==date("Y")){
	if(date("d")==1 or date("d")==2 or date("d")==3 or date("d")==4)
		$tpl->assign("estado"," ");
	else
		$tpl->assign("estado","readonly");
}
else
	$tpl->assign("estado","readonly");


$tpl->printToScreen();

?>