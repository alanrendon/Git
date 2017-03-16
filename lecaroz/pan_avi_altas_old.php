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
$descripcion_error[1] = "N&uacute;mero de compa&ntilde;&iacute;a no existe en la Base de Datos";


// --------------------------------- Validar usuario ---------------------------------
$session = new sessionclass($dsn);

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener informacion de la pantalla ---------------------------------
$session->info_pantalla();

// --------------------------------- Generar pantalla ---------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/$session->ruta/$session->plantilla");
$tpl->prepare();

// Seleccionar script para menu
$tpl->newBlock("menu");
$tpl->assign("menucnt","$_SESSION[menu]_cnt.js");
$tpl->gotoBlock("_ROOT");

if (!isset($_GET['tabla'])) {
	// Seleccionar tabla
	$tpl->assign("tabla",$session->tabla);
	
	$db = DB::connect($dsn);
	if (DB::isError($db)) {
		echo "Error al intentar acceder a la Base de Datos. Avisar al administrador. pan_avi_altas.<br>";
		die($db->getMessage());
	}
	
	// Generar listado de materias primas
	$sql = "SELECT * FROM catalogo_mat_primas WHERE controlada = 'TRUE' ORDER BY codmp ASC";
	$result = $db->query($sql);
	if (DB::isError($result)) {
		$db->disconnect();
		echo "Error en script SQL: $sql<br>Avisar al administrador. pan_avi_altas.<br>";
		die($result->getMessage());
	}
	while ($row = $result->fetchRow(DB_FETCHMODE_OBJECT)) {
		$tpl->newBlock("mp");
		$tpl->assign("codmp",$row->codmp);
		$tpl->assign("nombre",$row->nombre);
	}
	$db->disconnect();
	
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

// Insertar registro

if (!existe_registro("catalogo_companias",array("num_cia"),array($_POST['num_cia']),$dsn)) {
	header("location: ./pan_avi_altas.php?codigo_error=1");
	die();
}

//$db = new DBclass($dsn,$_GET['tabla'],$_POST);
for ($i=0; $i<7; $i++) {
	if (/*$db->datos['num_cia'.$i] > 0 && $db->datos['cod_turno'.$i] > 0*/isset($_POST['cod_turno'.$i])) {
		/*$db->generar_script_insert($i);
		$db->ejecutar_script();*/
		$sql = "INSERT INTO control_avio (num_cia,cod_turno,codmp,num_orden) VALUES ($_POST[num_cia],".$_POST['cod_turno'.$i].",$_POST[codmp],".(($_POST['num_orden'] != "")?$_POST['num_orden']:"NULL").")";
		ejecutar_script($sql,$dsn);
	}
}

header("location: ./pan_avi_altas.php");
?>