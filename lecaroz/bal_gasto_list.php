<?php
// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay resultados";
$descripcion_error[2] = "No existe el gasto";
$descripcion_error[3] = "No existe la compa��a";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body", "./plantillas/bal/bal_gasto_list.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt", "$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['anio'])) {
	$tpl->newBlock("datos");
	$tpl->assign("anio", date("Y"));
	
	// Si viene de una p�gina que genero error
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

//$db = new DBclass($dsn);

if($_GET['num_cia'] != ""){
	if(!existe_registro("catalogo_companias",array("num_cia"),array($_GET['num_cia']),$dsn)){
		header("location: ./bal_gasto_list.php?codigo_error=3");
		die();
	}
}

if(!existe_registro("catalogo_gastos",array("codgastos"),array($_GET['codgasto']),$dsn)){
	header("location: ./bal_gasto_list.php?codigo_error=3");
	die();
}


$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE " . ($_GET['num_cia'] > 0 ? "num_cia = $_GET[num_cia]" : "num_cia < 200") . " ORDER BY num_cia";
//$cia = $db->query($sql);
$cia = ejecutar_script($sql,$dsn);



$tpl->newBlock("listado");
$tpl->assign("anio", $_GET['anio']);
$gasto=obtener_registro("catalogo_gastos",array("codgastos"),array($_GET['codgasto']),"","",$dsn);
$tpl->assign("nombre_gasto",$gasto[0]['descripcion']);

$num_meses = $_GET['anio'] < date("Y") ? 12 : date("n", mktime(0, 0, 0, date("n"), 0, $_GET['anio']));
for($z=1;$z<=12;$z++)
	$t[$z]=0;

$grantotal = 0;
$promedio = 0;

for ($i = 0; $i < count($cia); $i++) {
	if(!existe_registro("movimiento_gastos",array("num_cia","codgastos"),array($cia[$i]['num_cia'],$_GET['codgasto']),$dsn))
		continue;

	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
	
	$total = 0;
	
	for ($j = 1; $j <= $num_meses; $j++) {
		$fecha1 = "1/$j/$_GET[anio]";
		$fecha2 = date("d/m/Y", mktime(0, 0, 0, $j + 1, 0, $_GET['anio']));
		
		$sql = "select sum(importe) from movimiento_gastos where num_cia=".$cia[$i]['num_cia']." and fecha between '".$fecha1."' and '".$fecha2."' and codgastos=$_GET[codgasto]";
//		$temp = $db->query($sql);
		$temp = ejecutar_script($sql,$dsn);
		$pro = $temp ? $temp[0]['sum'] : 0;
		
		$total += $pro;
		
		$t[$j] += $pro;
		$tpl->assign($j, round($pro, 3) != 0 ? number_format($pro, 0, ".", ",") : "&nbsp;");
	}
	$tpl->assign("total", number_format($total, 0, ".", ","));
	$tpl->assign("prom", number_format($total / $num_meses, 0, ".", ","));
	$grantotal += $total;
	$promedio += $total / $num_meses;
}

$tpl->gotoBlock("listado");

for($z=1;$z<=12;$z++){
	if($t[$z] > 0)
		$tpl->assign("t".$z,number_format($t[$z],0,'.',','));
}

$tpl->assign("total", number_format($grantotal, 0, ".", ","));
$tpl->assign("prom", number_format($promedio, 0, ".", ","));

$tpl->printToScreen();
//$db->desconectar();
?>