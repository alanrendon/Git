<?php
// CONTROL DE AVIO
// Tabla 'control_avio'
// Menu 'Panaderias->Producción'

define ('IDSCREEN',1213); // ID de pantalla

// --------------------------------- INCLUDES ---------------------------------
include 'DB.php';
include './includes/class.db3.inc.php';
include './includes/class.session2.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// --------------------------------- Descripcion de errores ---------------------------------
$descripcion_error[1] = "No hay resultados";

// --------------------------------- Validar usuario ---------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener informacion de la pantalla ---------------------------------
$session->info_pantalla();

if (isset($_POST['num_cia'])) {
	// Borrar todos el control de avio para la compañía
	$sql = "DELETE FROM control_avio WHERE num_cia = $_POST[num_cia]";
	ejecutar_script($sql,$dsn);
	
	// Insertar los nuevos registros para control de avio
	for ($i=0; $i<$_POST['numfilas']; $i++) {
		if (isset($_POST['fd'.$i]))
			ejecutar_script("INSERT INTO control_avio (num_cia,cod_turno,codmp,num_orden) VALUES ($_POST[num_cia],1,".$_POST['codmp'.$i].",".(($_POST['num_orden'.$i] > 0)?$_POST['num_orden'.$i]:"NULL").")",$dsn);
		if (isset($_POST['fn'.$i]))
			ejecutar_script("INSERT INTO control_avio (num_cia,cod_turno,codmp,num_orden) VALUES ($_POST[num_cia],2,".$_POST['codmp'.$i].",".(($_POST['num_orden'.$i] > 0)?$_POST['num_orden'.$i]:"NULL").")",$dsn);
		if (isset($_POST['bd'.$i]))
			ejecutar_script("INSERT INTO control_avio (num_cia,cod_turno,codmp,num_orden) VALUES ($_POST[num_cia],3,".$_POST['codmp'.$i].",".(($_POST['num_orden'.$i] > 0)?$_POST['num_orden'.$i]:"NULL").")",$dsn);
		if (isset($_POST['rep'.$i]))
			ejecutar_script("INSERT INTO control_avio (num_cia,cod_turno,codmp,num_orden) VALUES ($_POST[num_cia],4,".$_POST['codmp'.$i].",".(($_POST['num_orden'.$i] > 0)?$_POST['num_orden'.$i]:"NULL").")",$dsn);
		if (isset($_POST['pic'.$i]))
			ejecutar_script("INSERT INTO control_avio (num_cia,cod_turno,codmp,num_orden) VALUES ($_POST[num_cia],8,".$_POST['codmp'.$i].",".(($_POST['num_orden'.$i] > 0)?$_POST['num_orden'.$i]:"NULL").")",$dsn);
		if (isset($_POST['gel'.$i]))
			ejecutar_script("INSERT INTO control_avio (num_cia,cod_turno,codmp,num_orden) VALUES ($_POST[num_cia],9,".$_POST['codmp'.$i].",".(($_POST['num_orden'.$i] > 0)?$_POST['num_orden'.$i]:"NULL").")",$dsn);
		if (isset($_POST['des'.$i]))
			ejecutar_script("INSERT INTO control_avio (num_cia,cod_turno,codmp,num_orden) VALUES ($_POST[num_cia],10,".$_POST['codmp'.$i].",".(($_POST['num_orden'.$i] > 0)?$_POST['num_orden'.$i]:"NULL").")",$dsn);
	}
	
	if (isset($_POST['cap'])) {
		header("location: ./pan_avi_cap.php?num_cia=$_POST[num_cia]");
		die;
	}
	
	header("location: ./pan_avi_altas.php");
	die;
}

// --------------------------------- Generar pantalla ---------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_avi_altas.tpl");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['num_cia'])) {
	$tpl->newBlock("cia");
	
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
	
	// Imprimir el resultado
	$tpl->printToScreen();
	die();
}

$sql = "SELECT codmp,nombre FROM inventario_virtual JOIN catalogo_mat_primas USING (codmp) WHERE controlada='TRUE' AND num_cia=$_GET[num_cia] ORDER BY codmp";
$mp = ejecutar_script($sql,$dsn);

if (!$mp) {
	header("location: ./pan_avi_altas.php?codigo_error=1");
	die;
}

$tpl->newBlock("altas");
$tpl->assign("num_cia",$_GET['num_cia']);
$nombre_cia = ejecutar_script("SELECT nombre_corto FROM catalogo_companias WHERE num_cia = $_GET[num_cia]",$dsn);
$tpl->assign("nombre_cia",$nombre_cia[0]['nombre_corto']);
$tpl->assign("numfilas",count($mp));
if (isset($_GET['cap']))
	$tpl->assign("cap","<input name='cap' type='hidden' value='1'>");

for ($i=0; $i<count($mp); $i++) {
	$tpl->newBlock("fila");
	$tpl->assign("i",$i);
	$tpl->assign("codmp",$mp[$i]['codmp']);
	$tpl->assign("nombre",$mp[$i]['nombre']);
	
	if ($i < count($mp)-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next","0");
	
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",count($mp)-1);
	
	$sql = "SELECT * FROM control_avio WHERE num_cia = $_GET[num_cia] AND codmp = ".$mp[$i]['codmp']." ORDER BY codmp";
	$control = ejecutar_script($sql,$dsn);
	if ($control) {
		for ($j=0; $j<count($control); $j++) {
			switch ((int)$control[$j]['cod_turno']) {
				case 1: $tpl->assign("fd_checked","checked"); break;
				case 2: $tpl->assign("fn_checked","checked"); break;
				case 3: $tpl->assign("bd_checked","checked"); break;
				case 4: $tpl->assign("rep_checked","checked"); break;
				case 8: $tpl->assign("pic_checked","checked"); break;
				case 9: $tpl->assign("gel_checked","checked"); break;
				case 10: $tpl->assign("des_checked","checked"); break;
			}
			$num_orden = $control[$j]['num_orden'];
		}
		$tpl->assign("num_orden",$num_orden);
	}
}

$tpl->printToScreen();

?>