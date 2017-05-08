<?php
// BORRAR RAPIDO DE UN PRODUCTO EN CONTROL DE PRODUCCION
// Tablas 'control_produccion'
// Menu 'No definido'

//define ('IDSCREEN',2); // ID de pantalla

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

// --------------------------------- Descripcion de errores --------------------------------------------------
//$descripcion_error[]

// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/pan/pan_bloc_minidel.tpl");
$tpl->prepare();
$var=0;
if (isset($_POST['id'])) {
	$blocs=ejecutar_script("SELECT * from bloc where id = ".$_POST['id'],$dsn); //toma los datos del bloc
	//inserta el bloc en la tabla de blocs_borrados
	if($blocs[0]['let_folio']=="") $var=" ";
	else $var=$blocs[0]['let_folio'];
	$sql="INSERT INTO blocs_borrados (num_cia,folio_inicio,folio_final,let_folio) VALUES (".$blocs[0]['idcia'].", ".$blocs[0]['folio_inicio'].", ".$blocs[0]['folio_final'].", '".$var."')";
	ejecutar_script($sql,$dsn);
	//borra el bloc de la tabla de bloc
	ejecutar_script("DELETE FROM bloc WHERE id=".$_POST['id'],$dsn);
	if (isset($_POST['det']))
		$tpl->newBlock('cerrar2');
	else
		$tpl->newBlock("cerrar1");
	$tpl->printToScreen();
	die;
}

// Generar pantalla de captura
$tpl->newBlock("question");
$tpl->assign("id",$_GET['id']);
if (isset($_GET['det']))
$tpl->assign('det', '<input name="det" value="1" type="hidden">');

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
//	$tpl->assign( "message","El empleado no. $_GET[codigo_error] ya tiene un prestamo");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>