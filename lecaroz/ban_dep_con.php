<?php
// LISTADO DE GASTOS DE CAJA
// Tabla 'catalogo_gastos_caja'
// Menu 'Balance->Catálogos Especiales'

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

// --------------------------------- Descripcion de errores --------------------------------------------------
$descripcion_error[1] = "No hay registros";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dep_con.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
if (isset($_SESSION['menu']))
	$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

// -------------------------------- Tipo de listado -------------------------------------------------------
if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("tipo_listado");
	
	$mes = date("n");
	
	$tpl->assign("$mes","selected");
	
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

$fecha1 = date("d/m/Y",mktime(0,0,0,$_GET['mes'],1,$_GET['anio']));
$fecha2 = date("d/m/Y",mktime(0,0,0,$_GET['mes']+1,0,$_GET['anio']));

if (!in_array($_SESSION['iduser'], $users))
	$result = ejecutar_script("SELECT * FROM estado_cuenta WHERE num_cia=$_GET[num_cia] AND fecha>='$fecha1' AND fecha<='$fecha2' AND tipo_mov='FALSE' ORDER BY fecha ASC",$dsn);
else
	$result = ejecutar_script("SELECT * FROM estado_cuenta WHERE num_cia=$_GET[num_cia] AND num_cia BETWEEN 900 AND 950 AND fecha BETWEEN '$fecha1' AND '$fecha2' AND tipo_mov='FALSE' ORDER BY fecha ASC",$dsn);

if (!$result) {
	header("location: ./ban_dep_con.php?codigo_error=1");
	die;
}

$tpl->newBlock("listado");
$cia = ejecutar_script("SELECT nombre,clabe_cuenta FROM catalogo_companias WHERE num_cia=$_GET[num_cia]",$dsn);
$tpl->assign("num_cia",$_GET['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre']);
$tpl->assign("cuenta",$cia[0]['clabe_cuenta']);
$tpl->assign("dia",date("d",mktime(0,0,0,$_GET['mes']+1,0,date("Y"))));
switch ($_GET['mes']) {
	case 1: $tpl->assign("mes","Enero"); break;
	case 2: $tpl->assign("mes","Febrero"); break;
	case 3: $tpl->assign("mes","Marzo"); break;
	case 4: $tpl->assign("mes","Abril"); break;
	case 5: $tpl->assign("mes","Mayo"); break;
	case 6: $tpl->assign("mes","Junio"); break;
	case 7: $tpl->assign("mes","Julio"); break;
	case 8: $tpl->assign("mes","Agosto"); break;
	case 9: $tpl->assign("mes","Septiembre"); break;
	case 10: $tpl->assign("mes","Octubre"); break;
	case 11: $tpl->assign("mes","Noviembre"); break;
	case 12: $tpl->assign("mes","Diciembre"); break;
}
$tpl->assign("anio",date("Y"));

$total = 0;

for ($i=0; $i<count($result); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("id",$result[$i]['id']);
	$tpl->assign("fecha_mov",$result[$i]['fecha']);
	$tpl->assign("concepto",$result[$i]['concepto']);
	$tpl->assign("importe",number_format($result[$i]['importe'],2,".",","));
	$tpl->assign("cod_mov",$result[$i]['cod_mov']);
	$cod = ejecutar_script("SELECT descripcion FROM catalogo_mov_bancos WHERE cod_mov=".$result[$i]['cod_mov'],$dsn);
	if ($cod)
		$tpl->assign("nombre",$cod[0]['descripcion']);
	if ($result[$i]['fecha_con'])
		$tpl->assign("fecha_con",$result[$i]['fecha_con']);
	else
		$tpl->assign("fecha_con","&nbsp;");
	$total += $result[$i]['importe'];
}
$tpl->gotoBlock("listado");
$tpl->assign("total",number_format($total,2,".",","));
$tpl->printToScreen();
?>