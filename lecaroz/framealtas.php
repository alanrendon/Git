<?php
include 'DB.php';
include './includes/class.session.inc.php';
include './includes/class.TemplatePower.inc.php';
include './includes/dbstatus.php';

// Validar usuario
$session = new sessionclass();
$session->validar_sesion();

// Descripcion de errores
$descripcion_error[1] = "No se puede conectar a la Base de Datos";
$descripcion_error[2] = "Usted no tiene acceso a esta pantalla";
$descripcion_error[3] = "N&uacute;mero de compa&ntilde;&iacute;a no existe en la Base de Datos";
$descripcion_error[4] = "N&uacute;mero de compa&ntilde;&iacute;a ya existe en la Base de Datos";
$descripcion_error[5] = "N&uacute;mero de expendio no existe en la Base de Datos";
$descripcion_error[6] = "N&uacute;mero de expendio ya existe en la Base de Datos";
$descripcion_error[7] = "C&oacute;digo de puesto ya existe en la Base de Datos";
$descripcion_error[8] = "C&oacute;digo de horario no existe en la Base de Datos";
$descripcion_error[9] = "C&oacute;digo de horario ya existe en la Base de Datos";
$descripcion_error[10] = "C&oacute;digo de turno ya existe en la Base de Datos";
$descripcion_error[11] = "N&uacute;mero de proveedor no existe en la Base de Datos";
$descripcion_error[12] = "N&uacute;mero de proveedor ya existe en la Base de Datos";
$descripcion_error[13] = "El empleado no esta registrado en la Base de Datos";
$descripcion_error[14] = "El empleado ya esta registrado en la Base de Datos";

// Verificar si el usuario tiene acceso a la pantalla
$db = DB::connect($dsn);
if (DB::isError($db))
	die($db->getMessage());
	
// Solicitar permisos de usuario para la pantalla seleccionada
$sql = "SELECT permiso FROM permisos WHERE id_user = $_SESSION[id_user] AND authlevel = $_SESSION[authlevel] AND idscreen = $_GET[id_screen]";
$result = $db->query($sql);
if (DB::isError($result))
	die($result->getMessage());

$ver_pantalla = $result->fetchRow(DB_FETCHMODE_OBJECT);
$db->disconnect();

// Si usuario tiene permisos, mostrar pantalla, en caso contrario, denegar el acceso
if ($result->numRows() > 0 && $ver_pantalla->permiso == TRUE) {
	$db = DB::connect($dsn);
	if (DB::isError($db))
		die($db->getMessage());
	
	$sql = "SELECT * FROM screens WHERE idscreen = $_GET[id_screen]";
	$result = $db->query($sql);
	$screen = $result->fetchRow(DB_FETCHMODE_OBJECT);
	$sql = "SELECT * FROM menus WHERE idmenu = $screen->idmenu";
	$result = $db->query($sql);
	$menu = $result->fetchRow(DB_FETCHMODE_OBJECT);
	
	// Determinar el indice para el catalogo
	$result = $db->query("SELECT * FROM $screentabla");
		
	$db->disconnect();
	
	if ($result->numRows() == 0)
		return 1;
	
	$i = 1;
	
	while ($row = $result->fetchRow(DB_FETCHMODE_ORDERED)) {
		if ($row[0] != $i)
			return $i;
		$i++;
	}

	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower( "./plantillas/mainframe.tpl" );
	
	// Incluir el cuerpo del documento
	$tpl->assignInclude("body","./plantillas/$menu->path/$screen->plantilla");
	$tpl->prepare();
	
	// Seleccionar script para menu
	$tpl->newBlock("menu");
	$tpl->assign("menucnt","cnt_$_SESSION[menu].js");
	
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
}
else {
	$db = DB::connect($dsn);
	if (DB::isError($db))
		die($db->getMessage());
	
	// Pantalla de error de acceso
	$sql = "SELECT * FROM screens WHERE idscreen = $_GET[id_screen]";
	$result = $db->query($sql);
	$screen = $result->fetchRow(DB_FETCHMODE_OBJECT);
	
	// Insertar registro de acceso de usuario en la tabla 'registro'
	$sql = "insert into registro (id_user,fecha,ip,navegador,operacion) values($_SESSION[id_user],CURRENT_TIMESTAMP,'$_SERVER[REMOTE_ADDR]','$_SERVER[HTTP_USER_AGENT]','Acceso denegado a la pantalla $screen->idscreen:$screen->descripcion')";
	$result = $db->query($sql);

	if(DB::isError($result))//manda un error por si algo no salio bien en la conexion
	{
		echo $result->getMessage();
		exit;
	}
	$db->disconnect();
	
	// Hacer un nuevo objeto TemplatePower
	$tpl = new TemplatePower("./plantillas/mainframe.tpl");
	
	// Incluir el cuerpo del documento
	$tpl->assignInclude("body","./plantillas/access_denied.tpl");
	$tpl->prepare();
	
	// Seleccionar script para menu
	$tpl->newBlock("menu");
	$tpl->assign("menucnt","cnt_$_SESSION[menu].js");
	
	// Imprimir el resultado
	$tpl->printToScreen();
}
?>