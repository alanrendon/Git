<?php
// CONSULTA DE CUENTAS POR COMPAÑÍA
// Tablas ''
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

// --------------------------------- INCLUDES ----------------------------------------------------------------
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Validar usuario ---------------------------------------------------------
$session = new sessionclass($dsn);

$users = array(28, 29, 30, 31);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

$descripcion_error[1] = "No hay resultados";

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_cue_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['listado'])) {
	$tpl->newBlock("datos");

	// Si viene de una página que genero error
	if (isset($_GET['codigo_error'])) {
		$tpl->newBlock("error");
		$tpl->newBlock("message");
		$tpl->assign("message",$descripcion_error[$_GET['codigo_error']]);
	}

	if (isset($_GET['mensaje'])) {
		$tpl->newBlock("message");
		$tpl->assign("message", $_GET['mensaje']);
	}

	$tpl->printToScreen();
	die;
}

$clabe_cuenta = $_GET['cuenta'] == 1 ? "clabe_cuenta" : "clabe_cuenta2";

$sql = "SELECT num_cia, nombre, $clabe_cuenta FROM catalogo_companias AS cc WHERE num_cia NOT IN (999)";
if ($_GET['num_cia'] > 0)
	$sql .= " AND num_cia = $_GET[num_cia]";
if (/*in_array($_SESSION['iduser'], $users)*/$_SESSION['tipo_usuario'] == 2)
	$sql .= " AND num_cia IN (SELECT num_cia FROM catalogo_companias WHERE num_cia BETWEEN 900 AND 998)";
// $sql .= " AND (SELECT id FROM estado_cuenta WHERE num_cia = cc.num_cia AND cuenta = $_GET[cuenta] LIMIT 1) IS NOT NULL";
$sql .= " ORDER BY num_cia";
$cia = ejecutar_script($sql,$dsn);

if (!$cia) {
	header("location: ./ban_cue_con.php?codigo_error=1");
	die;
}

$numfilas_x_hoja = 57;
$numfilas = $numfilas_x_hoja;
for ($i=0; $i<count($cia); $i++) {
	if ($numfilas == $numfilas_x_hoja) {
		$tpl->newBlock("listado");
		$tpl->assign("dia",date("j"));
		$tpl->assign("mes",mes_escrito(date("n")));
		$tpl->assign("anio",date("Y"));

		$numfilas = 0;
	}
	$tpl->newBlock("fila");
	$tpl->assign("num_cia",$cia[$i]['num_cia']);
	$tpl->assign("cuenta",$cia[$i][$clabe_cuenta] != "" ? $cia[$i][$clabe_cuenta] : "&nbsp;");
	$tpl->assign("nombre",$cia[$i]['nombre']);

	$numfilas++;
}

$tpl->printToScreen();
?>
