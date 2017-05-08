<?php
// MODIFICACIÓN RÁPIDA DE PRECIOS DE VENTA PARA ROSTICERIAS
// Tablas 'precios_guerra'
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
$tpl->assignInclude("body","./plantillas/ros/ros_prv_minimod.tpl");
$tpl->prepare();

if (isset($_GET['tabla'])) {
	// Almacenar valores temporalmente
	for ($i=0; $i<10; $i++) {
		if ($_POST['codmp'.$i] > 0 && $_POST['precio_venta'.$i] > 0) {
			if (existe_registro("precios_guerra",array("num_cia","codmp"),array($_SESSION['num_cia'],$_POST['codmp'.$i]),$dsn))
				ejecutar_script("UPDATE precios_guerra SET precio_venta=".$_POST['precio_venta'.$i]." WHERE num_cia=$_SESSION[num_cia] AND codmp=".$_POST['codmp'.$i],$dsn);
			else
				ejecutar_script("INSERT INTO precios_guerra (num_cia,codmp,num_proveedor,precio_compra,precio_venta) VALUES ($_SESSION[num_cia],".$_POST['codmp'.$i].",13,0,".$_POST['precio_venta'.$i].")",$dsn);
		}
	}
	
	$tpl->newBlock("cerrar");
	$tpl->printToScreen();
	die;
}

// Generar pantalla de captura
$cia = ejecutar_script("SELECT num_cia,nombre_corto FROM catalogo_companias WHERE num_cia = $_SESSION[num_cia]",$dsn);
$mp = ejecutar_script("SELECT codmp,nombre FROM catalogo_mat_primas WHERE tipo_cia='FALSE'",$dsn);

$tpl->newBlock("modificar");
$tpl->assign("num_cia",$_SESSION['num_cia']);
$tpl->assign("nombre_cia",$cia[0]['nombre_corto']);
$tpl->assign("tabla","precios_guerra");

// Generar listado de materia prima para la compañía especificada por $_SESSION[num_cia]
for ($i=0; $i<count($mp); $i++) {
	$precio = ejecutar_script("SELECT precio_venta FROM precios_guerra WHERE num_cia=$_SESSION[num_cia] AND codmp=".$mp[$i]['codmp'],$dsn);
	$tpl->newBlock("nombre_mp");
	$tpl->assign("codmp",$mp[$i]['codmp']);
	$tpl->assign("nombre_mp",$mp[$i]['nombre']);
	if ($precio)
		$tpl->assign("precio_actual",number_format($precio[0]['precio_venta'],2,".",""));
	else
		$tpl->assign("precio_actual","\"SIN PRECIO\"");
}
// Caso especial para pollos adobados
$precio = ejecutar_script("SELECT precio_venta FROM precios_guerra WHERE num_cia=$_SESSION[num_cia] AND codmp=1601",$dsn);
$tpl->newBlock("nombre_mp");
$tpl->assign("codmp",1601);
$tpl->assign("nombre_mp","POLLOS ADOBADOS");
if ($precio)
	$tpl->assign("precio_actual",number_format($precio[0]['precio_venta'],2,".",""));
else
	$tpl->assign("precio_actual","\"SIN PRECIO\"");

// Generar filas
for ($i=0; $i<10; $i++) {
	$tpl->newBlock("fila");
	
	$tpl->assign("i",$i);
	if ($i < 10-1)
		$tpl->assign("next",$i+1);
	else
		$tpl->assign("next",0);
	if ($i > 0)
		$tpl->assign("back",$i-1);
	else
		$tpl->assign("back",10-1);
}

// Si viene de una página que genero error
if (isset($_GET['codigo_error'])) {
	$tpl->newBlock("error");
	$tpl->newBlock("message");
	$tpl->assign( "message","El empleado no. $_GET[codigo_error] ya tiene un prestamo");	
}

if (isset($_GET['mensaje'])) {
	$tpl->newBlock("message");
	$tpl->assign("message", $_GET['mensaje']);
}

$tpl->printToScreen();
?>