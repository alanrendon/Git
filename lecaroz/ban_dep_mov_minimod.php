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

// --------------------------------- Validar acceso de usuario a la pantalla ---------------------------------
//$session->validar_pantalla(IDSCREEN);

// --------------------------------- Obtener información de la pantalla --------------------------------------
//$session->info_pantalla();

$descripcion_error[1] = "La Compañía no existe en la Base de Datos";

// --------------------------------- Generar pantalla --------------------------------------------------------
// Hacer un nuevo objeto TemplatePower
$tpl = new TemplatePower( "./plantillas/header.tpl" );

// Incluir el cuerpo del documento
$tpl->assignInclude("body","./plantillas/ban/ban_dep_mov_minimod.tpl");
$tpl->prepare();

// -------------------------------- Tipo de listado -------------------------------------------------------
if (isset($_POST['id'])) {
	// Actualizar movimiento
	ejecutar_script("UPDATE mov_banorte SET cod_mov=$_POST[cod_mov],concepto='".strtoupper($_POST['concepto'])."',fecha='$_POST[fecha]',fecha_con='$_POST[fecha_con]',imprimir = 'TRUE' WHERE id=$_POST[id]",$dsn);
	// Insertar movimiento en estado_cuenta
	ejecutar_script("INSERT INTO estado_cuenta (num_cia,fecha,fecha_con,tipo_mov,importe,saldo_ini,saldo_fin,cod_mov,concepto,cuenta) SELECT num_cia,fecha,fecha_con,tipo_mov,importe,0,0,cod_mov,concepto,1 FROM mov_banorte WHERE id=$_POST[id]",$dsn);
	// Actualizar saldo
	$importe = ejecutar_script("SELECT num_cia,importe FROM mov_banorte WHERE id = $_POST[id]",$dsn);
	ejecutar_script("UPDATE saldos SET saldo_bancos = saldo_bancos + ".$importe[0]['importe'].",saldo_libros = saldo_libros + ".$importe[0]['importe']." WHERE num_cia = ".$importe[0]['num_cia']." AND cuenta = 1",$dsn);
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die();
}

$dep = ejecutar_script("SELECT * FROM mov_banorte WHERE id = $_GET[id]",$dsn);

$tpl->newBlock("modificar");
$tpl->assign("id",$_GET['id']);
$tpl->assign("num_cia",$dep[0]['num_cia']);
$tpl->assign("cod_banco",$dep[0]['cod_banco']);
$tpl->assign("fecha",$dep[0]['fecha']);
$tpl->assign("fecha_con",$dep[0]['fecha']);
$tpl->assign("concepto",$dep[0]['concepto']);
$tpl->assign("importe",$dep[0]['importe']);
$tpl->assign("fimporte",number_format($dep[0]['importe'],2,".",","));

$cod_mov = ejecutar_script("SELECT DISTINCT ON (cod_mov) cod_mov,descripcion FROM catalogo_mov_bancos WHERE tipo_mov = 'FALSE' ORDER BY cod_mov ASC",$dsn);
for ($i=0; $i<count($cod_mov); $i++) {
	$tpl->newBlock("cod_mov");
	$tpl->assign("cod_mov",$cod_mov[$i]['cod_mov']);
	$tpl->assign("descripcion",$cod_mov[$i]['descripcion']);
	if ($cod_mov[$i]['cod_mov'] == 16 && ($dep[0]['num_cia'] > 300 && $dep[0]['num_cia'] < 600 || $dep[0]['num_cia'] == 702 || $dep[0]['num_cia'] == 703 || $dep[0]['num_cia'] == 704))
		$tpl->assign("selected","selected");
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
?>