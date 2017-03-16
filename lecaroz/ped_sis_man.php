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

$numfilas = 20;

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ped/ped_sis_man.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

function buscar_producto($array, $codmp, $num_proveedor) {
	$num_elementos = count($array);
	if ($num_elementos < 1)
		return FALSE;
	
	$pro = array();
	$count = 0;
	for ($i=0; $i<$num_elementos; $i++)
		if ($array[$i]['codmp'] == $codmp && $array[$i]['num_proveedor'] == $num_proveedor)
			return $i;
	
	return FALSE;
}

if (isset($_POST['num_cia'])) {
	// Obtener listado de productos por proveedor
	$sql = "SELECT num_proveedor,codmp,contenido,porcentaje,unidadconsumo FROM catalogo_productos_proveedor LEFT JOIN catalogo_mat_primas USING (codmp)";
	$sql .= " ORDER BY codmp,num_proveedor,porcentaje";
	$por = $db->query($sql);
	
	$mes = date("n");
	$anio = date("Y");
	$sql = "";
	
	for ($i = 0; $i < $numfilas; $i++) {
		if ($_POST['num_cia'][$i] > 0 && $_POST['codmp'][$i] > 0 && $_POST['num_proveedor'][$i] > 0 && $_POST['cantidad'][$i] > 0) {
			$total_pedido = 0;
			
			$index = buscar_producto($por, $_POST['codmp'][$i], $_POST['num_proveedor'][$i]);
			if ($index !== FALSE) {
				$unidad = $por[$index]['unidadconsumo'];
				$contenido = $por[$index]['contenido'];
			}
			else {
				$unidad = "NULL";
				$contenido = "NULL";
			}
			
			$sql .= "INSERT INTO pedidos (num_cia,num_proveedor,codmp,mes,anio,cantidad,unidad,contenido) VALUES ({$_POST['num_cia'][$i]},{$_POST['num_proveedor'][$i]},{$_POST['codmp'][$i]},$mes,$anio,{$_POST['cantidad'][$i]},$unidad,$contenido);\n";
		}
	}
	
	$db->query($sql);
	$db->desconectar();
	
	header("location: ./ped_sis_man.php");
	die;
}

$tpl->newBlock("captura");

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias WHERE num_cia <= 300 ORDER BY num_cia";
$cia = $db->query($sql);
for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

//$sql = "SELECT codmp, nombre FROM catalogo_mat_primas WHERE tipo_cia = 'TRUE' ORDER BY codmp";
$sql = "SELECT codmp, nombre FROM catalogo_productos_proveedor LEFT JOIN catalogo_mat_primas USING (codmp) GROUP BY codmp, nombre ORDER BY codmp";
$mp = $db->query($sql);
for ($i = 0; $i < count($mp); $i++) {
	$tpl->newBlock("mp");
	$tpl->assign("codmp", $mp[$i]['codmp']);
	$tpl->assign("nombre_mp", str_replace("\"", "\\\"", $mp[$i]['nombre']));
}

$sql = "SELECT num_proveedor, nombre FROM catalogo_proveedores ORDER BY num_proveedor";
$pro = $db->query($sql);
for ($i = 0; $i < count($pro); $i++) {
	$tpl->newBlock("pro");
	$tpl->assign("pro", $pro[$i]['num_proveedor']);
	$tpl->assign("nombre_pro", str_replace(array("\""), array(""), $pro[$i]['nombre']));
}

for ($i = 0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
}

$tpl->printToScreen();
$db->desconectar();
?>