<?php
// LISTADO DE ENTRADAS DE MATERIA PRIMA POR PROVEEDOR MENSUAL

include './includes/class.db.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

$session = new sessionclass($dsn);

$descripcion_error[1] = "No hay resultados";

function buscar($array, $num_cia, $num_proveedor) {
	$num_elementos = count($array);	// Contar número de elementos en el arreglo
	
	// Recorrer array
	for ($i=0; $i<$num_elementos; $i++)
		if ($array[$i]['num_cia'] == $num_cia && $array[$i]['num_proveedor'] == $num_proveedor)
			return $array[$i]['entrada'];
	
	// Se llego al final del array y no se encontro registro
	return 0;
}

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/fac/fac_pro_pro.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['codmp'])) {
	$tpl->newBlock("datos");
	$tpl->assign(date("n", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))), "selected");
	$tpl->assign("anio", date("Y", mktime(0, 0, 0, date("m") - 1, 1, date("Y"))));
	
	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message", $descripcion_error[$_GET['codigo_error']]);	
	}
	
	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}
	$tpl->printToScreen();
	die;
}

// Conectarse a la base de datos
$db = new DBclass($dsn);

if ($_GET['fecha1'] != "" && $_GET['fecha2'] != "") {
	$fecha1 = $_GET['fecha1'];
	$fecha2 = $_GET['fecha2'];
}
else {
	$fecha1 = "1/$_GET[mes]/$_GET[anio]";
	$fecha2 = date("d/m/Y", mktime(0, 0, 0, $_GET['mes'] + 1, 0, $_GET['anio']));
	$mes = $_GET['mes'];
}

// Obtener proveedores que tuvieron movimientos en el mes
$sql = "SELECT num_proveedor, nombre FROM entrada_mp LEFT JOIN catalogo_proveedores USING (num_proveedor) WHERE codmp = $_GET[codmp] AND fecha BETWEEN '$fecha1' and '$fecha2' GROUP BY num_proveedor, nombre ORDER BY num_proveedor";
$proveedores = $db->query($sql);

if (!$proveedores) {
	header("location: ./fac_pro_pro.php?codigo_error=1");
	die;
}

// Obtener las compañias que tuvieron entradas
$sql = "SELECT num_cia, nombre_corto FROM entrada_mp LEFT JOIN catalogo_companias USING (num_cia) WHERE codmp = $_GET[codmp] AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia, nombre_corto ORDER BY num_cia";
$cias = $db->query($sql);

// Obtener entradas del mes por compañía, proveedor
$sql = "SELECT num_cia, num_proveedor, sum(cantidad * contenido) " . ($_GET['codmp'] == 1 ? "/ 44" : ($_GET['codmp'] == 3 || $_GET['codmp'] == 4 ? "/ 50" : ""))." AS entrada FROM entrada_mp WHERE codmp = $_GET[codmp] AND fecha BETWEEN '$fecha1' AND '$fecha2' GROUP BY num_cia,num_proveedor ORDER BY num_cia, num_proveedor";
$entradas = $db->query($sql);

$tpl->newBlock("listado");
$nombre_mp = $db->query("SELECT nombre FROM catalogo_mat_primas WHERE codmp = $_GET[codmp]");
$tpl->assign("nombre_mp", ucfirst(strtolower($nombre_mp[0]['nombre'])));
$tpl->assign("intervalo", $_GET['fecha1'] != "" && $_GET['fecha2'] != "" ? "del '$fecha1' al '$fecha2'" : "al mes de " . mes_escrito($_GET['mes']) . " de $_GET[anio]");

// Crear titulos
$total_pro = array();
for ($i = 0; $i < count($proveedores); $i++) {
	$tpl->newBlock("proveedor");
	$tpl->assign("proveedor", $proveedores[$i]['nombre']);
	$total_pro[$i] = 0;
}

// Mostrar entradas
$gran_total = 0;
for ($i = 0; $i < count($cias); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("num_cia", $cias[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cias[$i]['nombre_corto']);
	
	// Buscar entradas por proveedor
	$total = 0;
	for ($j=0; $j<count($proveedores); $j++) {
		$tpl->newBlock("entrada");
		$entrada = buscar($entradas,$cias[$i]['num_cia'],$proveedores[$j]['num_proveedor']);
		$tpl->assign("entrada",$entrada != 0 ? number_format($entrada,2,".",",") : "&nbsp;");
		
		$total += $entrada;
		$total_pro[$j] += $entrada;
		$gran_total += $entrada;
	}
	$tpl->assign("fila.total",number_format($total,2,".",","));
}
$tpl->assign("listado.total",number_format($gran_total,2,".",","));
for ($i=0; $i<count($total_pro); $i++) {
	$tpl->newBlock("total_pro");
	$tpl->assign("total",number_format($total_pro[$i],2,".",","));
}

$db->desconectar();
$tpl->printToScreen();
?>