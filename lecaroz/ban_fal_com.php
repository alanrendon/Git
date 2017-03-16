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

$users = array(28, 29, 30, 31);

$numfilas = 30;

// Insertar datos
if (isset($_POST['num_cia'])) {
	$sql = "";
	for ($i = 0; $i < $numfilas; $i++)
		if ($_POST['num_cia'][$i] > 0 && $_POST['fecha'][$i] != "" && $_POST['importe'][$i] > 0 && $_POST['tipo'][$i] != "") {
			$datos['num_cia']     = $_POST['num_cia'][$i];
			$datos['fecha']       = $_POST['fecha'][$i];
			$datos['deposito']    = $_POST['deposito'][$i];
			$datos['importe']     = $_POST['importe'][$i];
			$datos['tipo']        = $_POST['tipo'][$i];
			$datos['descripcion'] = strtoupper($_POST['descripcion'][$i]);
			$datos['imp']         = "FALSE";
			$datos['implis']      = "TRUE";
			
			$sql .= $db->preparar_insert("faltantes_cometra", $datos) . ";\n";
		}
	if ($sql != "")
		$db->query($sql);
	$db->desconectar();
	
	if ($_POST['listado'] == 0)
		header("location: ./ban_fal_com.php");
	else if ($_POST['listado'] == 1)
		header("location: ./ban_fal_com.php?listado=1");
		
	die;
}

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_fal_com.tpl");
$tpl->prepare();

//Seleccionar el script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (isset($_GET['listado'])) {
	$sql = "SELECT num_cia, nombre_corto, fecha, importe, tipo, descripcion, deposito FROM faltantes_cometra LEFT JOIN catalogo_companias USING (num_cia) WHERE implis = 'TRUE' ORDER BY num_cia, fecha, tipo";
	$result = $db->query($sql);
	
	if (!$result) {
		header("location: ./ban_fal_com.php");
		die;
	}
	
	$db->query("UPDATE faltantes_cometra set implis = 'FALSE' WHERE implis = 'TRUE'");
	
	$tpl->newBlock("listado");
	$tpl->assign("dia", date("d"));
	$tpl->assign("mes", mes_escrito(date("n")));
	$tpl->assign("anio", date("Y"));
	
	$num_cia = NULL;
	$faltantes = 0;
	$sobrantes = 0;
	for ($i = 0; $i < count($result); $i++) {
		if ($num_cia != $result[$i]['num_cia']) {
			if ($num_cia != NULL && $count > 1) {
				$tpl->newBlock("totales");
				$tpl->assign("faltante", number_format($faltante, 2, ".", ","));
				$tpl->assign("sobrante", number_format($sobrante, 2, ".", ","));
				$tpl->assign("diferencia", number_format($faltante - $sobrante, 2, ".", ","));
			}
			
			$num_cia = $result[$i]['num_cia'];
			
			$tpl->newBlock("cia");
			$tpl->assign("num_cia", $num_cia);
			$tpl->assign("nombre_cia", $result[$i]['nombre_corto']);
			
			$faltante = 0;
			$sobrante = 0;
			
			$count = 0;
		}
		$tpl->newBlock("fila_lis");
		$tpl->assign("fecha", $result[$i]['fecha']);
		$tpl->assign("deposito", number_format($result[$i]['deposito'], 2, ".", ","));
		$tpl->assign("descripcion", $result[$i]['descripcion']);
		$tpl->assign("faltante", $result[$i]['tipo'] == "f" ? number_format($result[$i]['importe'], 2, ".", ",") : "");
		$tpl->assign("sobrante", $result[$i]['tipo'] == "t" ? number_format($result[$i]['importe'], 2, ".", ",") : "");
		
		$faltante += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : 0;
		$sobrante += $result[$i]['tipo'] == "t" ? $result[$i]['importe'] : 0;
		
		$faltantes += $result[$i]['tipo'] == "f" ? $result[$i]['importe'] : 0;
		$sobrantes += $result[$i]['tipo'] == "t" ? $result[$i]['importe'] : 0;
		
		$count++;
	}
	if ($num_cia != NULL && $count > 1) {
		$tpl->newBlock("totales");
		$tpl->assign("faltante", number_format($faltante, 2, ".", ","));
		$tpl->assign("sobrante", number_format($sobrante, 2, ".", ","));
		$tpl->assign("diferencia", number_format($faltante - $sobrante, 2, ".", ","));
	}
	$tpl->assign("listado.faltantes", number_format($faltantes, 2, ".", ","));
	$tpl->assign("listado.sobrantes", number_format($sobrantes, 2, ".", ","));
	$tpl->assign("listado.diferencia", number_format($faltantes - $sobrantes, 2, ".", ","));
	$tpl->printToScreen();
	die;
}

$tpl->newBlock("captura");

$sql = "SELECT num_cia, nombre_corto FROM catalogo_companias" . (in_array($_SESSION['iduser'], $users) ? " WHERE num_cia BETWEEN 900 AND 950" : " WHERE num_cia BETWEEN 1 AND 800") . " ORDER BY num_cia";
$cia = $db->query($sql);

for ($i = 0; $i < count($cia); $i++) {
	$tpl->newBlock("nombre_cia");
	$tpl->assign("num_cia", $cia[$i]['num_cia']);
	$tpl->assign("nombre_cia", $cia[$i]['nombre_corto']);
}

$fecha = date("d/m/Y", mktime(0, 0, 0, date("m"), date("d") - 2, date("Y")));

for ($i=0; $i < $numfilas; $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i", $i);
	$tpl->assign("next", $i < $numfilas - 1 ? $i + 1 : 0);
	$tpl->assign("fecha", $fecha);
}

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
$db->desconectar();
die;
?>